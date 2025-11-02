@extends('admin.layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.categories.index') }}" class="text-primary-600 hover:text-primary-700">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Kategori
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Kategori</h1>
        
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                           placeholder="Contoh: Sayuran Kaleng" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Icon -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Icon (Font Awesome Class)
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('icon') border-red-500 @enderror" 
                               placeholder="Contoh: fas fa-leaf">
                        @if($category->icon)
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="{{ $category->icon }} text-primary-600 text-2xl"></i>
                            </div>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Cari icon di <a href="https://fontawesome.com/icons" target="_blank" class="text-primary-600 hover:underline">Font Awesome</a>
                    </p>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                              placeholder="Deskripsi singkat kategori...">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium mb-1">Informasi</p>
                            <p>Kategori ini memiliki <strong>{{ $category->products()->count() }} produk</strong></p>
                            <p class="text-xs mt-1">Dibuat: {{ $category->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Kategori
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
