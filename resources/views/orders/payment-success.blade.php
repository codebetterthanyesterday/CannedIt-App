@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="min-h-screen bg-gray-50 pt-8 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
                <i class="fas fa-check-circle text-6xl text-green-600"></i>
            </div>

            <!-- Success Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Pembayaran Berhasil!</h1>
            <p class="text-lg text-gray-600 mb-8">
                Terima kasih telah melakukan pembayaran. Pesanan Anda sedang diproses.
            </p>

            <!-- Order Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Pesanan</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nomor Pesanan:</span>
                        <span class="font-semibold text-gray-900">{{ $order->order_number }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Pembayaran:</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($order->xendit_paid_at)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Waktu Pembayaran:</span>
                        <span class="font-semibold text-gray-900">{{ $order->xendit_paid_at->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($order->payment_channel)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode Pembayaran:</span>
                        <span class="font-semibold text-gray-900">{{ strtoupper($order->payment_channel) }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-2"></i>
                            Dibayar
                        </span>
                    </div>
                </div>
            </div>

            <!-- What's Next -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 text-left">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    Langkah Selanjutnya
                </h3>
                <ul class="space-y-2 text-blue-800">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mt-1 mr-3 text-blue-600"></i>
                        <span>Pesanan Anda akan segera diproses oleh tim kami</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-box mt-1 mr-3 text-blue-600"></i>
                        <span>Anda akan menerima notifikasi saat pesanan dikirim</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-truck mt-1 mr-3 text-blue-600"></i>
                        <span>Lacak status pengiriman melalui halaman detail pesanan</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('orders.show', $order->id) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                    <i class="fas fa-receipt mr-2"></i>
                    Lihat Detail Pesanan
                </a>
                
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Lanjut Belanja
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
