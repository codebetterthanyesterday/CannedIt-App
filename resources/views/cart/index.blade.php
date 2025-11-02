@extends('layouts.app')

@section('title', 'Keranjang Belanja - CannedIt')
@section('description', 'Lihat dan kelola produk dalam keranjang belanja Anda sebelum melakukan checkout.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Keranjang Belanja</h1>
        <p class="text-gray-600">Kelola produk dalam keranjang Anda sebelum melakukan pemesanan</p>
    </div>

    @if($cartItems->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Produk dalam Keranjang ({{ $itemCount }} item)
                        </h2>
                        <button onclick="clearCart()" class="text-red-600 hover:text-red-700 text-sm font-medium">
                            <i class="fas fa-trash mr-1"></i>
                            Kosongkan Keranjang
                        </button>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($cartItems as $item)
                    <div class="p-6 cart-item" data-item-id="{{ $item->id }}">
                        <div class="flex items-start space-x-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                @if($item->product->first_image)
                                    <img src="{{ $item->product->first_image }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-20 h-20 rounded-lg object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-20 h-20 bg-gray-100 rounded-lg items-center justify-center hidden">
                                        <i class="fas fa-box-open text-3xl text-gray-400"></i>
                                    </div>
                                @else
                                    <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box-open text-3xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-primary-600">
                                                {{ $item->product->name }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $item->product->category->name }}</p>
                                        @if($item->product->brand)
                                        <p class="text-sm text-gray-500">Brand: {{ $item->product->brand }}</p>
                                        @endif
                                        
                                        <!-- Stock Warning -->
                                        @if($item->product->stock_quantity < $item->quantity)
                                        <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Stok tidak mencukupi! Tersedia: {{ $item->product->stock_quantity }} unit
                                        </div>
                                        @elseif($item->product->stock_quantity <= 10)
                                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-700">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Stok terbatas: {{ $item->product->stock_quantity }} unit tersisa
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Remove Button -->
                                    <button onclick="removeFromCart({{ $item->id }})" 
                                            class="text-red-600 hover:text-red-700 ml-4">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Price and Quantity -->
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <!-- Quantity Controls -->
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                                    class="px-3 py-1 text-gray-500 hover:text-gray-700 {{ $item->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span class="px-3 py-1 text-sm font-medium min-w-[3rem] text-center">{{ $item->quantity }}</span>
                                            <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                                    class="px-3 py-1 text-gray-500 hover:text-gray-700 {{ $item->quantity >= $item->product->stock_quantity ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}>
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>

                                        <div class="text-sm text-gray-500">
                                            x {{ $item->product->formatted_current_price }}
                                        </div>
                                    </div>

                                    <!-- Item Total -->
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-primary-600" data-item-total="{{ $item->total }}">
                                            Rp {{ number_format($item->total, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal ({{ $itemCount }} item)</span>
                        <span id="subtotal">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pajak (11%)</span>
                        <span id="tax-amount">Rp {{ number_format($total * 0.11, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ongkos Kirim</span>
                        <span id="shipping-amount">
                            @if($total >= 500000)
                                <span class="text-green-600">Gratis</span>
                            @else
                                Rp 25.000
                            @endif
                        </span>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total</span>
                        <span id="final-total" class="text-primary-600">
                            @php
                                $subtotal = $total;
                                $tax = $subtotal * 0.11;
                                $shipping = $subtotal >= 500000 ? 0 : 25000;
                                $finalTotal = $subtotal + $tax + $shipping;
                            @endphp
                            Rp {{ number_format($finalTotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Checkout Buttons -->
                <div class="mt-6 space-y-3">
                    @auth
                        <button onclick="proceedToCheckout()" 
                                class="w-full bg-primary-600 text-white py-3 px-4 rounded-md hover:bg-primary-700 transition-colors font-medium">
                            <i class="fas fa-credit-card mr-2"></i>
                            Lanjut ke Pembayaran
                        </button>
                    @else
                        <div class="text-center text-sm text-gray-600 mb-3">
                            Anda harus login untuk melakukan checkout
                        </div>
                        <a href="{{ route('login') }}" 
                           class="block w-full bg-primary-600 text-white py-3 px-4 rounded-md hover:bg-primary-700 transition-colors font-medium text-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login untuk Checkout
                        </a>
                    @endauth
                    
                    <a href="{{ route('products.index') }}" 
                       class="block w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-md hover:bg-gray-200 transition-colors font-medium text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Lanjut Belanja
                    </a>
                </div>

                <!-- Security Badge -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-center space-x-4 text-xs text-gray-500">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span>Secure Checkout</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-lock text-green-500"></i>
                            <span>SSL Protected</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty Cart -->
    <div class="text-center py-16">
        <div class="max-w-md mx-auto">
            <i class="fas fa-shopping-cart text-gray-300 text-8xl mb-6"></i>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Keranjang Belanja Kosong</h2>
            <p class="text-gray-600 mb-8">Belum ada produk dalam keranjang Anda. Mari mulai berbelanja!</p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-md hover:bg-primary-700 transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Mulai Berbelanja
            </a>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) return;
    
    fetch(`/cart/${itemId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the UI
            location.reload(); // Simple reload for now
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat memperbarui kuantitas');
    });
}

function removeFromCart(itemId) {
    Swal.fire({
        title: 'Hapus Produk?',
        text: 'Produk akan dihapus dari keranjang belanja Anda',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea580c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/cart/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item from DOM
                    document.querySelector(`[data-item-id="${itemId}"]`).remove();
                    
                    // Update cart count and totals
                    updateCartCount();
                    updateCartTotals();
                    
                    showNotification('success', data.message);
                    
                    // Reload if cart is empty
                    if (data.cart_count === 0) {
                        location.reload();
                    }
                } else {
                    showNotification('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Terjadi kesalahan saat menghapus produk');
            });
        }
    });
}

function clearCart() {
    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: 'Semua produk akan dihapus dari keranjang belanja Anda',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea580c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kosongkan!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/cart', {
                method: 'DELETE',
                headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
                    location.reload();
                } else {
                    showNotification('error', 'Terjadi kesalahan saat mengosongkan keranjang');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Terjadi kesalahan saat mengosongkan keranjang');
            });
        }
    });
}

function proceedToCheckout() {
    // Check if there are any stock issues
    const stockWarnings = document.querySelectorAll('.bg-red-50');
    if (stockWarnings.length > 0) {
        showNotification('error', 'Harap periksa stok produk yang tidak mencukupi sebelum checkout');
        return;
    }
    
    window.location.href = '{{ route("orders.checkout") }}';
}

function updateCartTotals() {
    // This would calculate and update totals in real-time
    // For now, we just reload the page
    location.reload();
}
</script>
@endpush
@endsection