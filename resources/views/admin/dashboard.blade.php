@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
    <p class="mt-2 text-gray-600">Kelola produk, pesanan, dan statistik toko Anda</p>
</div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Pendapatan</p>
                    <p class="text-3xl font-bold mt-2">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                    
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Pesanan</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalOrders ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Produk</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalProducts ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-boxes text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Pelanggan</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalCustomers ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Orders -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-shopping-bag text-primary-600 mr-2"></i>
                    Pesanan Terbaru
                </h2>
                <a href="{{ route('orders.index') }}" class="text-sm text-primary-600 hover:text-primary-700">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentOrders ?? [] as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">#{{ $order->order_number }}</h4>
                            <p class="text-xs text-gray-500">{{ $order->customer_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">Belum ada pesanan</p>
                @endforelse
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-star text-primary-600 mr-2"></i>
                    Produk Terlaris
                </h2>
                <a href="{{ route('products.index') }}" class="text-sm text-primary-600 hover:text-primary-700">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="space-y-4">
                @forelse($topProducts ?? [] as $product)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        @if($product->first_image)
                            <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-12 h-12 rounded object-cover">
                        @else
                            <div class="w-12 h-12 rounded bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-box-open text-gray-400 text-lg"></i>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-primary-600">{{ $product->sales_count ?? 0 }}</p>
                            <p class="text-xs text-gray-500">terjual</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">Belum ada data penjualan</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-bolt text-primary-600 mr-2"></i>
            Aksi Cepat
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.products.create') }}" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-all group">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-primary-500 transition-colors">
                    <i class="fas fa-plus text-primary-600 text-xl group-hover:text-white"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-primary-700">Tambah Produk</span>
            </a>
            
            <a href="{{ route('admin.categories.create') }}" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all group">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-blue-500 transition-colors">
                    <i class="fas fa-folder-plus text-blue-600 text-xl group-hover:text-white"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700">Tambah Kategori</span>
            </a>
            
            <a href="{{ route('admin.orders.index') }}?status=pending" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-all group">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-yellow-500 transition-colors">
                    <i class="fas fa-clock text-yellow-600 text-xl group-hover:text-white"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-yellow-700">Pesanan Pending</span>
            </a>
            
            <a href="{{ route('admin.products.index') }}?stock=low" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-red-500 transition-colors">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl group-hover:text-white"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Stok Menipis</span>
            </a>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-chart-line text-primary-600 mr-2"></i>
                Grafik Penjualan
            </h2>
            <div class="flex space-x-2">
                <button onclick="updateChart('7days')" class="px-3 py-1 text-xs rounded-md bg-primary-600 text-white hover:bg-primary-700 transition" id="btn-7days">
                    7 Hari
                </button>
                <button onclick="updateChart('30days')" class="px-3 py-1 text-xs rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition" id="btn-30days">
                    30 Hari
                </button>
                <button onclick="updateChart('12months')" class="px-3 py-1 text-xs rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition" id="btn-12months">
                    12 Bulan
                </button>
            </div>
        </div>
        
        <div class="relative h-80">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let salesChart;
let currentPeriod = '7days';

// Data dari controller
const chartData = {
    '7days': {
        labels: @json($sevenDaysData['labels']),
        data: @json($sevenDaysData['data'])
    },
    '30days': {
        labels: @json($thirtyDaysData['labels']),
        data: @json($thirtyDaysData['data'])
    },
    '12months': {
        labels: @json($twelveMonthsData['labels']),
        data: @json($twelveMonthsData['data'])
    }
};

function initChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData['7days'].labels,
            datasets: [{
                label: 'Pendapatan',
                data: chartData['7days'].data,
                borderColor: '#ea580c',
                backgroundColor: 'rgba(234, 88, 12, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#ea580c',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#ea580c',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 13,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 14
                    },
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000) + 'jt';
                            }
                            return 'Rp ' + (value / 1000) + 'rb';
                        },
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

function updateChart(period) {
    currentPeriod = period;
    
    // Update button styles
    document.querySelectorAll('[id^="btn-"]').forEach(btn => {
        btn.classList.remove('bg-primary-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    document.getElementById('btn-' + period).classList.remove('bg-gray-200', 'text-gray-700');
    document.getElementById('btn-' + period).classList.add('bg-primary-600', 'text-white');
    
    // Update chart data
    salesChart.data.labels = chartData[period].labels;
    salesChart.data.datasets[0].data = chartData[period].data;
    salesChart.update('active');
}

// Initialize chart when page loads
document.addEventListener('DOMContentLoaded', function() {
    initChart();
});
</script>
@endpush
@endsection