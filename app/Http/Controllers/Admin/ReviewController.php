<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product.category']);
        
        // Filter by rating
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }
        
        // Search by product or user
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('product', function($productQuery) use ($request) {
                    $productQuery->where('name', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('user', function($userQuery) use ($request) {
                    $userQuery->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }
        
        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'highest':
                $query->orderBy('rating', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc');
                break;
            default: // latest
                $query->latest();
                break;
        }
        
        $reviews = $query->paginate(20)->withQueryString();
        
        // Calculate statistics from all reviews (not just filtered)
        $statistics = [
            'total' => Review::count(),
            'rating_5' => Review::where('rating', 5)->count(),
            'rating_4' => Review::where('rating', 4)->count(),
            'rating_3' => Review::where('rating', 3)->count(),
            'rating_2_or_less' => Review::whereIn('rating', [1, 2])->count(),
        ];
        
        return view('admin.reviews.index', compact('reviews', 'statistics'));
    }

    public function destroy(Review $review)
    {
        $product = $review->product;
        $review->delete();
        
        // Update product rating
        $product->updateRating();

        return redirect()->back()
            ->with('success', 'Review berhasil dihapus!');
    }
}
