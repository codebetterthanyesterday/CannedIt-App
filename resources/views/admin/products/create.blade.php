@extends('admin.layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Produk Baru</h1>
            <p class="text-gray-600 mt-1">Tambahkan produk kaleng baru ke katalog</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h2>
                
                <div class="space-y-4">
                    <!-- Product Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('name') border-red-500 @enderror" 
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- SKU -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sku" value="{{ old('sku') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('sku') border-red-500 @enderror" 
                               placeholder="Contoh: CLG-001" required>
                        @error('sku')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" rows="5" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('description') border-red-500 @enderror" 
                                  placeholder="Deskripsi lengkap produk..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Short Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Singkat
                        </label>
                        <textarea name="short_description" rows="2" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('short_description') border-red-500 @enderror" 
                                  placeholder="Deskripsi singkat untuk preview...">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Brand -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Brand
                        </label>
                        <input type="text" name="brand" value="{{ old('brand') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('brand') border-red-500 @enderror" 
                               placeholder="Contoh: ABC, Sarden">
                        @error('brand')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Weight -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Berat
                        </label>
                        <input type="text" name="weight" value="{{ old('weight') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('weight') border-red-500 @enderror" 
                               placeholder="Contoh: 250g, 340g, 1kg">
                        @error('weight')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Produk</h2>
                
                <div class="space-y-4">
                    <!-- Ingredients -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bahan / Komposisi
                        </label>
                        <textarea name="ingredients" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('ingredients') border-red-500 @enderror" 
                                  placeholder="Contoh: Jagung manis, air, garam, gula">{{ old('ingredients') }}</textarea>
                        @error('ingredients')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Nutritional Info -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Informasi Nilai Gizi
                        </label>
                        <textarea name="nutritional_info" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('nutritional_info') border-red-500 @enderror" 
                                  placeholder="Contoh: Per 100g: Kalori 86, Protein 3.2g, Karbohidrat 19g">{{ old('nutritional_info') }}</textarea>
                        @error('nutritional_info')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Manufacture Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Produksi
                            </label>
                            <input type="date" name="manufacture_date" value="{{ old('manufacture_date') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('manufacture_date') border-red-500 @enderror">
                            @error('manufacture_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Expiry Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Kadaluarsa
                            </label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('expiry_date') border-red-500 @enderror">
                            @error('expiry_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pricing & Stock -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Harga & Stok</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Normal <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" name="price" value="{{ old('price') }}" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('price') border-red-500 @enderror" 
                                   required step="0.01" min="0">
                        </div>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Sale Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Diskon
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" name="sale_price" value="{{ old('sale_price') }}" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('sale_price') border-red-500 @enderror" 
                                   step="0.01" min="0">
                        </div>
                        @error('sale_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Stock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Stok <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('stock') border-red-500 @enderror" 
                               required min="0">
                        @error('stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Product Images -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Gambar Produk</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Gambar <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="image" accept="image/*" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('image') border-red-500 @enderror" 
                           required onchange="previewImage(event)">
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, WEBP (Max: 2MB)</p>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-4 hidden">
                        <img id="preview" src="" alt="Preview" class="w-48 h-48 object-cover rounded-lg border-2 border-gray-300">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Category -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Kategori</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('category_id') border-red-500 @enderror" 
                            required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Status & Options -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status & Opsi</h2>
                
                <div class="space-y-4">
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('status') border-red-500 @enderror" 
                                required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Featured -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_featured" value="1" 
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-700">Tampilkan sebagai Produk Featured</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <button type="submit" class="w-full px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Produk
                </button>
                <a href="{{ route('admin.products.index') }}" class="block w-full mt-3 px-4 py-3 border border-gray-300 text-center rounded-lg hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection
