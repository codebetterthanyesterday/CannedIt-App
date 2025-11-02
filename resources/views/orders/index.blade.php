@extends('layouts.app')

@section('title', 'Riwayat Pesanan - CannedIt')
@section('description', 'Lihat semua riwayat pesanan Anda, lacak status pengiriman, dan kelola pesanan dengan mudah.')

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
                        <span class="ml-4 text-sm font-medium text-gray-900">Riwayat Pesanan</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Riwayat Pesanan</h1>
        <p class="mt-2 text-gray-600">Kelola dan lacak semua pesanan Anda</p>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Pesanan</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Nomor pesanan, produk...">
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Dibayar</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Diterima</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Orders List -->
    @forelse($orders as $order)
        <div class="bg-white shadow rounded-lg mb-6 overflow-hidden">
            <!-- Order Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Pesanan #{{ $order->order_number }}
                            </h3>
                            
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
                            
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $config[0] }} {{ $config[1] }}">
                                <i class="fas {{ $config[2] }} mr-1"></i>
                                {{ $config[3] }}
                            </span>
                        </div>
                        
                        <div class="mt-2 flex flex-col sm:flex-row sm:items-center sm:space-x-6 text-sm text-gray-500">
                            <span><i class="fas fa-calendar-alt mr-1"></i>{{ $order->created_at->format('d M Y, H:i') }}</span>
                            <span><i class="fas fa-box mr-1"></i>{{ $order->orderItems->count() }} item</span>
                            <span><i class="fas fa-money-bill-wave mr-1"></i>{{ ucfirst($order->payment_method) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 md:mt-0 flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Total</p>
                            <p class="text-xl font-bold text-primary-600">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items Preview -->
            <div class="px-6 py-4">
                <div class="flex items-center space-x-4 mb-4">
                    @foreach($order->orderItems->take(3) as $item)
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
                            <img class="w-12 h-12 rounded-lg object-cover border" 
                                 src="{{ asset($item->product->first_image) }}" 
                                 alt="{{ $item->product_name }}"
                                 title="{{ $item->product_name }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 items-center justify-center hidden">
                                <i class="fas fa-box-open text-gray-400"></i>
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                <i class="fas fa-box-open text-gray-400"></i>
                            </div>
                        @endif
                    @endforeach
                    
                    @if($order->orderItems->count() > 3)
                        <div class="w-12 h-12 rounded-lg border border-dashed border-gray-300 flex items-center justify-center">
                            <span class="text-xs text-gray-500">+{{ $order->orderItems->count() - 3 }}</span>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">
                            {{ $order->orderItems->first()->product_name }}
                            @if($order->orderItems->count() > 1)
                                dan {{ $order->orderItems->count() - 1 }} produk lainnya
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Order Actions -->
                <div class="flex flex-wrap items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex flex-wrap gap-2 mb-2 sm:mb-0">
                        <a href="{{ route('orders.show', $order) }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-eye mr-1"></i>
                            Detail
                        </a>
                        
                        @if($order->payment_status === 'pending' && $order->status !== 'cancelled')
                            @if($order->xendit_invoice_url && (!$order->xendit_expired_at || $order->xendit_expired_at->isFuture()))
                                <!-- Active Xendit Invoice -->
                                <a href="{{ $order->xendit_invoice_url }}" target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100">
                                    <i class="fas fa-credit-card mr-1"></i>
                                    Bayar Sekarang
                                </a>
                            @else
                                <!-- Create New Payment -->
                                <form action="{{ route('payment.create', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100">
                                        <i class="fas fa-credit-card mr-1"></i>
                                        Bayar Sekarang
                                    </button>
                                </form>
                            @endif
                        @endif
                        
                        @if(in_array($order->status, ['shipped', 'delivered']))
                            <a href="{{ route('orders.tracking', $order) }}"
                               class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100">
                                <i class="fas fa-truck mr-1"></i>
                                Lacak Paket
                            </a>
                        @endif
                        
                        @if($order->status === 'delivered')
                            <a href="{{ route('orders.review', $order) }}"
                               class="inline-flex items-center px-3 py-1.5 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100">
                                <i class="fas fa-star mr-1"></i>
                                Beri Ulasan
                            </a>
                        @endif
                        
                        @if(in_array($order->status, ['pending', 'paid']))
                            <button type="button" 
                                    class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100"
                                    onclick="confirmCancel({{ $order->id }})">
                                <i class="fas fa-times mr-1"></i>
                                Batalkan
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        @if($order->notes)
                            <span title="{{ $order->notes }}">
                                <i class="fas fa-sticky-note"></i>
                            </span>
                        @endif
                        
                        @if($order->tracking_number)
                            <span title="Nomor Resi: {{ $order->tracking_number }}">
                                <i class="fas fa-shipping-fast"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-gray-100 mb-4">
                <i class="fas fa-shopping-bag text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pesanan</h3>
            <p class="text-gray-500 mb-6">Anda belum memiliki riwayat pesanan. Mulai berbelanja sekarang!</p>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                <i class="fas fa-shopping-cart mr-2"></i>
                Mulai Belanja
            </a>
        </div>
    @endforelse

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="mt-8">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script>
function confirmCancel(orderId) {
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
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang membatalkan pesanan',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX call to cancel the order
            fetch(`/orders/${orderId}/cancel`, {
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal membatalkan pesanan',
                        confirmButtonColor: '#ea580c'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat membatalkan pesanan',
                    confirmButtonColor: '#ea580c'
                });
            });
        }
    });
}
</script>
@endsection