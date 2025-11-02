@extends('layouts.app')

@section('title', 'Wishlist Saya - CannedIt')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Wishlist Saya</h1>
            <p class="text-gray-600 mt-1">Produk favorit yang ingin Anda beli</p>
        </div>
        @if($wishlists->isNotEmpty())
        <form action="{{ route('wishlist.move-to-cart') }}" method="POST">
            @csrf
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                <i class="fas fa-shopping-cart mr-2"></i>
                Pindahkan Semua ke Keranjang
            </button>
        </form>
        @endif
    </div>

    @if($wishlists->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">Wishlist Kosong</h3>
            <p class="text-gray-600 mb-6">Anda belum memiliki produk favorit</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                <i class="fas fa-shopping-bag mr-2"></i>
                Mulai Belanja
            </a>
        </div>
    @else
        <!-- Wishlist Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($wishlists as $wishlist)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="relative">
                    <!-- Product Image -->
                    <a href="{{ route('products.show', $wishlist->product->slug) }}">
                        @if($wishlist->product->first_image)
                            <img src="{{ $wishlist->product->first_image }}" 
                                 alt="{{ $wishlist->product->name }}" 
                                 class="w-full h-48 object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-48 bg-gray-100 items-center justify-center flex-col hidden">
                                <i class="fas fa-box-open text-5xl text-gray-400 mb-2"></i>
                                <span class="text-sm text-gray-500">No Image</span>
                            </div>
                        @else
                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center flex-col">
                                <i class="fas fa-box-open text-5xl text-gray-400 mb-2"></i>
                                <span class="text-sm text-gray-500">No Image</span>
                            </div>
                        @endif
                    </a>
                    
                    <!-- Remove Button -->
                    <button onclick="removeFromWishlist({{ $wishlist->id }})" 
                            class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-lg hover:bg-red-50 transition-colors group">
                        <i class="fas fa-heart text-red-500 group-hover:text-red-600"></i>
                    </button>
                    
                    <!-- Stock Badge -->
                    @if($wishlist->product->stock_quantity <= 0)
                        <span class="absolute top-2 left-2 px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">
                            Habis
                        </span>
                    @elseif($wishlist->product->stock_quantity < 10)
                        <span class="absolute top-2 left-2 px-3 py-1 bg-yellow-500 text-white text-xs font-semibold rounded-full">
                            Stok Terbatas
                        </span>
                    @endif
                </div>

                <div class="p-4">
                    <!-- Category -->
                    <p class="text-xs text-gray-500 mb-1">{{ $wishlist->product->category->name }}</p>
                    
                    <!-- Product Name -->
                    <a href="{{ route('products.show', $wishlist->product->slug) }}" 
                       class="text-gray-900 font-semibold hover:text-primary-600 transition-colors line-clamp-2 h-12 mb-2">
                        {{ $wishlist->product->name }}
                    </a>
                    
                    <!-- Price -->
                    <div class="mb-4">
                        @if($wishlist->product->is_on_sale)
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-bold text-primary-600">
                                    {{ $wishlist->product->formatted_current_price }}
                                </span>
                                <span class="text-sm text-gray-400 line-through">
                                    {{ $wishlist->product->formatted_price }}
                                </span>
                            </div>
                        @else
                            <span class="text-lg font-bold text-gray-900">
                                {{ $wishlist->product->formatted_price }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex space-x-2">
                        @if($wishlist->product->stock_quantity > 0)
                            <button onclick="addToCartFromWishlist({{ $wishlist->product->id }})" 
                                    class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-sm font-medium">
                                <i class="fas fa-cart-plus mr-1"></i>
                                Keranjang
                            </button>
                        @else
                            <button disabled class="flex-1 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed text-sm font-medium">
                                Stok Habis
                            </button>
                        @endif
                        
                        <a href="{{ route('products.show', $wishlist->product->slug) }}" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>

                <!-- Added Date -->
                <div class="px-4 py-2 bg-gray-50 border-t border-gray-100">
                    <p class="text-xs text-gray-500">
                        <i class="far fa-clock mr-1"></i>
                        Ditambahkan {{ $wishlist->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600">Total Produk di Wishlist</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $wishlists->count() }} Produk</p>
                </div>
                <div>
                    <p class="text-gray-600">Total Estimasi Harga</p>
                    <p class="text-2xl font-bold text-primary-600">
                        Rp {{ number_format($wishlists->sum(function($w) { 
                            return $w->product->current_price; 
                        }), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function removeFromWishlist(wishlistId) {
        Swal.fire({
            title: 'Hapus dari Wishlist?',
            text: 'Produk akan dihapus dari daftar wishlist Anda',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/wishlist/${wishlistId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update wishlist count in navigation
                        updateWishlistCount();
                        showNotification('success', 'Produk dihapus dari wishlist');
                        // Reload page to show updated wishlist
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification('error', data.message || 'Gagal menghapus dari wishlist');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('error', 'Terjadi kesalahan');
                });
            }
        });
    }
    
    function addToCartFromWishlist(productId) {
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count in navigation
                updateCartCount();
                showNotification('success', 'Produk ditambahkan ke keranjang');
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat menambahkan ke keranjang');
        });
    }
    
    function updateWishlistCount() {
        fetch('{{ route("wishlist.count") }}')
            .then(response => response.json())
            .then(data => {
                const wishlistCountEl = document.getElementById('wishlist-count');
                if (wishlistCountEl && data.count > 0) {
                    wishlistCountEl.textContent = data.count;
                    wishlistCountEl.classList.remove('hidden');
                } else if (wishlistCountEl) {
                    wishlistCountEl.classList.add('hidden');
                }
            })
            .catch(error => console.error('Error updating wishlist count:', error));
    }
    
    function updateCartCount() {
        fetch('{{ route("cart.count") }}')
            .then(response => response.json())
            .then(data => {
                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl && data.count > 0) {
                    cartCountEl.textContent = data.count;
                    cartCountEl.classList.remove('hidden');
                } else if (cartCountEl) {
                    cartCountEl.classList.add('hidden');
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }
</script>
@endpush
@endsection
