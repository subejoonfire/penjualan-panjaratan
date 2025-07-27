@extends('layouts.app')

@section('title', 'Edit Produk - Dashboard Penjual')

@push('styles')
<style>
    .image-overlay-button {
        transition: all 0.2s ease;
        backdrop-filter: blur(4px);
    }
    .image-overlay-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .group:hover .image-overlay-button {
        opacity: 1;
    }
</style>
@endpush

@section('content')
@include('components.modal-notification')

<!-- Session Messages -->
@if(session('success'))
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    </div>
@endif

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
                                            <img src="{{ url('storage/' . $product->images->where('is_primary', true)->first()?->image ?? $product->images->first()?->image) }}"
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
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $product->is_active) == '1' || old('is_active', $product->is_active) === true ? 'checked' : '' }}
                                class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="is_active" value="0" {{ old('is_active', $product->is_active) == '0' || old('is_active', $product->is_active) === false ? 'checked' : '' }}
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
                            <img src="{{ url('storage/' . $image->image) }}" alt="Gambar Produk"
                                class="w-full h-32 object-cover rounded-lg border border-gray-200">
                            @if($image->is_primary)
                            <span
                                class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded z-10 image-overlay-button">Utama</span>
                            @else
                            <button type="button" onclick="setPrimaryImage({{ $image->id }})"
                                class="absolute top-2 left-2 bg-gray-600 text-white text-xs px-2 py-1 rounded hover:bg-blue-700 z-10 cursor-pointer image-overlay-button focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Jadikan Utama
                            </button>
                            @endif
                            <form action="{{ route('seller.products.images.delete', $image) }}" method="POST" 
                                style="display: inline;" id="deleteImageForm{{ $image->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDeleteImage({{ $image->id }})" 
                                    class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700 z-10 cursor-pointer image-overlay-button focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Hapus
                                </button>
                            </form>
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
                        div.className = 'relative group';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Pratinjau ${index + 1}" 
                                 class="w-full h-32 object-cover rounded-lg border border-gray-200">
                            <div class="absolute top-2 right-2">
                                <span class="bg-green-600 text-white text-xs px-2 py-1 rounded">Baru</span>
                            </div>
                            <div class="absolute bottom-2 left-2">
                                <span class="bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                    ${file.name}
                                </span>
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

    function confirmDeleteImage(imageId) {
        confirmAction('Apakah Anda yakin ingin menghapus gambar ini?', function() {
            document.getElementById('deleteImageForm' + imageId).submit();
        });
    }

    function setPrimaryImage(imageId) {
        confirmAction('Jadikan gambar ini sebagai gambar utama?', function() {
            // Create a form dynamically to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/seller/products/images/${imageId}/primary`;
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add method field for PUT
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);
            
            // Submit the form
            document.body.appendChild(form);
            form.submit();
            
            // Show loading indicator
            showAlert('Mengubah gambar utama...', 'info');
        });
    }

    // Debug form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="products"][action*="update"]');
        console.log('Form found:', form);
        
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form is being submitted');
                
                // Basic validation
                const productName = document.getElementById('productname').value.trim();
                const productPrice = document.getElementById('productprice').value;
                const productStock = document.getElementById('productstock').value;
                const category = document.getElementById('idcategories').value;
                
                console.log('Validation data:', {
                    productName, 
                    productPrice, 
                    productStock, 
                    category
                });
                
                if (!productName) {
                    e.preventDefault();
                    showAlert('Nama produk harus diisi', 'error');
                    return false;
                }
                
                if (!productPrice || parseFloat(productPrice) <= 0) {
                    e.preventDefault();
                    showAlert('Harga produk harus diisi dan lebih dari 0', 'error');
                    return false;
                }
                
                if (productStock === '' || parseInt(productStock) < 0) {
                    e.preventDefault();
                    showAlert('Stok produk harus diisi dan tidak boleh negatif', 'error');
                    return false;
                }
                
                if (!category) {
                    e.preventDefault();
                    showAlert('Kategori harus dipilih', 'error');
                    return false;
                }
                
                console.log('Form validation passed, submitting...');
                
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                    submitBtn.disabled = true;
                    
                    // Enable back if there's an error (form will reload if successful)
                    setTimeout(() => {
                        if (submitBtn) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    }, 10000);
                }
                
                return true; // Allow form submission
            });
        }
    });
</script>
@endsection