@extends('admin.layouts.app')

@section('title', 'Kelola Reviews')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Kelola Reviews</h1>
    <p class="text-gray-600 mt-1">Manage review produk dari customer</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <input type="text" name="search" placeholder="Cari produk atau user..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <select name="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Semua Rating</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 Bintang)</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4 Bintang)</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3 Bintang)</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ (2 Bintang)</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ (1 Bintang)</option>
            </select>
        </div>
        <div>
            <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Rating Tertinggi</option>
                <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Rating Terendah</option>
            </select>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
            <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Reviews</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">5 Bintang</p>
                <p class="text-2xl font-bold text-green-600">{{ $statistics['rating_5'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">4 Bintang</p>
                <p class="text-2xl font-bold text-blue-600">{{ $statistics['rating_4'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">3 Bintang</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $statistics['rating_3'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">≤ 2 Bintang</p>
                <p class="text-2xl font-bold text-red-600">{{ $statistics['rating_2_or_less'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Reviews List -->
<div class="space-y-4">
    @forelse($reviews as $review)
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-start space-x-4 flex-1">
                <!-- Product Image -->
                @if($review->product->image && file_exists(public_path('storage/' . $review->product->image)))
                    <img src="{{ asset('storage/' . $review->product->image) }}" 
                         alt="{{ $review->product->name }}" 
                         class="w-20 h-20 object-cover rounded-lg"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-20 h-20 rounded-lg bg-gray-100 items-center justify-center flex-col hidden">
                        <i class="fas fa-box-open text-2xl text-gray-400"></i>
                    </div>
                @else
                    <div class="w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center flex-col">
                        <i class="fas fa-box-open text-2xl text-gray-400"></i>
                    </div>
                @endif
                
                <!-- Review Content -->
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $review->product->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $review->product->category->name }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <!-- Rating Stars -->
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @else
                                        <i class="far fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                                <span class="ml-2 text-sm font-semibold text-gray-700">{{ $review->rating }}/5</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Review Text -->
                    <p class="text-gray-700 mb-3">{{ $review->comment }}</p>
                    
                    <!-- Review Meta -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <div class="flex items-center">
                                <img src="{{ $review->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->user->name) }}" 
                                     alt="{{ $review->user->name }}" 
                                     class="w-6 h-6 rounded-full mr-2">
                                <span class="font-medium text-gray-700">{{ $review->user->name }}</span>
                            </div>
                            <span>•</span>
                            <span>{{ $review->created_at->format('d M Y, H:i') }}</span>
                            <span>•</span>
                            <span>{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <!-- Delete Button -->
                        <button onclick="confirmDeleteReview({{ $review->id }}, '{{ addslashes($review->user->name) }}')" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Hapus Review
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-md p-12 text-center text-gray-500">
        <i class="fas fa-star text-6xl mb-4 text-gray-300"></i>
        <p class="text-lg">Tidak ada review ditemukan</p>
        <p class="text-sm mt-2">Review dari customer akan muncul di sini</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $reviews->links() }}
</div>

@push('scripts')
<script>
function confirmDeleteReview(reviewId, userName) {
    Swal.fire({
        title: 'Hapus Review?',
        html: `
            <div class="text-left">
                <p class="mb-3">Yakin ingin menghapus review dari <strong>${userName}</strong>?</p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Peringatan:</strong> Review yang dihapus tidak dapat dikembalikan!
                            </p>
                            <p class="text-xs text-yellow-600 mt-1">
                                Rating produk akan otomatis di-update setelah review dihapus.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i> Ya, Hapus Review!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i> Batal',
        reverseButtons: true,
        customClass: {
            popup: 'swal-wide',
            confirmButton: 'swal-button-spacing',
            cancelButton: 'swal-button-spacing'
        },
        buttonsStyling: true,
        allowOutsideClick: false,
        allowEscapeKey: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve) => {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/reviews/${reviewId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                
                // Submit form
                form.submit();
                resolve();
            });
        }
    });
}

// Show success message if redirected with success flash
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#10b981',
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true
    });
@endif

// Show error message if redirected with error flash
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'OK'
    });
@endif
</script>

<style>
.swal-wide {
    width: 600px !important;
}
.swal-button-spacing {
    padding: 10px 24px !important;
    font-weight: 600 !important;
}
</style>
@endpush
@endsection
