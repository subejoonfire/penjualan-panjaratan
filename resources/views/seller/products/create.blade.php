@extends('layouts.app')

@section('title', 'Tambah Produk Baru - Dashboard Penjual')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tambah Produk Baru</h1>
                    <p class="mt-2 text-gray-600">Buat listing produk baru untuk toko Anda</p>
                </div>
                <a href="{{ route('seller.products.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Produk
                </a>
            </div>
        </div>

        <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-8">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Dasar</h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Product Name -->
                    <div>
                        <label for="productname" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="productname" id="productname" value="{{ old('productname') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Masukkan nama produk">
                        @error('productname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="idcategories" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="idcategories" id="idcategories" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('idcategories')==$category->id ? 'selected' : ''
                                }}>
                                {{ $category->category }}
                            </option>
                            @endforeach
                        </select>
                        @error('idcategories')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="productdescription" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="productdescription" id="productdescription" rows="4" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Deskripsikan produk Anda secara detail...">{{ old('productdescription') }}</textarea>
                        @error('productdescription')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Harga & Stok</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price -->
                        <div>
                            <label for="productprice" class="block text-sm font-medium text-gray-700 mb-2">
                                Harga (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="productprice" id="productprice"
                                    value="{{ old('productprice') }}" required min="0" step="0.01"
                                    class="w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="0.00">
                            </div>
                            @error('productprice')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label for="productstock" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Stok <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="productstock" id="productstock" value="{{ old('productstock') }}" required min="0"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="0">
                            @error('productstock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Gambar Produk</h3>
                    <p class="mt-1 text-sm text-gray-600">Unggah gambar produk untuk menampilkan produk Anda. Gambar pertama
                        akan menjadi gambar utama.</p>
                </div>
                <div class="p-6">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
                            <div class="flex text-sm text-gray-600">
                                <label for="images"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Unggah gambar</span>
                                    <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*" required>
                                </label>
                                <p class="pl-1">atau seret dan lepas</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF maksimal 2MB per gambar</p>
                        </div>
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 hidden">
                        <!-- Images will be previewed here -->
                    </div>

                    @error('images')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('seller.products.index') }}"
                    class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Buat Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        imagePreview.innerHTML = '';
        
        if (files.length > 0) {
            imagePreview.classList.remove('hidden');
            
            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imageDiv = document.createElement('div');
                        imageDiv.className = 'relative';
                        imageDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Pratinjau ${index + 1}" class="w-full h-24 object-cover rounded-md border">
                            ${index === 0 ? '<span class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-2 py-1 rounded">Utama</span>' : ''}
                        `;
                        imagePreview.appendChild(imageDiv);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            imagePreview.classList.add('hidden');
        }
    });

    // Price formatting
    const priceInput = document.getElementById('productprice');
    priceInput.addEventListener('input', function(e) {
        let value = e.target.value;
        // Remove any non-numeric characters except decimal point
        value = value.replace(/[^\d.]/g, '');
        // Ensure only one decimal point
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        e.target.value = value;
    });
});
</script>
@endsection