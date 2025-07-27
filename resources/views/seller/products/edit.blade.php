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
    
    /* Drag and Drop Styles */
    #dropArea {
        transition: all 0.3s ease;
    }
    
    #dropArea.dragover {
        border-color: #3B82F6 !important;
        background-color: #EFF6FF !important;
        transform: scale(1.02);
    }
    
    .upload-preview {
        transition: all 0.3s ease;
    }
    
    .upload-preview:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .file-remove-btn {
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .upload-preview:hover .file-remove-btn {
        opacity: 1;
    }
    
    /* Animation for new uploads */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .upload-preview {
        animation: fadeInUp 0.4s ease;
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
                            <input type="number" name="productprice" id="productprice" min="0" step="any"
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
                            <button type="button" disabled
                                class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded z-10 image-overlay-button cursor-default">
                                Utama
                            </button>
                            @else
                            <button type="button" onclick="setPrimaryImage({{ $image->id }})"
                                class="absolute top-2 left-2 bg-gray-600 text-white text-xs px-2 py-1 rounded hover:bg-blue-700 z-10 cursor-pointer image-overlay-button focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Jadikan Utama
                            </button>
                            @endif
                            <button type="button" onclick="confirmDeleteImage({{ $image->id }})" 
                                class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700 z-10 cursor-pointer image-overlay-button focus:outline-none focus:ring-2 focus:ring-red-500">
                                Hapus
                            </button>
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
                    <!-- Upload Info -->
                    <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium">Info Upload Gambar:</p>
                                <ul class="mt-1 space-y-1">
                                    <li>• Maksimal 5 gambar baru (total maksimal 6 dengan gambar utama)</li>
                                    <li>• Format: JPG, PNG, GIF</li>
                                    <li>• Ukuran maksimal: 2MB per gambar</li>
                                    <li>• Gambar saat ini: <span id="currentImageCount">{{ $product->images->count() }}</span></li>
                                    <li id="remainingSlotsInfo">• Sisa slot untuk gambar baru: <span id="remainingSlots">{{ 6 - $product->images->count() }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tambah Gambar Produk
                        </label>
                        
                        <!-- Hidden file input -->
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">
                        
                        <!-- Drag and Drop Area -->
                        <div id="dropArea" class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors duration-300 bg-gray-50 hover:bg-blue-50">
                            <div id="dropContent">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Drag & Drop gambar di sini</h3>
                                <p class="text-sm text-gray-600 mb-4">atau</p>
                                <button type="button" id="browseBtn" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-folder-open mr-2"></i>
                                    Pilih Gambar
                                </button>
                                <p class="text-xs text-gray-500 mt-2">
                                    Maksimal 5 gambar, ukuran masing-masing maksimal 2MB
                                </p>
                            </div>
                            
                            <!-- Upload Progress -->
                            <div id="uploadProgress" class="hidden">
                                <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
                                <p class="text-sm text-blue-600">Memproses gambar...</p>
                            </div>
                        </div>
                        
                        @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- Selected Files Info -->
                        <div id="selectedFilesInfo" class="mt-2 hidden">
                            <p class="text-sm text-gray-600">
                                <span id="selectedCount">0</span> gambar dipilih
                            </p>
                        </div>
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4"></div>
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
    // Drag and Drop Upload Implementation
    const dropArea = document.getElementById('dropArea');
    const dropContent = document.getElementById('dropContent');
    const uploadProgress = document.getElementById('uploadProgress');
    const fileInput = document.getElementById('images');
    const browseBtn = document.getElementById('browseBtn');
    const preview = document.getElementById('imagePreview');
    const selectedFilesInfo = document.getElementById('selectedFilesInfo');
    const selectedCount = document.getElementById('selectedCount');
    
    let selectedFiles = [];
    const maxFiles = 5;
    const maxFileSize = 2 * 1024 * 1024; // 2MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    
    // Browse button click
    browseBtn.addEventListener('click', () => {
        fileInput.click();
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
    
    // Drag and drop events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        dropArea.classList.add('border-blue-500', 'bg-blue-50', 'dragover');
        dropArea.classList.remove('border-gray-300');
    }
    
    function unhighlight() {
        dropArea.classList.remove('border-blue-500', 'bg-blue-50', 'dragover');
        dropArea.classList.add('border-gray-300');
    }
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }
    
    function handleFiles(files) {
        const fileArray = Array.from(files);
        const validFiles = [];
        const errors = [];
        
        // Get current image count
        const currentImageCount = parseInt(document.getElementById('currentImageCount').textContent || '0');
        const totalAllowed = 6; // Maximum total images
        const remainingSlots = totalAllowed - currentImageCount - selectedFiles.length;
        
        // Validate files
        fileArray.forEach((file, index) => {
            if (!allowedTypes.includes(file.type)) {
                errors.push(`File "${file.name}" bukan format gambar yang valid`);
                return;
            }
            
            if (file.size > maxFileSize) {
                errors.push(`File "${file.name}" terlalu besar (maksimal 2MB)`);
                return;
            }
            
            if (validFiles.length >= remainingSlots) {
                errors.push(`Tidak bisa menambah lebih banyak gambar. Sisa slot: ${remainingSlots}`);
                return;
            }
            
            if (selectedFiles.length + validFiles.length >= maxFiles) {
                errors.push(`Maksimal ${maxFiles} gambar baru yang bisa dipilih`);
                return;
            }
            
            validFiles.push(file);
        });
        
        // Show errors if any
        if (errors.length > 0) {
            showAlert(errors.join('<br>'), 'error');
            return;
        }
        
        // Add valid files
        selectedFiles = [...selectedFiles, ...validFiles];
        
        // Update file input with selected files
        updateFileInput();
        
        // Update UI
        updateSelectedInfo();
        updatePreview();
        
        if (selectedFiles.length > 0) {
            showAlert(`${validFiles.length} gambar berhasil ditambahkan`, 'success');
        }
    }
    
    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }
    
    function updateSelectedInfo() {
        if (selectedFiles.length > 0) {
            selectedFilesInfo.classList.remove('hidden');
            selectedCount.textContent = selectedFiles.length;
        } else {
            selectedFilesInfo.classList.add('hidden');
        }
        
        // Update remaining slots info
        const currentImageCount = parseInt(document.getElementById('currentImageCount').textContent || '0');
        const remainingSlots = 6 - currentImageCount - selectedFiles.length;
        const remainingSlotsElement = document.getElementById('remainingSlots');
        if (remainingSlotsElement) {
            remainingSlotsElement.textContent = remainingSlots;
            
                         // Change color based on remaining slots
            const remainingSlotsInfo = document.getElementById('remainingSlotsInfo');
            if (remainingSlots <= 0) {
                remainingSlotsInfo.className = 'text-red-600 font-medium';
                // Disable drop area
                dropArea.classList.add('opacity-50', 'pointer-events-none');
                browseBtn.disabled = true;
                browseBtn.textContent = 'Maksimal Gambar Tercapai';
            } else if (remainingSlots <= 2) {
                remainingSlotsInfo.className = 'text-yellow-600 font-medium';
                // Enable drop area
                dropArea.classList.remove('opacity-50', 'pointer-events-none');
                browseBtn.disabled = false;
                browseBtn.innerHTML = '<i class="fas fa-folder-open mr-2"></i>Pilih Gambar';
            } else {
                remainingSlotsInfo.className = 'text-blue-800';
                // Enable drop area
                dropArea.classList.remove('opacity-50', 'pointer-events-none');
                browseBtn.disabled = false;
                browseBtn.innerHTML = '<i class="fas fa-folder-open mr-2"></i>Pilih Gambar';
            }
        }
    }
    
    function updatePreview() {
        preview.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'upload-preview relative group bg-white rounded-lg shadow-md overflow-hidden';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}" 
                         class="w-full h-32 object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full shadow">
                            <i class="fas fa-plus mr-1"></i>Baru
                        </span>
                    </div>
                    <div class="absolute top-2 left-2 file-remove-btn">
                        <button type="button" onclick="removeFile(${index})" 
                                class="bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center hover:bg-red-700 shadow-lg transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white p-3">
                        <p class="text-xs truncate font-medium">${file.name}</p>
                        <p class="text-xs text-gray-300">${formatFileSize(file.size)}</p>
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
    
    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updateFileInput();
        updateSelectedInfo();
        updatePreview();
        showAlert('Gambar berhasil dihapus', 'info');
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Make removeFile function global
    window.removeFile = removeFile;
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSelectedInfo(); // Update initial state
    });

    function confirmDeleteImage(imageId) {
        confirmAction('Apakah Anda yakin ingin menghapus gambar ini?', function() {
            // Update current image count
            const currentCount = document.getElementById('currentImageCount');
            if (currentCount) {
                const newCount = parseInt(currentCount.textContent) - 1;
                currentCount.textContent = newCount;
                
                // Update remaining slots
                updateSelectedInfo();
            }
            
            // Create a form dynamically to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/seller/products/images/${imageId}`;
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add method field for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            console.log('Deleting image with form action:', form.action);
            console.log('Delete form method:', form.method);
            
            // Submit the form
            document.body.appendChild(form);
            form.submit();
            
            // Show loading indicator
            showAlert('Menghapus gambar...', 'info');
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

    // Form submission handling
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="products"][action*="update"]');
        console.log('Main form found:', form);
        
        if (form) {
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            
            form.addEventListener('submit', function(e) {
                console.log('Main form is being submitted');
                console.log('Form action at submit:', this.action);
                console.log('Form method at submit:', this.method);
                
                // Check for method override
                const methodInput = this.querySelector('input[name="_method"]');
                console.log('Method override:', methodInput ? methodInput.value : 'none');
                
                // Basic validation
                const productName = document.getElementById('productname');
                const productPrice = document.getElementById('productprice');
                const productStock = document.getElementById('productstock');
                const category = document.getElementById('idcategories');
                
                if (!productName || !productName.value.trim()) {
                    e.preventDefault();
                    showAlert('Nama produk harus diisi', 'error');
                    return false;
                }
                
                if (!productPrice || !productPrice.value || parseFloat(productPrice.value) <= 0) {
                    e.preventDefault();
                    showAlert('Harga produk harus diisi dan lebih dari 0', 'error');
                    return false;
                }
                
                if (!productStock || productStock.value === '' || parseInt(productStock.value) < 0) {
                    e.preventDefault();
                    showAlert('Stok produk harus diisi dan tidak boleh negatif', 'error');
                    return false;
                }
                
                if (!category || !category.value) {
                    e.preventDefault();
                    showAlert('Kategori harus dipilih', 'error');
                    return false;
                }
                
                console.log('Form validation passed, allowing submission...');
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
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
        
        // Log info about dynamic forms
        console.log('Using dynamic forms for image operations to prevent conflicts');
    });
</script>
@endsection