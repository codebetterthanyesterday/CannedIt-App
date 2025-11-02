@extends('layouts.app')

@section('title', 'Daftar Produk Makanan Kaleng - CannedIt')
@section('description', 'Jelajahi koleksi lengkap makanan kaleng berkualitas. Tersedia sayuran, buah, daging, seafood, sup, dan pasta dengan harga terjangkau.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Section -->
    @if(!request()->has('search') && !request()->has('category'))
    <div class="bg-orange-600 rounded-lg p-8 mb-8 text-white">
        <div class="max-w-3xl">
            <h1 class="text-4xl font-bold mb-4">Makanan Kaleng Berkualitas</h1>
            <p class="text-xl mb-6 opacity-90">Temukan berbagai pilihan makanan kaleng segar dan bergizi untuk kebutuhan sehari-hari Anda</p>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-truck text-green-300"></i>
                    <span>Gratis ongkir untuk pembelian di atas 500rb</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-shield-alt text-green-300"></i>
                    <span>Produk berkualitas terjamin</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters and Search Results -->
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                <h3 class="font-semibold text-lg mb-4">Filter Produk</h3>
                
                <form action="{{ route('products.index') }}" method="GET" id="filter-form">
                    <!-- Keep search term -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Categories -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Kategori</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm">Semua Kategori</span>
                            </label>
                            @foreach($categories as $category)
                            <label class="flex items-center">
                                <input type="radio" name="category" value="{{ $category->id }}" {{ request('category') == $category->id ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm">{{ $category->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Rentang Harga</h4>
                        <div class="space-y-2">
                            <div class="flex space-x-2 flex-wrap w-full gap-2">
                                <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" class="flex-1 w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                                <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" class="flex-1 px-3 w-full py-2 text-sm border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>

                    <!-- Brand -->
                    @if($brands->count())
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Brand</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="brand" value="" {{ !request('brand') ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm">Semua Brand</span>
                            </label>
                            @foreach($brands as $brand)
                            <label class="flex items-center">
                                <input type="radio" name="brand" value="{{ $brand }}" {{ request('brand') == $brand ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm">{{ $brand }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Featured Products -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="featured" value="1" {{ request('featured') ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-sm">Produk Unggulan</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>
                        Terapkan Filter
                    </button>
                </form>

                <!-- Clear Filters -->
                @if(request()->hasAny(['category', 'min_price', 'max_price', 'brand', 'featured']))
                <a href="{{ route('products.index', request()->only('search')) }}" class="block w-full text-center mt-3 text-gray-600 hover:text-primary-600 text-sm">
                    <i class="fas fa-times mr-1"></i>
                    Hapus Filter
                </a>
                @endif
            </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <!-- Sort and Results Count -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div>
                        <p class="text-gray-600">
                            Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                            @if(request('search'))
                                untuk "<strong>{{ request('search') }}</strong>"
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <form action="{{ route('products.index') }}" method="GET" class="flex items-center space-x-2">
                            <!-- Preserve filters -->
                            @foreach(request()->except(['sort_by', 'sort_order']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            
                            <label class="text-sm text-gray-600">Urutkan:</label>
                            <select name="sort_by" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-primary-500 focus:border-primary-500">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="price_low" {{ request('sort_by') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_high" {{ request('sort_by') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                                <option value="popularity" {{ request('sort_by') == 'popularity' ? 'selected' : '' }}>Terpopuler</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            @if($products->count())
                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <!-- Product Image -->
                        <div class="relative">
                            <a href="{{ route('products.show', $product->slug) }}">
                                @if($product->first_image)
                                    <img src="{{ $product->first_image }}" 
                                         alt="{{ $product->name }}" 
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
                            
                            @if($product->is_on_sale)
                                <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 text-xs font-semibold rounded">
                                    -{{ $product->discount_percentage }}%
                                </span>
                            @endif
                            
                            <!-- Wishlist Button -->
                            @if(Auth::check() && !Auth::user()->is_admin)
                            <button onclick="toggleWishlist({{ $product->id }}, event)" 
                                    class="wishlist-toggle absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-red-50 transition-colors z-10"
                                    data-product-id="{{ $product->id }}">
                                <i class="far fa-heart text-gray-600 hover:text-red-500"></i>
                            </button>
                            @elseif(!Auth::check())
                            <button onclick="promptLogin({{ $product->id }}, 'wishlist')" 
                                    class="wishlist-toggle absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-red-50 transition-colors z-10">
                                <i class="far fa-heart text-gray-600 hover:text-red-500"></i>
                            </button>
                            @endif
                            
                            @if($product->is_featured)
                                <span class="absolute {{ auth()->check() && !auth()->user()->is_admin ? 'top-12' : 'top-2' }} right-2 bg-yellow-500 text-white px-2 py-1 text-xs font-semibold rounded">
                                    <i class="fas fa-star"></i>
                                </span>
                            @endif

                            @if(!$product->stock_quantity)
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <span class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold">Stok Habis</span>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <div class="mb-2">
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $product->category->name }}</span>
                            </div>
                            
                            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
                                <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary-600">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                            
                            <!-- Brand and Weight -->
                            <div class="flex justify-between items-center text-xs text-gray-500 mb-3">
                                @if($product->brand)
                                    <span><i class="fas fa-tag mr-1"></i>{{ $product->brand }}</span>
                                @endif
                                @if($product->weight)
                                    <span><i class="fas fa-weight mr-1"></i>{{ $product->weight }}</span>
                                @endif
                            </div>

                            <!-- Price -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    @if($product->is_on_sale)
                                        <span class="text-lg font-bold text-primary-600">{{ $product->formatted_current_price }}</span>
                                        <span class="text-sm text-gray-500 line-through ml-2">{{ $product->formatted_price }}</span>
                                    @else
                                        <span class="text-lg font-bold text-primary-600">{{ $product->formatted_price }}</span>
                                    @endif
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">Stok: {{ $product->stock_quantity }}</div>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            @if($product->stock_quantity)
                                @if(Auth::check() && Auth::user()->is_admin)
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-md cursor-not-allowed flex items-center justify-center space-x-2">
                                    <i class="fas fa-user-shield"></i>
                                    <span>Fitur untuk Customer</span>
                                </button>
                                @elseif(Auth::check())
                                <button onclick="addToCart({{ $product->id }})" 
                                        class="w-full bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700 transition-colors flex items-center justify-center space-x-2">
                                    <i class="fas fa-cart-plus"></i>
                                    <span>Tambah ke Keranjang</span>
                                </button>
                                @else
                                <button onclick="promptLogin({{ $product->id }}, 'cart')" 
                                        class="w-full bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700 transition-colors flex items-center justify-center space-x-2">
                                    <i class="fas fa-cart-plus"></i>
                                    <span>Tambah ke Keranjang</span>
                                </button>
                                @endif
                            @else
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-md cursor-not-allowed">
                                    <i class="fas fa-times mr-2"></i>
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <!-- No Products Found -->
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Produk Tidak Ditemukan</h3>
                    <p class="text-gray-500 mb-6">
                        @if(request('search'))
                            Tidak ada produk yang cocok dengan pencarian "{{ request('search') }}"
                        @else
                            Tidak ada produk yang sesuai dengan filter yang dipilih
                        @endif
                    </p>
                    <a href="{{ route('products.index') }}" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Lihat Semua Produk
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
@auth
// Store wishlist state
let wishlistItems = {};

// Check wishlist status for all products on page load
document.addEventListener('DOMContentLoaded', function() {
    checkAllWishlistStatus();
});

function checkAllWishlistStatus() {
    const productIds = Array.from(document.querySelectorAll('.wishlist-toggle')).map(btn => btn.dataset.productId);
    
    productIds.forEach(productId => {
        fetch(`{{ url('wishlist/check') }}/${productId}`)
            .then(response => response.json())
            .then(data => {
                wishlistItems[productId] = {
                    in_wishlist: data.in_wishlist,
                    wishlist_id: data.wishlist_id
                };
                updateWishlistButton(productId);
            })
            .catch(error => console.error('Error:', error));
    });
}

function toggleWishlist(productId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const item = wishlistItems[productId] || { in_wishlist: false, wishlist_id: null };
    
    if (item.in_wishlist) {
        // Remove from wishlist
        fetch(`{{ url('wishlist') }}/${item.wishlist_id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                wishlistItems[productId] = { in_wishlist: false, wishlist_id: null };
                updateWishlistButton(productId);
                updateWishlistCount();
                showNotification('success', 'Produk dihapus dari wishlist');
            } else {
                showNotification('error', data.message || 'Gagal menghapus dari wishlist');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan: ' + error.message);
        });
    } else {
        // Add to wishlist
        fetch('{{ route("wishlist.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                wishlistItems[productId] = { in_wishlist: true, wishlist_id: data.wishlist_id };
                updateWishlistButton(productId);
                updateWishlistCount();
                showNotification('success', 'Produk ditambahkan ke wishlist');
            } else {
                showNotification('error', data.message || 'Gagal menambahkan ke wishlist');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan: ' + error.message);
        });
    }
}

function updateWishlistButton(productId) {
    const btn = document.querySelector(`[data-product-id="${productId}"]`);
    if (!btn) return;
    
    const icon = btn.querySelector('i');
    const item = wishlistItems[productId];
    
    if (item && item.in_wishlist) {
        btn.classList.add('bg-red-50');
        icon.classList.remove('far', 'text-gray-600');
        icon.classList.add('fas', 'text-red-500');
    } else {
        btn.classList.remove('bg-red-50');
        icon.classList.remove('fas', 'text-red-500');
        icon.classList.add('far', 'text-gray-600');
    }
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
@endauth

function addToCart(productId) {
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
            // Update cart count
            updateCartCount();
            
            // Show success message
            showNotification('success', data.message);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat menambahkan produk ke keranjang');
    });
}

function promptLogin(productId, action) {
    let message = '';
    switch(action) {
        case 'cart':
            message = 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang';
            break;
        case 'wishlist':
            message = 'Silakan login terlebih dahulu untuk menambahkan produk ke wishlist';
            break;
    }
    
    Swal.fire({
        title: 'Login Required',
        text: message,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Login',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route("login") }}?redirect=' + encodeURIComponent(window.location.pathname);
        }
    });
}
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
@endsection