@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan - CannedIt')
@section('description', 'Terima kasih atas pesanan Anda. Berikut adalah detail pesanan dan instruksi pembayaran.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Success Header -->
    <div class="text-center mb-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
            <i class="fas fa-check text-green-600 text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesanan Berhasil Dibuat!</h1>
        <p class="text-lg text-gray-600">Terima kasih atas kepercayaan Anda berbelanja di CannedIt</p>
    </div>

    <!-- Order Information -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="bg-primary-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">
                <i class="fas fa-receipt mr-2"></i>
                Detail Pesanan
            </h2>
        </div>
        
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Nomor Pesanan</h3>
                    <p class="text-2xl font-bold text-gray-900">#{{ $order->order_number }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Tanggal Pesanan</h3>
                    <p class="text-lg text-gray-900">{{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Status Pesanan</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1"></i>
                        Menunggu Pembayaran
                    </span>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Total Pembayaran</h3>
                    <p class="text-2xl font-bold text-primary-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Customer & Shipping Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 pt-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">
                        <i class="fas fa-user text-primary-600 mr-2"></i>
                        Informasi Pelanggan
                    </h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium">Nama:</span> {{ $order->customer_name }}</p>
                        <p><span class="font-medium">Email:</span> {{ $order->customer_email }}</p>
                        <p><span class="font-medium">Telepon:</span> {{ $order->customer_phone }}</p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">
                        <i class="fas fa-map-marker-alt text-primary-600 mr-2"></i>
                        Alamat Pengiriman
                    </h3>
                    <div class="text-sm text-gray-600">
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_province }}</p>
                        <p>{{ $order->shipping_postal_code }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Instructions -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">
                <i class="fas fa-credit-card mr-2"></i>
                Instruksi Pembayaran
            </h2>
        </div>
        
        <div class="px-6 py-6">
            @if($order->payment_method === 'transfer')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-blue-900 mb-3">Transfer Bank</h3>
                    <p class="text-sm text-blue-800 mb-4">Silakan transfer ke salah satu rekening berikut:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded border">
                            <div class="flex items-center mb-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-6 mr-2">
                                <span class="font-medium">Bank BCA</span>
                            </div>
                            <p class="text-sm text-gray-600">No. Rek: <span class="font-mono">1234567890</span></p>
                            <p class="text-sm text-gray-600">A.n: PT CannedIt Indonesia</p>
                        </div>
                        
                        <div class="bg-white p-4 rounded border">
                            <div class="flex items-center mb-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" alt="Mandiri" class="h-6 mr-2">
                                <span class="font-medium">Bank Mandiri</span>
                            </div>
                            <p class="text-sm text-gray-600">No. Rek: <span class="font-mono">9876543210</span></p>
                            <p class="text-sm text-gray-600">A.n: PT CannedIt Indonesia</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-medium text-yellow-900 mb-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Penting!
                    </h4>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>• Transfer sesuai dengan jumlah total: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></li>
                        <li>• Sertakan nomor pesanan <strong>#{{ $order->order_number }}</strong> dalam berita transfer</li>
                        <li>• Pembayaran maksimal 24 jam setelah pesanan dibuat</li>
                        <li>• Kirim bukti transfer ke WhatsApp: <strong>0812-3456-7890</strong></li>
                    </ul>
                </div>
            @elseif($order->payment_method === 'ewallet')
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-purple-900 mb-3">E-Wallet</h3>
                    <p class="text-sm text-purple-800 mb-4">Anda akan menerima notifikasi pembayaran melalui aplikasi e-wallet pilihan Anda.</p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white p-3 rounded text-center border">
                            <i class="fab fa-google-pay text-2xl text-blue-600 mb-1"></i>
                            <p class="text-xs">GoPay</p>
                        </div>
                        <div class="bg-white p-3 rounded text-center border">
                            <i class="fas fa-wallet text-2xl text-purple-600 mb-1"></i>
                            <p class="text-xs">OVO</p>
                        </div>
                        <div class="bg-white p-3 rounded text-center border">
                            <i class="fas fa-mobile-alt text-2xl text-blue-500 mb-1"></i>
                            <p class="text-xs">DANA</p>
                        </div>
                        <div class="bg-white p-3 rounded text-center border">
                            <i class="fas fa-shopping-bag text-2xl text-orange-500 mb-1"></i>
                            <p class="text-xs">ShopeePay</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-medium text-green-900 mb-3">Cash on Delivery (COD)</h3>
                    <p class="text-sm text-green-800">Pembayaran akan dilakukan saat barang diterima. Pastikan Anda memiliki uang pas sebesar <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
                </div>
            @endif
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-box text-primary-600 mr-2"></i>
                Produk yang Dipesan
            </h2>
        </div>
        
        <div class="px-6 py-6">
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                    <div class="flex items-center space-x-4 py-4 border-b border-gray-100 last:border-b-0">
                        <div class="shrink-0 w-16 h-16">
                            <img class="w-16 h-16 rounded-lg object-cover border" 
                                 src="{{ $item->product_image }}" 
                                 alt="{{ $item->product_name }}">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-medium text-gray-900">{{ $item->product_name }}</h4>
                            <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                            <p class="text-sm font-medium text-primary-600">
                                Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-semibold text-gray-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Order Summary -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Ongkos Kirim</span>
                        <span class="text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Diskon</span>
                            <span class="text-green-600">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">PPN (11%)</span>
                        <span class="text-gray-900">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base font-semibold pt-2 border-t border-gray-200">
                        <span class="text-gray-900">Total</span>
                        <span class="text-primary-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="text-center space-y-4">
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('orders.show', $order) }}" 
               class="inline-flex items-center px-6 py-3 border border-primary-600 text-base font-medium rounded-md text-primary-600 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-eye mr-2"></i>
                Lihat Detail Pesanan
            </a>
            
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-shopping-cart mr-2"></i>
                Lanjut Belanja
            </a>
        </div>
        
        <p class="text-sm text-gray-500">
            <i class="fas fa-headset mr-1"></i>
            Butuh bantuan? Hubungi customer service di <strong>0812-3456-7890</strong>
        </p>
    </div>
</div>
@endsection