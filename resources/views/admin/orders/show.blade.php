@extends('admin.layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}" class="text-primary-600 hover:text-primary-700">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Pesanan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h2>
                    <p class="text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                @php
                    $statusColors = [
                        'pending' => 'bg-gray-100 text-gray-800',
                        'processing' => 'bg-yellow-100 text-yellow-800',
                        'shipped' => 'bg-purple-100 text-purple-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <!-- Products -->
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-900">Produk</h3>
                @foreach($order->orderItems as $item)
                <div class="flex items-center space-x-4 pb-4 border-b border-gray-200 last:border-0">
                    @php
                        $hasImage = false;
                        if ($item->product) {
                            $imagePath = $item->product->first_image;
                            if ($imagePath && file_exists(public_path($imagePath))) {
                                $hasImage = true;
                            }
                        }
                    @endphp
                    
                    @if($hasImage)
                        <img src="{{ asset($item->product->first_image) }}" 
                             alt="{{ $item->product_name }}" 
                             class="w-16 h-16 object-cover rounded"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-16 h-16 rounded bg-gray-100 items-center justify-center hidden">
                            <i class="fas fa-box-open text-gray-400 text-xl"></i>
                        </div>
                    @else
                        <div class="w-16 h-16 rounded bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-box-open text-gray-400 text-xl"></i>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">{{ $item->product_name }}</h4>
                        <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->shipping_cost > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Ongkir</span>
                    <span class="text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($order->discount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Diskon</span>
                    <span class="text-red-600">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold pt-2 border-t">
                    <span>Total</span>
                    <span class="text-primary-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Customer & Shipping Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi Pengiriman</h3>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Customer</h4>
                    <p class="font-medium text-gray-900">{{ $order->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                    <p class="text-sm text-gray-600">{{ $order->user->phone ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Alamat Pengiriman</h4>
                    <p class="text-sm text-gray-900">{{ $order->shipping_address }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Update Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Update Status</h3>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                    <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                {{-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                    <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="Masukkan nomor resi...">
                </div> --}}

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kurir</label>
                        <select name="shipping_courier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            <option value="">Pilih Kurir</option>
                            <option value="jne" {{ strtolower($order->shipping_courier ?? '') === 'jne' ? 'selected' : '' }}>JNE</option>
                            <option value="pos" {{ strtolower($order->shipping_courier ?? '') === 'pos' ? 'selected' : '' }}>POS Indonesia</option>
                            <option value="tiki" {{ strtolower($order->shipping_courier ?? '') === 'tiki' ? 'selected' : '' }}>TIKI</option>
                            <option value="jnt" {{ strtolower($order->shipping_courier ?? '') === 'jnt' ? 'selected' : '' }}>J&T Express</option>
                            <option value="sicepat" {{ strtolower($order->shipping_courier ?? '') === 'sicepat' ? 'selected' : '' }}>SiCepat</option>
                            <option value="anteraja" {{ strtolower($order->shipping_courier ?? '') === 'anteraja' ? 'selected' : '' }}>AnterAja</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                        <input type="text" name="shipping_service" value="{{ $order->shipping_service }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                               placeholder="REG, YES, ONS, etc...">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" 
                              placeholder="Tambahkan catatan...">{{ $order->notes }}</textarea>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Status
                </button>
            </form>
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Info Pembayaran</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode</span>
                    <span class="font-medium text-gray-900">{{ ucfirst($order->payment_method ?? 'Transfer') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    @if($order->payment_status === 'paid')
                        <span class="text-green-600 font-medium">✓ Lunas</span>
                    @else
                        <span class="text-yellow-600 font-medium">⏱ Pending</span>
                    @endif
                </div>
                @if($order->paid_at)
                <div class="flex justify-between">
                    <span class="text-gray-600">Dibayar</span>
                    <span class="font-medium text-gray-900">{{ $order->paid_at->format('d M Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Timeline</h3>
            <div class="space-y-3">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Order Created</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @if($order->paid_at)
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Payment Confirmed</p>
                        <p class="text-xs text-gray-500">{{ $order->paid_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
