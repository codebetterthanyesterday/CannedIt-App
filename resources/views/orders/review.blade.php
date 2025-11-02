@extends('layouts.app')

@section('title', 'Beri Ulasan - Pesanan #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                        <a href="{{ route('orders.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Pesanan</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                        <a href="{{ route('orders.show', $order) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">#{{ $order->order_number }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                        <span class="ml-4 text-sm font-medium text-gray-900">Beri Ulasan</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-primary-600 text-2xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Beri Ulasan</h1>
                <p class="text-sm text-gray-600 mt-1">Bagikan pengalaman Anda dengan produk dari pesanan #{{ $order->order_number }}</p>
            </div>
        </div>
    </div>

    <!-- Review Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('orders.review.store', $order) }}" method="POST" id="review-form">
            @csrf

            <div class="space-y-6">
                @foreach($order->orderItems as $index => $item)
                    <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                        <!-- Product Info -->
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="shrink-0 w-20 h-20">
                                @if($item->product && $item->product->first_image)
                                    <img class="w-20 h-20 rounded-lg object-cover border" 
                                         src="{{ asset($item->product->first_image) }}" 
                                         alt="{{ $item->product_name }}">
                                @else
                                    <div class="w-20 h-20 rounded-lg bg-gray-100 border flex items-center justify-center">
                                        <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                            </div>
                        </div>

                        <!-- Check if already reviewed -->
                        @php
                            $existingReview = $item->product ? $item->product->reviews()
                                ->where('user_id', auth()->id())
                                ->where('order_id', $order->id)
                                ->first() : null;
                        @endphp

                        @if($existingReview)
                            <!-- Already Reviewed -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-600 mt-0.5 mr-3"></i>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-green-900 mb-2">Produk ini sudah diulas</h4>
                                        <div class="flex items-center space-x-2 mb-2">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-sm {{ $i <= $existingReview->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-gray-600">{{ $existingReview->rating }}/5</span>
                                        </div>
                                        <p class="text-sm text-gray-700">{{ $existingReview->comment }}</p>
                                        @if($existingReview->created_at)
                                            <p class="text-xs text-gray-500 mt-2">Diulas pada {{ $existingReview->created_at->format('d F Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Review Form Fields -->
                            <input type="hidden" name="reviews[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                            
                            <!-- Rating Stars -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Rating <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center space-x-1 star-rating" data-index="{{ $index }}">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" class="star-btn focus:outline-none" data-rating="{{ $i }}">
                                                <i class="fas fa-star text-3xl text-gray-300 hover:text-yellow-400 transition-colors"></i>
                                            </button>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600 ml-2">
                                        <span class="rating-text" data-index="{{ $index }}">Pilih rating</span>
                                    </span>
                                </div>
                                <input type="hidden" name="reviews[{{ $index }}][rating]" id="rating-{{ $index }}" required>
                                @error("reviews.{$index}.rating")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Comment -->
                            <div class="mb-4">
                                <label for="comment-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ulasan Anda <span class="text-red-500">*</span>
                                </label>
                                <textarea id="comment-{{ $index }}" 
                                          name="reviews[{{ $index }}][comment]" 
                                          rows="4" 
                                          required
                                          placeholder="Ceritakan pengalaman Anda dengan produk ini..."
                                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                                <p class="mt-1 text-sm text-gray-500">Minimal 10 karakter</p>
                                @error("reviews.{$index}.comment")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('orders.show', $order) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Kirim Ulasan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle star rating
    document.querySelectorAll('.star-rating').forEach(container => {
        const index = container.dataset.index;
        const stars = container.querySelectorAll('.star-btn');
        const ratingInput = document.getElementById(`rating-${index}`);
        const ratingText = document.querySelector(`.rating-text[data-index="${index}"]`);
        
        stars.forEach(star => {
            star.addEventListener('click', function(e) {
                e.preventDefault();
                const rating = parseInt(this.dataset.rating);
                
                // Update input value
                ratingInput.value = rating;
                
                // Update stars visual
                stars.forEach((s, i) => {
                    const icon = s.querySelector('i');
                    if (i < rating) {
                        icon.classList.remove('text-gray-300');
                        icon.classList.add('text-yellow-400');
                    } else {
                        icon.classList.remove('text-yellow-400');
                        icon.classList.add('text-gray-300');
                    }
                });
                
                // Update text
                const ratingTexts = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Bagus', 'Sangat Bagus'];
                ratingText.textContent = ratingTexts[rating];
            });
            
            // Hover effect
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                stars.forEach((s, i) => {
                    const icon = s.querySelector('i');
                    if (i < rating) {
                        icon.classList.add('text-yellow-400');
                    }
                });
            });
            
            star.addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value) || 0;
                stars.forEach((s, i) => {
                    const icon = s.querySelector('i');
                    if (i >= currentRating) {
                        icon.classList.remove('text-yellow-400');
                        icon.classList.add('text-gray-300');
                    }
                });
            });
        });
    });
    
    // Form validation
    document.getElementById('review-form').addEventListener('submit', function(e) {
        let valid = true;
        const ratings = document.querySelectorAll('input[name*="[rating]"]');
        
        ratings.forEach(rating => {
            if (!rating.value) {
                valid = false;
                const container = rating.closest('.mb-4');
                if (container && !container.querySelector('.error-message')) {
                    const error = document.createElement('p');
                    error.className = 'mt-1 text-sm text-red-600 error-message';
                    error.textContent = 'Rating harus diisi';
                    container.appendChild(error);
                }
            }
        });
        
        if (!valid) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});
</script>

@endsection
