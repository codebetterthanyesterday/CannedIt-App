@extends('layouts.app')

@section('title', $product->name . ' - CannedIt')
@section('description', $product->short_description)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                    <i class="fas fa-home mr-2"></i>
                    Beranda
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('products.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600">
                        Produk
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600">
                        {{ $product->category->name }}
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Images -->
        <div>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Main Image -->
                <div class="relative">
                    @if($product->first_image)
                        <img id="main-image" 
                             src="{{ $product->first_image }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-96 object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-full h-96 bg-gray-100 items-center justify-center flex-col hidden">
                            <i class="fas fa-box-open text-8xl text-gray-400 mb-4"></i>
                            <span class="text-lg text-gray-500">No Image Available</span>
                        </div>
                    @else
                        <div class="w-full h-96 bg-gray-100 flex items-center justify-center flex-col">
                            <i class="fas fa-box-open text-8xl text-gray-400 mb-4"></i>
                            <span class="text-lg text-gray-500">No Image Available</span>
                        </div>
                    @endif
                    
                    @if($product->is_on_sale)
                        <span class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 text-sm font-semibold rounded">
                            -{{ $product->discount_percentage }}%
                        </span>
                    @endif
                    
                    @if($product->is_featured)
                        <span class="absolute top-4 right-4 bg-yellow-500 text-white px-3 py-1 text-sm font-semibold rounded">
                            <i class="fas fa-star mr-1"></i>Unggulan
                        </span>
                    @endif
                </div>

                <!-- Thumbnail Images (if multiple images exist) -->
                @if($product->all_images && count($product->all_images) > 1)
                <div class="p-4">
                    <div class="flex space-x-2 overflow-x-auto">
                        @foreach($product->all_images as $index => $image)
                        <img src="{{ $image }}" 
                             alt="{{ $product->name }}" 
                             class="w-16 h-16 object-cover rounded cursor-pointer border-2 {{ $index === 0 ? 'border-primary-500' : 'border-gray-200' }} hover:border-primary-500 transition-colors"
                             onclick="changeMainImage('{{ $image }}', this)"
                             onerror="this.src='{{ asset('images/no-image.svg') }}'; this.onerror=null;">
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="space-y-6">
            <!-- Title and Category -->
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="bg-primary-100 text-primary-800 text-sm px-3 py-1 rounded-full">{{ $product->category->name }}</span>
                    @if($product->brand)
                        <span class="bg-gray-100 text-gray-600 text-sm px-3 py-1 rounded-full">{{ $product->brand }}</span>
                    @endif
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                <p class="text-lg text-gray-600">{{ $product->description }}</p>
            </div>

            <!-- Price -->
            <div class="border-t pt-6">
                <div class="flex items-center space-x-4 mb-4">
                    @if($product->is_on_sale)
                        <span class="text-3xl font-bold text-primary-600">{{ $product->formatted_current_price }}</span>
                        <span class="text-xl text-gray-500 line-through">{{ $product->formatted_price }}</span>
                        <span class="bg-red-100 text-red-800 px-2 py-1 text-sm font-semibold rounded">
                            Hemat {{ $product->discount_percentage }}%
                        </span>
                    @else
                        <span class="text-3xl font-bold text-primary-600">{{ $product->formatted_price }}</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="flex items-center space-x-4 text-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-box"></i>
                        <span class="{{ $product->stock_status_class }}">
                            @if($product->stock_quantity)
                                Stok tersedia ({{ $product->stock_quantity }} unit)
                            @elseif($product->stock_quantity)
                                Stok terbatas ({{ $product->stock_quantity }} unit)
                            @else
                                Stok habis
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-weight"></i>
                        <span class="text-gray-600">{{ $product->weight ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Add to Cart -->
            @if($product->stock_quantity)
            <div class="border-t pt-6">
                <div class="flex items-center space-x-4 mb-4">
                    <label class="text-sm font-medium text-gray-700">Jumlah:</label>
                    <div class="flex items-center border border-gray-300 rounded-md">
                        <button type="button" onclick="decreaseQuantity()" class="px-3 py-2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" 
                               class="w-16 text-center border-0 focus:ring-0">
                        <button type="button" onclick="increaseQuantity()" class="px-3 py-2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <span class="text-sm text-gray-500">Max: {{ $product->stock_quantity }} unit</span>
                </div>

                <div class="flex space-x-4">
                    @if(Auth::check() && Auth::user()->is_admin)
                    <button disabled class="flex-1 bg-gray-300 text-gray-500 py-3 px-6 rounded-md cursor-not-allowed flex items-center justify-center space-x-2">
                        <i class="fas fa-user-shield"></i>
                        <span>Fitur untuk Customer</span>
                    </button>
                    @elseif(Auth::check())
                    <button onclick="addToCart()" class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-md hover:bg-primary-700 transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-cart-plus"></i>
                        <span>Tambah ke Keranjang</span>
                    </button>
                    <button onclick="buyNow()" class="flex-1 bg-green-600 text-white py-3 px-6 rounded-md hover:bg-green-700 transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-bolt"></i>
                        <span>Beli Sekarang</span>
                    </button>
                    <button onclick="toggleWishlist()" id="wishlist-btn" class="bg-white border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-md hover:border-red-500 hover:text-red-500 transition-colors flex items-center justify-center">
                        <i class="far fa-heart" id="wishlist-icon"></i>
                    </button>
                    @else
                    <button onclick="promptLogin('cart')" class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-md hover:bg-primary-700 transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-cart-plus"></i>
                        <span>Tambah ke Keranjang</span>
                    </button>
                    <button onclick="promptLogin('buy')" class="flex-1 bg-green-600 text-white py-3 px-6 rounded-md hover:bg-green-700 transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-bolt"></i>
                        <span>Beli Sekarang</span>
                    </button>
                    <button onclick="promptLogin('wishlist')" class="bg-white border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-md hover:border-red-500 hover:text-red-500 transition-colors flex items-center justify-center">
                        <i class="far fa-heart"></i>
                    </button>
                    @endif
                </div>
            </div>
            @else
            <div class="border-t pt-6">
                <div class="flex space-x-4">
                    <button disabled class="flex-1 bg-gray-300 text-gray-500 py-3 px-6 rounded-md cursor-not-allowed">
                        <i class="fas fa-times mr-2"></i>
                        Produk Tidak Tersedia
                    </button>
                    @if(Auth::check() && Auth::user()->role !== 'admin')
                    <button onclick="toggleWishlist()" id="wishlist-btn" class="bg-white border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-md hover:border-red-500 hover:text-red-500 transition-colors flex items-center justify-center">
                        <i class="far fa-heart" id="wishlist-icon"></i>
                    </button>
                    @elseif(!Auth::check())
                    <button onclick="promptLogin('wishlist')" class="bg-white border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-md hover:border-red-500 hover:text-red-500 transition-colors flex items-center justify-center">
                        <i class="far fa-heart"></i>
                    </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Product Information Tabs -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="border-b">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button onclick="showTab('description')" class="tab-button active border-b-2 border-primary-500 py-4 px-1 text-sm font-medium text-primary-600">
                    Deskripsi
                </button>
                <button onclick="showTab('specifications')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Spesifikasi
                </button>
                <button onclick="showTab('nutrition')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Informasi Gizi
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Description Tab -->
            <div id="description" class="tab-content">
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                    
                    @if($product->ingredients)
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-900 mb-2">Komposisi:</h4>
                        <p class="text-gray-700">{{ $product->ingredients }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Specifications Tab -->
            <div id="specifications" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Detail Produk</h4>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">SKU:</dt>
                                <dd class="font-medium">{{ $product->sku }}</dd>
                            </div>
                            @if($product->brand)
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Brand:</dt>
                                <dd class="font-medium">{{ $product->brand }}</dd>
                            </div>
                            @endif
                            @if($product->weight)
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Berat:</dt>
                                <dd class="font-medium">{{ $product->weight }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Kategori:</dt>
                                <dd class="font-medium">{{ $product->category->name }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Informasi Tanggal</h4>
                        <dl class="space-y-3">
                            @if($product->manufacture_date)
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Tanggal Produksi:</dt>
                                <dd class="font-medium">{{ $product->manufacture_date->format('d/m/Y') }}</dd>
                            </div>
                            @endif
                            @if($product->expiry_date)
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Tanggal Kedaluwarsa:</dt>
                                <dd class="font-medium">{{ $product->expiry_date->format('d/m/Y') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Nutrition Tab -->
            <div id="nutrition" class="tab-content hidden">
                @if($product->nutritional_info)
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Informasi Nilai Gizi</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $product->nutritional_info }}</p>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Informasi gizi dapat berbeda tergantung pada variasi produk dan supplier.
                    </p>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-utensils text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Informasi gizi tidak tersedia untuk produk ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count())
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Produk Serupa</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <a href="{{ route('products.show', $relatedProduct->slug) }}">
                        @if($relatedProduct->first_image)
                            <img src="{{ $relatedProduct->first_image }}" 
                                 alt="{{ $relatedProduct->name }}" 
                                 class="w-full h-40 object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-40 bg-gray-100 items-center justify-center flex-col hidden">
                                <i class="fas fa-box-open text-4xl text-gray-400 mb-2"></i>
                                <span class="text-sm text-gray-500">No Image</span>
                            </div>
                        @else
                            <div class="w-full h-40 bg-gray-100 flex items-center justify-center flex-col">
                                <i class="fas fa-box-open text-4xl text-gray-400 mb-2"></i>
                                <span class="text-sm text-gray-500">No Image</span>
                            </div>
                        @endif
                    </a>
                    @if($relatedProduct->is_on_sale)
                        <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 text-xs font-semibold rounded">
                            -{{ $relatedProduct->discount_percentage }}%
                        </span>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="hover:text-primary-600">
                            {{ $relatedProduct->name }}
                        </a>
                    </h3>
                    <div class="flex items-center justify-between">
                        @if($relatedProduct->is_on_sale)
                            <span class="text-lg font-bold text-primary-600">{{ $relatedProduct->formatted_current_price }}</span>
                        @else
                            <span class="text-lg font-bold text-primary-600">{{ $relatedProduct->formatted_price }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
let maxQuantity = {{ $product->stock_quantity }};
let isInWishlist = false;
let wishlistId = null;

// Check wishlist status on page load
@auth
document.addEventListener('DOMContentLoaded', function() {
    checkWishlistStatus();
});

function checkWishlistStatus() {
    fetch('{{ route("wishlist.check", $product->id) }}')
        .then(response => response.json())
        .then(data => {
            isInWishlist = data.in_wishlist;
            wishlistId = data.wishlist_id;
            updateWishlistButton();
        })
        .catch(error => console.error('Error:', error));
}

function toggleWishlist() {
    if (isInWishlist) {
        // Remove from wishlist
        fetch(`{{ url('wishlist') }}/${wishlistId}`, {
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
                isInWishlist = false;
                wishlistId = null;
                updateWishlistButton();
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
                product_id: {{ $product->id }}
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
                isInWishlist = true;
                wishlistId = data.wishlist_id;
                updateWishlistButton();
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

function updateWishlistButton() {
    const btn = document.getElementById('wishlist-btn');
    const icon = document.getElementById('wishlist-icon');
    
    if (isInWishlist) {
        btn.classList.remove('border-gray-300', 'text-gray-700');
        btn.classList.add('border-red-500', 'text-red-500', 'bg-red-50');
        icon.classList.remove('far');
        icon.classList.add('fas');
    } else {
        btn.classList.remove('border-red-500', 'text-red-500', 'bg-red-50');
        btn.classList.add('border-gray-300', 'text-gray-700');
        icon.classList.remove('fas');
        icon.classList.add('far');
    }
}
@endauth

function changeMainImage(imageSrc, thumbnail) {
    document.getElementById('main-image').src = imageSrc;
    
    // Update thumbnail borders
    document.querySelectorAll('.w-16.h-16').forEach(img => {
        img.classList.remove('border-primary-500');
        img.classList.add('border-gray-200');
    });
    thumbnail.classList.remove('border-gray-200');
    thumbnail.classList.add('border-primary-500');
}

function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    if (currentValue < maxQuantity) {
        quantityInput.value = currentValue + 1;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
}

function addToCart() {
    const quantity = parseInt(document.getElementById('quantity').value);
    
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: {{ $product->id }},
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
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

function buyNow() {
    const quantity = parseInt(document.getElementById('quantity').value);
    
    // Add to cart first, then redirect to checkout
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: {{ $product->id }},
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to checkout
            window.location.href = '{{ route("orders.checkout") }}';
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan');
    });
}

function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-primary-500', 'text-primary-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName).classList.remove('hidden');
    
    // Add active class to selected tab button
    event.target.classList.add('active', 'border-primary-500', 'text-primary-600');
    event.target.classList.remove('border-transparent', 'text-gray-500');
}

function promptLogin(action) {
    let message = '';
    switch(action) {
        case 'cart':
            message = 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang';
            break;
        case 'buy':
            message = 'Silakan login terlebih dahulu untuk membeli produk';
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