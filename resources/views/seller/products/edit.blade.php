@extends('layouts.app')

@section('title', 'Edit Produk - Dashboard Penjual')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Produk</h1>
                    <p class="mt-2 text-gray-600">Perbarui informasi produk Anda</p>
                </div>
                <a href="{{ route('seller.products.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Produk
                </a>
            </div>
        </div>

        <!-- Current Product Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    @if($product->images->count() > 0)
                    <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                        alt="{{ $product->productname }}" class="h-16 w-16 rounded-lg object-cover">
                    @else
                    <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-xl"></i>
                    </div>
                    @endif
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-blue-900">{{ $product->productname }}</h3>
                    <p class="text-sm text-blue-700">
                        {{ $product->category->category }} •
                        Dibuat {{ $product->created_at->format('d M Y') }} •
                        {{ $product->images->count() }} gambar
                    </p>
                    <div class="mt-1 flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                        <span class="text-sm text-blue-600 font-medium">Rp {{ number_format($product->productprice)
                            }}</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Dasar</h3>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <!-- Product Name -->
                    <div>
                        <label for="productname" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="productname" id="productname"
                            value="{{ old('productname', $product->productname) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                      @error('productname') border-red-300 @enderror">
                        @error('productname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="idcategories" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="idcategories" id="idcategories" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                       @error('idcategories') border-red-300 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('idcategories', $product->idcategories) ==
                                $category->id ? 'selected' : '' }}>
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
                            Deskripsi Produk <span class="text-red-500">*</span>
                        </label>
                        <textarea name="productdescription" id="productdescription" rows="4" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                         @error('productdescription') border-red-300 @enderror"
                            placeholder="Deskripsikan produk Anda...">{{ old('productdescription', $product->productdescription) }}</textarea>
                        @error('productdescription')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Harga & Stok</h3>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price -->
                        <div>
                            <label for="productprice" class="block text-sm font-medium text-gray-700 mb-2">
                                Harga (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="productprice" id="productprice" min="0" step="1000"
                                value="{{ old('productprice', $product->productprice) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('productprice') border-red-300 @enderror" placeholder="0">
                            @error('productprice')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label for="productstock" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Stok <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="productstock" id="productstock" min="0"
                                value="{{ old('productstock', $product->productstock) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('productstock') border-red-300 @enderror" placeholder="0">
                            @error('productstock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Produk</label>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $product->is_active)
                                == 1 ? 'checked' : '' }}
                                class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="is_active" value="0" {{ old('is_active', $product->is_active)
                                == 0 ? 'checked' : '' }}
                                class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Tidak Aktif</span>
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Produk tidak aktif tidak akan terlihat oleh pelanggan
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current Images -->
            @if($product->images->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Gambar Saat Ini</h3>
                    <p class="text-sm text-gray-600">{{ $product->images->count() }} gambar telah diunggah</p>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($product->images as $image)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $image->image) }}" alt="Gambar Produk"
                                class="w-full h-32 object-cover rounded-lg border border-gray-200">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" onclick="deleteImage({{ $image->id }})"
                                    class="text-white hover:text-red-300">
                                    <i class="fas fa-trash text-lg"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Add New Images -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Tambah Gambar Baru</h3>
                    <p class="text-sm text-gray-600">Unggah gambar produk tambahan (opsional)</p>
                </div>
                <div class="px-6 py-6">
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                            Gambar Produk
                        </label>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                      @error('images') border-red-300 @enderror">
                        @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Pilih beberapa gambar (JPG, PNG, maksimal 2MB per gambar)
                        </p>
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 hidden"></div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-eye mr-1"></i>
                        Pratinjau Produk
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('seller.products.index') }}"
                        class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Perbarui Produk
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    const files = e.target.files;

    preview.innerHTML = '';
    
    if (files.length > 0) {
        preview.classList.remove('hidden');
        
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Pratinjau ${index + 1}" 
                             class="w-full h-32 object-cover rounded-lg border border-gray-200">
                        <div class="absolute top-2 right-2">
                            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded">Baru</span>
                        </div>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        preview.classList.add('hidden');
    }
});

function deleteImage(imageId) {
                    confirmAction('Apakah Anda yakin ingin menghapus gambar ini?', function() {
        // In a real application, you would make an AJAX call here
        fetch(`/seller/products/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menghapus gambar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menghapus gambar');
        });
    }
}
</script>
@endsection