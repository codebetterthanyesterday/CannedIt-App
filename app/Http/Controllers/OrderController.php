<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    /**
     * Display user's orders
     */
    public function index(Request $request)
    {
        $query = Order::with('orderItems.product')
            ->where('user_id', Auth::id());

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('orderItems', function($q) use ($search) {
                      $q->where('product_name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date from filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang Anda kosong');
        }

        // Verify stock availability
        foreach ($cartItems as $item) {
            if ($item->product->stock_quantity < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Stok {$item->product->name} tidak mencukupi");
            }
        }

        // Get user's saved addresses
        $addresses = Auth::check() ? Auth::user()->addresses()->orderBy('is_default', 'desc')->get() : collect();

        // Calculate total weight (in grams)
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $productWeight = $item->product->weight ?? 500; // default 500gr if not set
            $totalWeight += $productWeight * $item->quantity;
        }

        $subtotal = $cartItems->sum('total');
        $taxRate = 0.11; // 11% PPN
        $taxAmount = $subtotal * $taxRate;
        $shippingAmount = 0; // Will be calculated via RajaOngkir
        
        // Apply discount if exists
        $discountAmount = 0;
        if (Session::has('discount_percentage')) {
            $discountPercentage = Session::get('discount_percentage');
            $discountAmount = $subtotal * ($discountPercentage / 100);
        }

        $total = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        return view('orders.checkout', compact(
            'cartItems', 'subtotal', 'taxAmount', 'shippingAmount', 
            'discountAmount', 'total', 'totalWeight', 'addresses'
        ));
    }

    /**
     * Process checkout and create order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_province_id' => 'nullable|integer',
            'shipping_city_id' => 'nullable|integer',
            'shipping_courier' => 'nullable|string|max:50',
            'shipping_service' => 'nullable|string|max:100',
            'shipping_etd' => 'nullable|string|max:50',
            'shipping_weight' => 'nullable|integer',
            'shipping_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:xendit,bank_transfer,credit_card,ewallet,cod',
            'notes' => 'nullable|string|max:500'
        ]);

        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang Anda kosong');
        }

        DB::beginTransaction();
        
        try {
            // Calculate totals
            $subtotal = $cartItems->sum('total');
            $taxRate = 0.11;
            $taxAmount = $subtotal * $taxRate;
            $shippingAmount = $validated['shipping_amount']; // From RajaOngkir
            
            $discountAmount = 0;
            if (Session::has('discount_percentage')) {
                $discountPercentage = Session::get('discount_percentage');
                $discountAmount = $subtotal * ($discountPercentage / 100);
            }

            $total = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $total,
                'shipping_name' => $validated['shipping_name'],
                'shipping_email' => $validated['shipping_email'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'shipping_province_id' => $validated['shipping_province_id'] ?? null,
                'shipping_province_name' => $validated['shipping_state'], // Already contains province name
                'shipping_city_id' => $validated['shipping_city_id'] ?? null,
                'shipping_city_name' => $validated['shipping_city'], // Already contains city name
                'shipping_courier' => $validated['shipping_courier'] ?? null,
                'shipping_service' => $validated['shipping_service'] ?? null,
                'shipping_etd' => $validated['shipping_etd'] ?? null,
                'shipping_weight' => $validated['shipping_weight'] ?? 0,
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                
                // Verify stock again
                if ($product->stock_quantity < $cartItem->quantity) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi");
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                ]);

                // Update product stock
                $product->decrement('stock_quantity', $cartItem->quantity);
                
                // Update stock status if needed
                if ($product->stock_quantity <= 0) {
                    $product->update(['status' => 'out_of_stock']);
                }
            }

            // Clear cart
            $this->clearCart();
            
            // Clear discount session
            Session::forget(['discount_code', 'discount_percentage']);

            DB::commit();

            // If payment method is not COD, create Xendit invoice and redirect to payment page
            if ($order->payment_method !== 'cod') {
                $result = $this->xenditService->createInvoice($order);
                
                if ($result['success']) {
                    $order->update([
                        'xendit_invoice_id' => $result['invoice_id'],
                        'xendit_invoice_url' => $result['invoice_url'],
                        'xendit_expired_at' => $result['expiry_date'],
                    ]);
                    
                    // Redirect to Xendit payment page
                    return redirect($result['invoice_url']);
                } else {
                    // If Xendit fails, still show success page but with payment pending
                    return redirect()->route('orders.success', $order->id)
                        ->with('warning', 'Pesanan berhasil dibuat, namun terjadi kesalahan saat membuat invoice pembayaran. Silakan hubungi customer service.');
                }
            }

            // For COD, just show success page
            return redirect()->route('orders.success', $order->id)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show order success page
     */
    public function success($orderId)
    {
        $order = Order::with('orderItems.product')
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('orders.success', compact('order'));
    }

    /**
     * Show specific order details
     */
    public function show(Order $order)
    {
        // Verify ownership
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderItems.product.category', 'user');

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel order (only if pending)
     */
    public function cancel(Order $order)
    {
        // Verify ownership
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Pesanan tidak dapat dibatalkan');
        }

        DB::beginTransaction();
        
        try {
            // Restore product stock
            foreach ($order->orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                    
                    // Update status if was out of stock
                    if ($product->status === 'out_of_stock' && $product->stock_quantity > 0) {
                        $product->update(['status' => 'active']);
                    }
                }
            }

            // Update order status
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Pesanan berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat membatalkan pesanan');
        }
    }

    /**
     * Process payment confirmation
     */
    public function confirmPayment(Request $request, Order $order)
    {
        // Verify ownership
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'payment_reference' => 'required|string|max:255',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $paymentProof = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProof = $request->file('payment_proof')
                ->store('payment_proofs', 'public');
        }

        $order->update([
            'payment_reference' => $request->payment_reference,
            'payment_proof' => $paymentProof,
            'status' => 'processing' // Will be verified by admin
        ]);

        return redirect()->back()
            ->with('success', 'Konfirmasi pembayaran berhasil dikirim');
    }

    /**
     * Show review form for delivered order
     */
    public function review(Order $order)
    {
        // Verify ownership
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Only delivered orders can be reviewed
        if ($order->status !== 'delivered') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Hanya pesanan yang sudah diterima yang dapat diulas');
        }

        return view('orders.review', compact('order'));
    }

    /**
     * Store reviews for order items
     */
    public function storeReview(Request $request, Order $order)
    {
        // Verify ownership
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Only delivered orders can be reviewed
        if ($order->status !== 'delivered') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Hanya pesanan yang sudah diterima yang dapat diulas');
        }

        $validated = $request->validate([
            'reviews' => 'required|array',
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'required|string|min:10|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $reviewCount = 0;

            foreach ($validated['reviews'] as $reviewData) {
                // Check if already reviewed
                $existingReview = Review::where('user_id', Auth::id())
                    ->where('product_id', $reviewData['product_id'])
                    ->where('order_id', $order->id)
                    ->first();

                if (!$existingReview) {
                    Review::create([
                        'user_id' => Auth::id(),
                        'product_id' => $reviewData['product_id'],
                        'order_id' => $order->id,
                        'rating' => $reviewData['rating'],
                        'comment' => $reviewData['comment'],
                    ]);
                    $reviewCount++;
                }
            }

            DB::commit();

            if ($reviewCount > 0) {
                return redirect()->route('orders.show', $order)
                    ->with('success', "Terima kasih! {$reviewCount} ulasan berhasil dikirim");
            } else {
                return redirect()->route('orders.show', $order)
                    ->with('info', 'Semua produk sudah pernah diulas sebelumnya');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Review submission error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengirim ulasan. Silakan coba lagi');
        }
    }

    /**
     * Show tracking page for an order
     */
    public function tracking(Order $order)
    {
        // Verify user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        return view('orders.tracking', compact('order'));
    }

    /**
     * Get cart items for current user
     */
    private function getCartItems()
    {
        return Cart::with('product.category')
            ->where('user_id', Auth::id())
            ->get();
    }

    /**
     * Clear user's cart
     */
    private function clearCart()
    {
        Cart::where('user_id', Auth::id())->delete();
    }

    /**
     * Calculate shipping cost
     */
    private function calculateShipping($subtotal)
    {
        // Simple shipping calculation
        // Free shipping for orders above 500,000
        if ($subtotal >= 500000) {
            return 0;
        }

        // Flat rate shipping
        return 25000;
    }
}
