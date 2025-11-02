@extends('layouts.app')

@section('title', 'Pesanan Berhasil - CannedIt')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Message -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4">
                    <i class="fas fa-check text-green-500 text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Pesanan Berhasil!</h1>
                <p class="text-green-100">Terima kasih atas pembelian Anda</p>
            </div>

            <!-- Order Details -->
            <div class="px-6 py-8">
                <div class="text-center mb-8">
                    <p class="text-gray-600 mb-2">Nomor Pesanan</p>
                    <h2 class="text-2xl font-bold text-gray-900">#{{ $order->order_number }}</h2>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-receipt text-primary-600 mr-2"></i>
                        Ringkasan Pesanan
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Status Pembayaran</span>
                            <span class="font-medium text-gray-900">
                                @if($order->payment_status === 'paid')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                        <i class="fas fa-check-circle"></i> Lunas
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                        <i class="fas fa-clock"></i> Menunggu Pembayaran
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Metode Pembayaran</span>
                            <span class="font-medium text-gray-900">
                                @if($order->payment_method === 'bank_transfer')
                                    Transfer Bank
                                @elseif($order->payment_method === 'credit_card')
                                    Kartu Kredit
                                @elseif($order->payment_method === 'ewallet')
                                    E-Wallet
                                @else
                                    COD (Bayar di Tempat)
                                @endif
                            </span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between">
                            <span class="text-gray-900 font-semibold">Total Pembayaran</span>
                            <span class="text-xl font-bold text-primary-600">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-shopping-bag text-primary-600 mr-2"></i>
                        Produk yang Dibeli ({{ $order->orderItems->count() }} item)
                    </h3>
                    
                    <div class="space-y-3">
                        @foreach($order->orderItems as $item)
                        <div class="flex items-center space-x-4 bg-gray-50 rounded-lg p-3">
                            @if($item->product && $item->product->first_image)
                                <img src="{{ asset($item->product->first_image) }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="w-16 h-16 rounded object-cover">
                            @else
                                <div class="w-16 h-16 rounded bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $item->product_name }}</h4>
                                <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-shipping-fast text-blue-600 text-xl mt-1"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-1">Alamat Pengiriman</h4>
                            <p class="text-sm text-gray-700">
                                <strong>{{ $order->shipping_name }}</strong><br>
                                {{ $order->shipping_phone }}<br>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($order->payment_method !== 'cod')
                <!-- Payment Instructions -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-2">Instruksi Pembayaran</h4>
                            @if($order->payment_method === 'bank_transfer')
                                <p class="text-sm text-gray-700 mb-2">Silakan transfer ke salah satu rekening berikut:</p>
                                <div class="bg-white rounded p-3 space-y-2 text-sm">
                                    <div>
                                        <strong>Bank BCA</strong><br>
                                        No. Rek: 1234567890<br>
                                        a.n. CannedIt Store
                                    </div>
                                    <div class="border-t pt-2">
                                        <strong>Bank Mandiri</strong><br>
                                        No. Rek: 0987654321<br>
                                        a.n. CannedIt Store
                                    </div>
                                </div>
                            @elseif($order->payment_method === 'credit_card')
                                <p class="text-sm text-gray-700">Anda akan diarahkan ke halaman pembayaran kartu kredit.</p>
                            @elseif($order->payment_method === 'ewallet')
                                <p class="text-sm text-gray-700">Scan QR code berikut untuk pembayaran via e-wallet.</p>
                            @endif
                            <p class="text-sm text-gray-600 mt-3">
                                <i class="fas fa-clock mr-1"></i>
                                Mohon lakukan pembayaran dalam 24 jam
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('orders.show', $order) }}" 
                       class="block w-full text-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        <i class="fas fa-eye mr-2"></i>
                        Lihat Detail Pesanan
                    </a>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('products.index') }}" 
                           class="text-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Belanja Lagi
                        </a>
                        <a href="{{ route('orders.index') }}" 
                           class="text-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-list mr-2"></i>
                            Daftar Pesanan
                        </a>
                    </div>
                </div>

                <!-- Email Notification -->
                <div class="mt-6 text-center text-sm text-gray-500">
                    <i class="fas fa-envelope mr-1"></i>
                    Konfirmasi pesanan telah dikirim ke <strong>{{ $order->shipping_email }}</strong>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Butuh bantuan? <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Hubungi Customer Service</a></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Konfetti animation on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Show success toast
        Swal.fire({
            icon: 'success',
            title: 'Pesanan Berhasil!',
            text: 'Pesanan Anda sedang diproses',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>
@endpush
@endsection
