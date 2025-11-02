@extends('layouts.app')

@section('title', 'Pembayaran Gagal')

@section('content')
<div class="min-h-screen bg-gray-50 pt-8 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Failed Icon -->
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                <i class="fas fa-times-circle text-6xl text-red-600"></i>
            </div>

            <!-- Failed Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Pembayaran Gagal</h1>
            <p class="text-lg text-gray-600 mb-8">
                Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi atau hubungi customer service.
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
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times mr-2"></i>
                            Belum Dibayar
                        </span>
                    </div>
                </div>
            </div>

            <!-- Reason -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8 text-left">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Kemungkinan Penyebab
                </h3>
                <ul class="space-y-2 text-yellow-800">
                    <li class="flex items-start">
                        <i class="fas fa-circle text-xs mt-2 mr-3 text-yellow-600"></i>
                        <span>Waktu pembayaran telah habis</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-xs mt-2 mr-3 text-yellow-600"></i>
                        <span>Saldo atau limit kartu tidak mencukupi</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-xs mt-2 mr-3 text-yellow-600"></i>
                        <span>Terjadi kesalahan pada sistem pembayaran</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-xs mt-2 mr-3 text-yellow-600"></i>
                        <span>Pembayaran dibatalkan</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if($order->payment_status === 'pending' && (!$order->xendit_expired_at || $order->xendit_expired_at->isFuture()))
                <form action="{{ route('payment.create', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                        <i class="fas fa-redo mr-2"></i>
                        Coba Bayar Lagi
                    </button>
                </form>
                @endif
                
                <a href="{{ route('orders.show', $order->id) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-receipt mr-2"></i>
                    Lihat Detail Pesanan
                </a>
                
                <a href="{{ route('orders.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Daftar Pesanan
                </a>
            </div>

            <!-- Help Section -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-gray-600">
                    Butuh bantuan? 
                    <a href="#" class="text-orange-600 hover:text-orange-700 font-medium">
                        <i class="fas fa-headset mr-1"></i>
                        Hubungi Customer Service
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
