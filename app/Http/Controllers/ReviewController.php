<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Display reviews for a product.
     */
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    /**
     * Store a new review.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'order_id' => ['nullable', 'exists:orders,id'],
        ]);

        $user = Auth::user();

        // Check if user already reviewed this product
        $existingReview = $user->reviews()
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan review untuk produk ini');
        }

        // Check if this is a verified purchase
        $isVerifiedPurchase = false;
        if (isset($validated['order_id'])) {
            $order = Order::where('id', $validated['order_id'])
                ->where('user_id', $user->id)
                ->whereIn('status', ['delivered', 'completed'])
                ->whereHas('items', function($q) use ($validated) {
                    $q->where('product_id', $validated['product_id']);
                })
                ->exists();
            
            $isVerifiedPurchase = $order;
        }

        $review = $user->reviews()->create([
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'] ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified_purchase' => $isVerifiedPurchase,
        ]);

        // Update product rating cache
        $this->updateProductRating($validated['product_id']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review berhasil ditambahkan',
                'review' => $review->load('user'),
            ]);
        }

        return back()->with('success', 'Terima kasih! Review Anda telah ditambahkan');
    }

    /**
     * Update a review.
     */
    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $review->update($validated);

        // Update product rating cache
        $this->updateProductRating($review->product_id);

        return back()->with('success', 'Review berhasil diperbarui');
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $productId = $review->product_id;
        $review->delete();

        // Update product rating cache
        $this->updateProductRating($productId);

        return back()->with('success', 'Review berhasil dihapus');
    }

    /**
     * Update product rating statistics.
     */
    private function updateProductRating($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $reviews = Review::where('product_id', $productId);
        $avgRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();

        // You could store these in the products table for better performance
        // For now, they will be calculated on the fly
    }
}
