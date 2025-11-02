@extends('layouts.app')

@section('title', 'Lacak Paket - Pesanan #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
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
                        <span class="ml-4 text-sm font-medium text-gray-900">Lacak Paket</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="bg-orange-600 rounded-lg shadow-lg p-8 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-shipping-fast mr-3"></i>
                    Lacak Paket
                </h1>
                <p class="text-orange-100">Pesanan #{{ $order->order_number }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-orange-100 mb-1">Kurir Pengiriman</div>
                <div class="text-2xl font-bold">
                    @if($order->shipping_courier)
                        {{ strtoupper($order->shipping_courier) }}
                    @else
                        <span class="text-lg">Belum Ditentukan</span>
                    @endif
                </div>
                @if($order->shipping_service)
                    <div class="text-sm text-orange-100">{{ $order->shipping_service }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tracking Number -->
    @if($order->tracking_number)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center">
                <div class="text-sm text-gray-600 mb-2">Nomor Resi</div>
                <div class="flex items-center justify-center space-x-2">
                    <span class="text-2xl font-mono font-bold text-gray-900" id="trackingNumber">{{ $order->tracking_number }}</span>
                    <button onclick="copyTracking()" class="text-primary-600 hover:text-primary-700 transition" title="Salin nomor resi">
                        <i class="fas fa-copy text-xl"></i>
                    </button>
                </div>
                <div class="text-xs text-gray-500 mt-1">Klik icon untuk menyalin nomor resi</div>
            </div>
        </div>
    @endif

    <!-- Tracking Status -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Status Pengiriman</h2>
        
        <div class="relative">
            <!-- Progress Line -->
            <div class="absolute left-6 top-0 bottom-0 w-1 bg-gray-200"></div>
            
            <!-- Timeline -->
            <div class="space-y-8">
                <!-- Delivered -->
                @if($order->status === 'delivered')
                    <div class="relative flex items-start">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-500 shadow-lg z-10">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div class="ml-6 flex-1 bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-green-900">Paket Telah Diterima</h3>
                                <span class="text-sm text-green-700">
                                    {{ $order->delivered_at ? $order->delivered_at->format('d M Y, H:i') : '-' }}
                                </span>
                            </div>
                            <p class="text-sm text-green-800">Paket telah sampai ke tujuan dan diterima oleh {{ $order->customer_name }}</p>
                            <div class="mt-3 flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-200 text-green-900">
                                    <i class="fas fa-box-open mr-1"></i>
                                    DELIVERED
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- In Transit / Shipped -->
                @if(in_array($order->status, ['shipped', 'delivered']))
                    <div class="relative flex items-start">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $order->status === 'shipped' ? 'bg-blue-500 animate-pulse' : 'bg-green-500' }} shadow-lg z-10">
                            <i class="fas fa-truck text-white text-xl"></i>
                        </div>
                        <div class="ml-6 flex-1 bg-{{ $order->status === 'shipped' ? 'blue' : 'gray' }}-50 rounded-lg p-4 border-l-4 border-{{ $order->status === 'shipped' ? 'blue' : 'gray' }}-500">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">Paket Dalam Perjalanan</h3>
                                <span class="text-sm text-gray-700">
                                    {{ $order->shipped_at ? $order->shipped_at->format('d M Y, H:i') : '-' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700">Paket sedang dalam perjalanan menuju alamat tujuan</p>
                            @if($order->shipping_etd)
                                <p class="text-xs text-gray-600 mt-2">
                                    <i class="fas fa-clock mr-1"></i>
                                    Estimasi tiba: {{ $order->shipping_etd }} hari kerja
                                </p>
                            @endif
                            @if($order->status === 'shipped')
                                <div class="mt-3 flex items-center space-x-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-200 text-blue-900 animate-pulse">
                                        <i class="fas fa-shipping-fast mr-1"></i>
                                        ON DELIVERY
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Processing -->
                @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                    <div class="relative flex items-start">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-500 shadow-lg z-10">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div class="ml-6 flex-1 bg-gray-50 rounded-lg p-4 border-l-4 border-gray-300">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">Paket Sedang Dikemas</h3>
                                <span class="text-sm text-gray-700">-</span>
                            </div>
                            <p class="text-sm text-gray-700">Pesanan Anda sedang dikemas oleh penjual</p>
                        </div>
                    </div>
                @endif

                <!-- Paid -->
                @if(in_array($order->payment_status, ['paid']))
                    <div class="relative flex items-start">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-500 shadow-lg z-10">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div class="ml-6 flex-1 bg-gray-50 rounded-lg p-4 border-l-4 border-gray-300">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">Pembayaran Dikonfirmasi</h3>
                                <span class="text-sm text-gray-700">
                                    {{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700">Pembayaran Anda telah diterima dan dikonfirmasi</p>
                        </div>
                    </div>
                @endif

                <!-- Order Created -->
                <div class="relative flex items-start">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-500 shadow-lg z-10">
                        <i class="fas fa-check text-white text-xl"></i>
                    </div>
                    <div class="ml-6 flex-1 bg-gray-50 rounded-lg p-4 border-l-4 border-gray-300">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">Pesanan Dibuat</h3>
                            <span class="text-sm text-gray-700">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-700">Pesanan Anda telah berhasil dibuat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- From -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-store text-primary-600 mr-2"></i>
                Dari
            </h3>
            <div class="space-y-2 text-sm">
                <p class="font-medium text-gray-900">CannedIt Store</p>
                <p class="text-gray-600">Jakarta Pusat, DKI Jakarta</p>
                <p class="text-gray-600">Indonesia</p>
            </div>
        </div>

        <!-- To -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-map-marker-alt text-primary-600 mr-2"></i>
                Tujuan
            </h3>
            <div class="space-y-2 text-sm">
                <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                <p class="text-gray-600">{{ $order->shipping_address }}</p>
                <p class="text-gray-600">{{ $order->shipping_city_name }}, {{ $order->shipping_province_name }}</p>
                <p class="text-gray-600">{{ $order->shipping_postal_code }}</p>
            </div>
        </div>
    </div>

    <!-- External Tracking Links -->
    @if($order->tracking_number)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-external-link-alt text-primary-600 mr-2"></i>
                Lacak via Website Kurir
            </h3>
            <p class="text-sm text-gray-600 mb-4">Lacak paket Anda langsung melalui website resmi kurir pengiriman</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @if($order->shipping_courier && strtolower($order->shipping_courier) === 'jne')
                    <a href="https://www.jne.co.id/id/tracking/trace" target="_blank" 
                       class="flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-truck mr-2"></i>
                        Lacak via JNE
                    </a>
                @elseif($order->shipping_courier && strtolower($order->shipping_courier) === 'pos')
                    <a href="https://www.posindonesia.co.id/id/tracking" target="_blank" 
                       class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-truck mr-2"></i>
                        Lacak via POS
                    </a>
                @elseif($order->shipping_courier && strtolower($order->shipping_courier) === 'tiki')
                    <a href="https://tiki.id/id/tracking" target="_blank" 
                       class="flex items-center justify-center px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-truck mr-2"></i>
                        Lacak via TIKI
                    </a>
                @else
                    <a href="https://cekresi.com/?noresi={{ $order->tracking_number }}" target="_blank" 
                       class="flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition col-span-full">
                        <i class="fas fa-search mr-2"></i>
                        Lacak via Cekresi.com
                    </a>
                @endif
            </div>
            @if(!$order->shipping_courier)
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Kurir pengiriman belum ditentukan. Gunakan Cekresi.com untuk lacak nomor resi secara universal.
                    </p>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-external-link-alt text-primary-600 mr-2"></i>
                Lacak via Website Kurir
            </h3>
            <div class="p-6 bg-gray-50 border border-gray-200 rounded-lg text-center">
                <i class="fas fa-box-open text-4xl text-gray-400 mb-3"></i>
                <p class="text-sm text-gray-600">
                    Nomor resi belum tersedia. Admin akan memperbarui nomor resi setelah paket dikirim.
                </p>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('orders.show', $order) }}" 
               class="flex-1 text-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Detail Pesanan
            </a>
            
            @if($order->status === 'delivered')
                <a href="{{ route('orders.review', $order) }}" 
                   class="flex-1 text-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-star mr-2"></i>
                    Beri Ulasan
                </a>
            @endif
            
            <button onclick="window.location.reload()" 
                    class="px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh Status
            </button>
        </div>
    </div>
</div>

<script>
function copyTracking() {
    const trackingNumber = document.getElementById('trackingNumber').textContent;
    navigator.clipboard.writeText(trackingNumber).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Nomor resi berhasil disalin',
            timer: 2000,
            showConfirmButton: false
        });
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}
</script>
@endsection
