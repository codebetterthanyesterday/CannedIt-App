@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - CannedIt')
@section('description', 'Detail pesanan dan status pengiriman produk makanan kaleng Anda.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                        <span class="ml-4 text-sm font-medium text-gray-900">#{{ $order->order_number }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Order Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    Pesanan #{{ $order->order_number }}
                </h1>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB
                </p>
            </div>
            
            <div class="mt-4 md:mt-0">
                @php
                    $statusConfig = [
                        'pending' => ['bg-yellow-100', 'text-yellow-800', 'fa-clock', 'Menunggu Pembayaran'],
                        'paid' => ['bg-blue-100', 'text-blue-800', 'fa-credit-card', 'Dibayar'],
                        'processing' => ['bg-orange-100', 'text-orange-800', 'fa-cog', 'Diproses'],
                        'shipped' => ['bg-purple-100', 'text-purple-800', 'fa-truck', 'Dikirim'],
                        'delivered' => ['bg-green-100', 'text-green-800', 'fa-check-circle', 'Diterima'],
                        'cancelled' => ['bg-red-100', 'text-red-800', 'fa-times-circle', 'Dibatalkan']
                    ];
                    $config = $statusConfig[$order->status] ?? $statusConfig['pending'];
                @endphp
                
                <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-medium {{ $config[0] }} {{ $config[1] }}">
                    <i class="fas {{ $config[2] }} mr-2"></i>
                    {{ $config[3] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Order Tracking -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">
            <i class="fas fa-route text-primary-600 mr-2"></i>
            Status Pengiriman
        </h2>
        
        <div class="relative">
            <!-- Progress Line -->
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            
            <!-- Timeline Items -->
            <div class="space-y-8">
                <!-- Order Placed -->
                <div class="relative flex items-start">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $order->status != 'cancelled' ? 'bg-green-500' : 'bg-gray-300' }} z-10">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-medium text-gray-900">Pesanan Dibuat</h3>
                        <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-xs text-gray-400 mt-1">Pesanan Anda telah berhasil dibuat</p>
                    </div>
                </div>
                
                <!-- Payment -->
                <div class="relative flex items-start">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']) ? 'bg-green-500' : ($order->status == 'pending' ? 'bg-yellow-500' : 'bg-gray-300') }} z-10">
                        <i class="fas {{ in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']) ? 'fa-check' : 'fa-clock' }} text-white text-sm"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-medium text-gray-900">Pembayaran</h3>
                        <p class="text-sm text-gray-500">
                            @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']))
                                {{ $order->updated_at->format('d M Y, H:i') }}
                            @else
                                Menunggu pembayaran
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']))
                                Pembayaran telah dikonfirmasi
                            @else
                                Segera lakukan pembayaran
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Processing -->
                <div class="relative flex items-start">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-green-500' : ($order->status == 'paid' ? 'bg-yellow-500' : 'bg-gray-300') }} z-10">
                        <i class="fas {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'fa-check' : 'fa-cog' }} text-white text-sm"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-medium text-gray-900">Diproses</h3>
                        <p class="text-sm text-gray-500">
                            @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                                Sedang diproses
                            @else
                                Menunggu proses
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Pesanan sedang disiapkan untuk pengiriman</p>
                    </div>
                </div>
                
                <!-- Shipped -->
                <div class="relative flex items-start">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-green-500' : ($order->status == 'processing' ? 'bg-yellow-500' : 'bg-gray-300') }} z-10">
                        <i class="fas {{ in_array($order->status, ['shipped', 'delivered']) ? 'fa-check' : 'fa-truck' }} text-white text-sm"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-medium text-gray-900">Dikirim</h3>
                        <p class="text-sm text-gray-500">
                            @if(in_array($order->status, ['shipped', 'delivered']))
                                Dalam pengiriman
                            @else
                                Menunggu pengiriman
                            @endif
                        </p>
                        @if($order->tracking_number)
                            <p class="text-xs text-gray-400 mt-1">
                                Nomor Resi: <span class="font-mono font-semibold">{{ $order->tracking_number }}</span>
                            </p>
                        @endif
                    </div>
                </div>
                
                <!-- Delivered -->
                <div class="relative flex items-start">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $order->status == 'delivered' ? 'bg-green-500' : ($order->status == 'shipped' ? 'bg-yellow-500' : 'bg-gray-300') }} z-10">
                        <i class="fas {{ $order->status == 'delivered' ? 'fa-check' : 'fa-box' }} text-white text-sm"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-medium text-gray-900">Diterima</h3>
                        <p class="text-sm text-gray-500">
                            @if($order->status == 'delivered')
                                Pesanan telah diterima
                            @else
                                Menunggu konfirmasi penerimaan
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Konfirmasi penerimaan setelah barang diterima</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2">
            <!-- Products -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-box-open text-primary-600 mr-2"></i>
                    Produk yang Dipesan
                </h2>
                
                <div class="divide-y divide-gray-200">
                    @foreach($order->orderItems as $item)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center space-x-4">
                                <div class="shrink-0 w-20 h-20">
                                    @php
                                        $hasImage = false;
                                        if ($item->product) {
                                            $imagePath = $item->product->first_image;
                                            if ($imagePath) {
                                                $hasImage = true;
                                            }
                                        }
                                    @endphp
                                    
                                    @if($hasImage)
                                        <img class="w-20 h-20 rounded-lg object-cover border" 
                                             src="{{ asset($item->product->first_image) }}" 
                                             alt="{{ $item->product_name }}"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-20 h-20 rounded-lg bg-gray-100 border border-gray-200 items-center justify-center hidden">
                                            <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                                        </div>
                                    @else
                                        <div class="w-20 h-20 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                            <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-medium text-gray-900">{{ $item->product_name }}</h4>
                                    <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                                    <div class="mt-1 flex items-center space-x-4 text-sm">
                                        <span class="text-gray-500">Qty: {{ $item->quantity }}</span>
                                        <span class="text-primary-600 font-medium">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-sticky-note text-primary-600 mr-2"></i>
                        Catatan Pesanan
                    </h2>
                    <p class="text-gray-700">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Order Summary -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900 font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ongkos Kirim</span>
                        <span class="text-gray-900 font-medium">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Diskon</span>
                            <span class="text-green-600 font-medium">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">PPN (11%)</span>
                        <span class="text-gray-900 font-medium">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-base font-semibold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-primary-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-credit-card text-primary-600 mr-2"></i>
                    Info Pembayaran
                </h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode:</span>
                        <span class="font-medium">{{ strtoupper($order->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        @php
                            $paymentStatusConfig = [
                                'pending' => ['bg-yellow-100', 'text-yellow-800', 'Belum Dibayar'],
                                'paid' => ['bg-green-100', 'text-green-800', 'Sudah Dibayar'],
                                'expired' => ['bg-red-100', 'text-red-800', 'Kadaluarsa'],
                                'failed' => ['bg-red-100', 'text-red-800', 'Gagal'],
                            ];
                            $pConfig = $paymentStatusConfig[$order->payment_status] ?? ['bg-gray-100', 'text-gray-800', 'Unknown'];
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pConfig[0] }} {{ $pConfig[1] }}">
                            {{ $pConfig[2] }}
                        </span>
                    </div>
                    @if($order->xendit_paid_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dibayar:</span>
                            <span class="font-medium">{{ $order->xendit_paid_at->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                    @if($order->payment_channel)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Channel:</span>
                            <span class="font-medium">{{ strtoupper($order->payment_channel) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user text-primary-600 mr-2"></i>
                    Info Pelanggan
                </h2>
                <div class="space-y-2 text-sm">
                    <p><span class="text-gray-600">Nama:</span> <span class="font-medium">{{ $order->customer_name }}</span></p>
                    <p><span class="text-gray-600">Email:</span> <span class="font-medium">{{ $order->customer_email }}</span></p>
                    <p><span class="text-gray-600">Telepon:</span> <span class="font-medium">{{ $order->customer_phone }}</span></p>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-shipping-fast text-primary-600 mr-2"></i>
                    Info Pengiriman
                </h2>
                <div class="text-sm text-gray-700 space-y-1">
                    <p class="font-medium">{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_province }}</p>
                    <p>{{ $order->shipping_postal_code }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h2>
                <div class="space-y-3">
                    @if($order->payment_status === 'pending' && $order->status !== 'cancelled')
                        @if($order->xendit_invoice_url && (!$order->xendit_expired_at || $order->xendit_expired_at->isFuture()))
                            <!-- Active Xendit Invoice -->
                            <a href="{{ $order->xendit_invoice_url }}" target="_blank"
                               class="block w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 transition-colors text-center">
                                <i class="fas fa-credit-card mr-2"></i>
                                Bayar Sekarang
                            </a>
                            @if($order->xendit_expired_at)
                                <p class="text-xs text-center text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    Kadaluarsa: {{ $order->xendit_expired_at->format('d M Y, H:i') }}
                                </p>
                            @endif
                        @else
                            <!-- Create New Payment -->
                            <form action="{{ route('payment.create', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 transition-colors">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Bayar Sekarang
                                </button>
                            </form>
                        @endif
                    @endif
                    
                    @if(in_array($order->status, ['shipped']))
                        <a href="{{ route('orders.tracking', $order) }}" 
                           class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-truck mr-2"></i>
                            Lacak Paket
                        </a>
                    @endif
                    
                    @if($order->status === 'delivered')
                        <a href="{{ route('orders.review', $order) }}" 
                           class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                            <i class="fas fa-star mr-2"></i>
                            Beri Ulasan
                        </a>
                    @endif
                    
                    @if(in_array($order->status, ['pending', 'paid']))
                        <button onclick="confirmCancel()" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Batalkan Pesanan
                        </button>
                    @endif
                    
                    <a href="{{ route('orders.index') }}" class="block w-full text-center border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar Pesanan
                    </a>
                    
                    <button onclick="window.print()" class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Cetak Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmCancel() {
    Swal.fire({
        title: 'Batalkan Pesanan?',
        text: 'Apakah Anda yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Make AJAX call to cancel the order
            fetch('{{ route("orders.cancel", $order) }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pesanan berhasil dibatalkan',
                        confirmButtonColor: '#ea580c'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    showNotification('error', data.message || 'Gagal membatalkan pesanan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Terjadi kesalahan saat membatalkan pesanan');
            });
        }
    });
}
</script>

@endsection