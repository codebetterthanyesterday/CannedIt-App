@extends('admin.layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Kelola Kategori</h1>
        <p class="text-gray-600 mt-1">Manage kategori produk makanan kaleng</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-plus mr-2"></i>
        Tambah Kategori
    </a>
</div>

<!-- Categories Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($categories as $category)
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    @if($category->icon)
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                            <i class="{{ $category->icon }} text-primary-600 text-2xl"></i>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tag text-gray-400 text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $category->products_count }} produk</p>
                    </div>
                </div>
            </div>
            
            @if($category->description)
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $category->description }}</p>
            @endif
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <span class="text-xs text-gray-500">
                    <i class="far fa-clock mr-1"></i>
                    {{ $category->created_at->diffForHumans() }}
                </span>
                
                <div class="flex space-x-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="px-3 py-1 text-sm text-primary-600 hover:bg-primary-50 rounded transition-colors">
                        <i class="fas fa-edit"></i>
                        Edit
                    </a>
                    <button onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')" class="px-3 py-1 text-sm text-red-600 hover:bg-red-50 rounded transition-colors">
                        <i class="fas fa-trash"></i>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Kategori</h3>
            <p class="text-gray-600 mb-6">Mulai dengan menambahkan kategori produk pertama</p>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Kategori
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($categories->hasPages())
<div class="mt-6">
    {{ $categories->links() }}
</div>
@endif

@push('scripts')
<script>
function confirmDelete(categoryId, categoryName) {
    Swal.fire({
        title: 'Hapus Kategori?',
        html: `Yakin ingin menghapus kategori <strong>${categoryName}</strong>?<br><small class="text-gray-500">Produk dalam kategori ini tidak akan terhapus</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/categories/${categoryId}`;
            
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
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
