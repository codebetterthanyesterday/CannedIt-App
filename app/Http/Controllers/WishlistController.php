<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        $wishlists = Auth::user()->wishlists()
            ->with('product.category')
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * Add a product to wishlist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $user = Auth::user();
        
        // Check if already in wishlist
        $exists = $user->wishlists()
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Produk sudah ada di wishlist',
            ], 409);
        }

        $wishlist = $user->wishlists()->create([
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk ditambahkan ke wishlist',
            'wishlist_id' => $wishlist->id,
            'count' => $user->wishlists()->count(),
        ]);
    }

    /**
     * Remove a product from wishlist.
     */
    public function destroy($id)
    {
        try {
            $wishlist = Auth::user()->wishlists()->findOrFail($id);
            $wishlist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk dihapus dari wishlist',
                'count' => Auth::user()->wishlists()->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk dari wishlist: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if product is in wishlist.
     */
    public function check($productId)
    {
        $wishlist = Auth::user()->wishlists()
            ->where('product_id', $productId)
            ->first();

        return response()->json([
            'in_wishlist' => $wishlist ? true : false,
            'wishlist_id' => $wishlist ? $wishlist->id : null,
        ]);
    }

    /**
     * Get wishlist count.
     */
    public function count()
    {
        return response()->json([
            'count' => Auth::user()->wishlists()->count(),
        ]);
    }

    /**
     * Move all wishlist items to cart.
     */
    public function moveToCart()
    {
        $user = Auth::user();
        $wishlists = $user->wishlists()->with('product')->get();

        $movedCount = 0;
        $outOfStock = 0;
        
        foreach ($wishlists as $wishlist) {
            $product = $wishlist->product;
            
            // Skip if product out of stock
            if ($product->stock_quantity <= 0) {
                $outOfStock++;
                continue;
            }
            
            // Check if product already in cart
            $cartItem = $user->cart()->where('product_id', $product->id)->first();
            
            if ($cartItem) {
                // Update quantity if already in cart
                $newQuantity = $cartItem->quantity + 1;
                
                // Check if new quantity exceeds stock
                if ($newQuantity <= $product->stock_quantity) {
                    $cartItem->update([
                        'quantity' => $newQuantity,
                        'price' => $product->current_price,
                    ]);
                    $movedCount++;
                }
            } else {
                // Add new item to cart
                $user->cart()->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $product->current_price,
                ]);
                $movedCount++;
            }
            
            // Remove from wishlist
            $wishlist->delete();
        }

        $message = "$movedCount produk berhasil dipindahkan ke keranjang";
        
        if ($outOfStock > 0) {
            $message .= " ($outOfStock produk stok habis tidak dipindahkan)";
        }

        return redirect()->route('cart.index')
            ->with('success', $message);
    }
}
