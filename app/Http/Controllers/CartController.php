<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function index()
    {
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum('total');
        $itemCount = $cartItems->sum('quantity');

        return view('cart.index', compact('cartItems', 'total', 'itemCount'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product is active and in stock
        if ($product->status !== 'active' || $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak tersedia atau stok tidak mencukupi'
            ], 400);
        }

        $userId = Auth::id();
        $sessionId = Session::getId();

        // Check if item already exists in cart
        $existingCartItem = Cart::where('product_id', $request->product_id)
            ->forUserOrSession($userId, $sessionId)
            ->first();

        if ($existingCartItem) {
            // Update quantity
            $newQuantity = $existingCartItem->quantity + $request->quantity;
            
            // Check stock limit
            if ($newQuantity > $product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuantitas melebihi stok yang tersedia'
                ], 400);
            }

            $existingCartItem->update([
                'quantity' => $newQuantity
            ]);
        } else {
            // Create new cart item
            Cart::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->current_price
            ]);
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, Cart $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Verify ownership
        if (!$this->verifyCartOwnership($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $product = $cartItem->product;

        // Check stock availability
        if ($request->quantity > $product->stock_quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Kuantitas melebihi stok yang tersedia'
            ], 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        $cartItems = $this->getCartItems();
        $total = $cartItems->sum('total');
        $itemCount = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui',
            'cart_total' => number_format($total, 0, ',', '.'),
            'cart_count' => $itemCount,
            'item_total' => number_format($cartItem->total, 0, ',', '.')
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(Cart $cartItem)
    {
        // Verify ownership
        if (!$this->verifyCartOwnership($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $cartItem->delete();

        $cartCount = $this->getCartCount();
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum('total');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang',
            'cart_count' => $cartCount,
            'cart_total' => number_format($total, 0, ',', '.')
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        Cart::forUserOrSession($userId, $sessionId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }

    /**
     * Get cart items count (for header display)
     */
    public function count()
    {
        $count = $this->getCartCount();
        return response()->json(['count' => $count]);
    }

    /**
     * Apply discount code
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'discount_code' => 'required|string'
        ]);

        // This is a simple implementation
        // In a real application, you would have a discounts table
        $discountCodes = [
            'WELCOME10' => 10,
            'SAVE15' => 15,
            'NEWCUSTOMER' => 20
        ];

        $code = strtoupper($request->discount_code);
        
        if (!isset($discountCodes[$code])) {
            return response()->json([
                'success' => false,
                'message' => 'Kode diskon tidak valid'
            ]);
        }

        $discountPercentage = $discountCodes[$code];
        
        // Store discount in session
        Session::put('discount_code', $code);
        Session::put('discount_percentage', $discountPercentage);

        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum('total');
        $discountAmount = $subtotal * ($discountPercentage / 100);
        $total = $subtotal - $discountAmount;

        return response()->json([
            'success' => true,
            'message' => "Kode diskon berhasil diterapkan! Diskon {$discountPercentage}%",
            'discount_percentage' => $discountPercentage,
            'discount_amount' => number_format($discountAmount, 0, ',', '.'),
            'total' => number_format($total, 0, ',', '.')
        ]);
    }

    /**
     * Remove discount code
     */
    public function removeDiscount()
    {
        Session::forget(['discount_code', 'discount_percentage']);

        $cartItems = $this->getCartItems();
        $total = $cartItems->sum('total');

        return response()->json([
            'success' => true,
            'message' => 'Kode diskon berhasil dihapus',
            'total' => number_format($total, 0, ',', '.')
        ]);
    }

    /**
     * Get cart items for current user/session
     */
    private function getCartItems()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        return Cart::with('product.category')
            ->forUserOrSession($userId, $sessionId)
            ->get();
    }

    /**
     * Get cart items count
     */
    private function getCartCount()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        return Cart::forUserOrSession($userId, $sessionId)
            ->sum('quantity');
    }

    /**
     * Verify cart item ownership
     */
    private function verifyCartOwnership(Cart $cartItem)
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        if ($userId) {
            return $cartItem->user_id == $userId;
        } else {
            return $cartItem->session_id == $sessionId;
        }
    }
}
