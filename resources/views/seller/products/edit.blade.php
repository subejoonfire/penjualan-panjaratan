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
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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
@php
$hasPrimaryImage = $product->images->where('is_primary', true)->count() > 0;
$sortedImages = $product->images->sortByDesc('is_primary');
@endphp
@include('components.modal-notification')
<div class="py-3 sm:py-6">
    <div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-4 sm:mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Edit Produk</h1>
                    <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Perbarui informasi produk Anda</p>
                </div>
                <a href="{{ route('seller.products.index') }}"
                    class="bg-gray-600 text-white px-2 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-gray-700 text-xs sm:text-sm">
                    <i class="fas fa-arrow-left mr-1 sm:mr-2"></i>
                    <span class="hidden sm:inline">Kembali ke Produk</span>
                    <span class="sm:hidden">Kembali</span>
                </a>
            </div>
        </div>

        <!-- Current Product Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 sm:h-16 sm:w-16">
                    @if($product->images->count() > 0)
                    <img src="{{ url('storage/' . $product->images->where('is_primary', true)->first()?->image ?? $product->images->first()?->image) }}"
                        alt="{{ $product->productname }}" class="h-12 w-12 sm:h-16 sm:w-16 rounded-lg object-cover">
                    @else
                    <div class="h-12 w-12 sm:h-16 sm:w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-base sm:text-xl"></i>
                    </div>
                    @endif
                </div>
                <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                    <h3 class="text-sm sm:text-base md:text-lg font-medium text-blue-900 truncate">{{
                        $product->productname }}</h3>
                    <p class="text-xs sm:text-sm text-blue-700">
                        {{ $product->category->category }} •
                        Dibuat {{ $product->created_at->format('d M Y') }} •
                        {{ $product->images->count() }} gambar
                    </p>
                    <div class="mt-1 flex items-center space-x-2">
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium
                            {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                        <span class="text-xs sm:text-sm text-blue-600 font-medium">Rp {{
                            number_format($product->productprice)
                            }}</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data"
            class="space-y-4 sm:space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Informasi Dasar</h3>
                </div>
                <div class="px-3 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6">
                    <!-- Product Name -->
                    <div>
                        <label for="productname"
                            class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="productname" id="productname"
                            value="{{ old('productname', $product->productname) }}" required class="w-full text-sm sm:text-base border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                      @error('productname') border-red-300 @enderror">
                        @error('productname')
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="idcategories"
                            class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="idcategories" id="idcategories" required class="w-full text-sm sm:text-base border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
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
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="productdescription"
                            class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                            Deskripsi Produk <span class="text-red-500">*</span>
                        </label>
                        <textarea name="productdescription" id="productdescription" rows="4" required class="w-full text-sm sm:text-base border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                         @error('productdescription') border-red-300 @enderror"
                            placeholder="Deskripsikan produk Anda...">{{ old('productdescription', $product->productdescription) }}</textarea>
                        @error('productdescription')
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Harga & Stok</h3>
                </div>
                <div class="px-3 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Price -->
                        <div>
                            <label for="productprice"
                                class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Harga (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="productprice" id="productprice" min="0" step="any"
                                value="{{ old('productprice', $product->productprice) }}" required class="w-full text-sm sm:text-base border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('productprice') border-red-300 @enderror" placeholder="0">
                            @error('productprice')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label for="productstock"
                                class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Jumlah Stok <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="productstock" id="productstock" min="0"
                                value="{{ old('productstock', $product->productstock) }}" required class="w-full text-sm sm:text-base border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('productstock') border-red-300 @enderror" placeholder="0">
                            @error('productstock')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Status
                            Produk</label>
                        <div class="flex items-center space-x-4 sm:space-x-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="is_active" value="1" {{ (old('is_active', $product->is_active
                                ? '1' : '0') == '1') ? 'checked' : '' }}
                                class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-xs sm:text-sm text-gray-700">Aktif</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="is_active" value="0" {{ (old('is_active', $product->is_active
                                ? '1' : '0') == '0') ? 'checked' : '' }}
                                class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-xs sm:text-sm text-gray-700">Tidak Aktif</span>
                            </label>
                        </div>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">
                            Produk tidak aktif tidak akan terlihat oleh pelanggan
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current Images -->
            @if($product->images->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Gambar Saat Ini</h3>
                    <p class="text-xs sm:text-sm text-gray-600">{{ $product->images->count() }} gambar telah diunggah
                    </p>
                </div>
                <div class="px-3 sm:px-6 py-4 sm:py-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($sortedImages as $image)
                        <div class="relative group">
                            <img src="{{ url('storage/' . $image->image) }}" alt="Gambar Produk"
                                class="w-full h-24 sm:h-32 object-cover rounded-lg border border-gray-200">
                            @if($image->is_primary)
                            <button type="button" disabled
                                class="absolute top-1 sm:top-2 left-1 sm:left-2 bg-blue-600 text-white text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded z-10 image-overlay-button cursor-default">
                                Utama
                            </button>
                            @else
                            <button type="button" onclick="setPrimaryImage({{ $image->id }})"
                                class="absolute top-1 sm:top-2 left-1 sm:left-2 bg-gray-600 text-white text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded hover:bg-blue-700 z-10 cursor-pointer image-overlay-button focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="hidden sm:inline">Jadikan Utama</span>
                                <span class="sm:hidden">Utama</span>
                            </button>
                            @endif
                            <button type="button" onclick="deleteImage({{ $image->id }})"
                                class="absolute top-1 sm:top-2 right-1 sm:right-2 bg-red-600 text-white text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded hover:bg-red-700">
                                <span class="hidden sm:inline">Hapus</span>
                                <i class="sm:hidden fas fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Add New Images -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Tambah Gambar Baru</h3>
                    <p class="text-xs sm:text-sm text-gray-600">Unggah gambar produk tambahan (opsional)</p>
                </div>
                <div class="px-3 sm:px-6 py-4 sm:py-6">
                    <!-- Upload Info -->
                    <div class="mb-3 sm:mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5 text-sm sm:text-base"></i>
                            <div class="text-xs sm:text-sm text-blue-800">
                                <p class="font-medium">Info Upload Gambar:</p>
                                <ul class="mt-1 space-y-1">
                                    <li>• Maksimal 5 gambar baru (total maksimal 6 dengan gambar utama)</li>
                                    <li>• Format: JPG, PNG, GIF</li>
                                    <li>• Ukuran maksimal: 2MB per gambar</li>
                                    @if($hasPrimaryImage)
                                    <li>• Gambar utama sudah ada</li>
                                    @else
                                    <li>• Gambar pertama akan menjadi gambar utama</li>
                                    @endif
                                    <li>• Gambar saat ini: <span id="currentImageCount">{{ $product->images->count()
                                            }}</span></li>
                                    <li id="remainingSlotsInfo">• Sisa slot untuk gambar baru: <span
                                            id="remainingSlots">{{ 6 - $product->images->count() }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="images" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-3">
                            Upload Gambar Tambahan
                        </label>

                        <!-- File Upload Area -->
                        <div id="dropArea"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-4 sm:p-6 hover:border-blue-400 transition-colors">
                            <div id="dropContent" class="text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl sm:text-3xl mb-2 sm:mb-3"></i>
                                <div class="flex text-xs sm:text-sm text-gray-600 justify-center">
                                    <label for="images"
                                        class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Pilih file</span>
                                        <input type="file" name="images[]" id="images" multiple accept="image/*"
                                            class="sr-only">
                                    </label>
                                    <p class="pl-1">atau seret dan lepas</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF maksimal 2MB per gambar</p>
                            </div>
                        </div>

                        <!-- Upload Info Box -->
                        <div id="selectedFilesInfo"
                            class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                    <span class="text-sm text-blue-800">
                                        <span id="selectedCount">0</span> gambar dipilih
                                    </span>
                                </div>
                                <button type="button" onclick="clearAllFiles()"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    Hapus Semua
                                </button>
                            </div>
                        </div>

                        @error('images')
                        <p class="mt-2 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-4 sm:mt-6 hidden">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Pratinjau Gambar Baru (Seret untuk
                            mengurutkan)</h4>
                        <div id="sortableImages" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                            <!-- Images will be previewed here -->
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Gambar pertama akan menjadi gambar utama produk</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                <div class="flex items-center space-x-2 sm:space-x-4 w-full sm:w-auto">
                    <a href="{{ route('products.show', $product) }}"
                        class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm text-center">
                        <i class="fas fa-eye mr-1"></i>
                        Pratinjau Produk
                    </a>
                </div>
                <div
                    class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                    <a href="{{ route('seller.products.index') }}"
                        class="w-full sm:w-auto bg-gray-300 text-gray-700 px-4 sm:px-6 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 text-center text-sm">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <i class="fas fa-save mr-1 sm:mr-2"></i>
                        Perbarui Produk
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Include Modal Notification Component -->
@include('components.modal-notification')

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Drag and Drop Upload Implementation

document.addEventListener('DOMContentLoaded', function() {
    // Prevent default drag behaviors on document level
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        document.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
        }, false);
    });

    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('images');
    const preview = document.getElementById('imagePreview');
    const sortableImages = document.getElementById('sortableImages');
    const selectedFilesInfo = document.getElementById('selectedFilesInfo');
    const selectedCount = document.getElementById('selectedCount');
    
    let selectedFiles = [];
    const maxFiles = 5;
    const maxFileSize = 2 * 1024 * 1024; // 2MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    const hasPrimaryImage = {{ $hasPrimaryImage ? 'true' : 'false' }};

    // Initialize Sortable
    let sortable;
    
    function initializeSortable() {
        if (sortable) {
            sortable.destroy();
        }
        
        sortable = Sortable.create(sortableImages, {
            animation: 300,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            swapThreshold: 0.3,
            direction: 'horizontal',
            forceFallback: false,
            fallbackClass: 'sortable-fallback',
            fallbackOnBody: true,
            scroll: true,
            scrollSensitivity: 50,
            scrollSpeed: 20,
            invertSwap: false,
            swap: true,
            multiDrag: false,
            selectedClass: 'sortable-selected',
            filter: 'button',
            preventOnFilter: false,
            setData: function(dataTransfer, dragEl) {
                dataTransfer.setData('Text', dragEl.outerHTML);
            },
            onStart: function(evt) {
                // Add visual feedback when dragging starts
                evt.item.style.transform = 'rotate(5deg) scale(1.05)';
                evt.item.style.zIndex = '9999';
            },
            onMove: function(evt) {
                // Allow all moves
                return true;
            },
            onEnd: function (evt) {
                // Reset transform
                evt.item.style.transform = '';
                evt.item.style.zIndex = '';
                
                // Reorder files array
                const oldIndex = evt.oldIndex;
                const newIndex = evt.newIndex;
                
                if (oldIndex !== newIndex) {
                    const movedFile = selectedFiles.splice(oldIndex, 1)[0];
                    selectedFiles.splice(newIndex, 0, movedFile);
                    
                    // Update file input
                    updateFileInput();
                    // Update preview with new order
                    updatePreview();
                    
                    // Show success message
                    showModalNotification({
                        type: 'success',
                        title: 'Berhasil',
                        message: 'Urutan gambar berhasil diubah',
                        confirmText: 'OK',
                        showCancel: false
                    });
                }
            }
        });
    }

    // File input change
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    // Drop area specific events
    dropArea.addEventListener('dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        highlight();
    });
    
    dropArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        highlight();
    });
    
    dropArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!dropArea.contains(e.relatedTarget)) {
            unhighlight();
        }
    });
    
    dropArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        unhighlight();
        
        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
            handleFiles(files);
        }
    });

    function highlight() {
        dropArea.classList.add('border-blue-500', 'bg-blue-50', 'dragover');
        dropArea.classList.remove('border-gray-300');
    }
    
    function unhighlight() {
        dropArea.classList.remove('border-blue-500', 'bg-blue-50', 'dragover');
        dropArea.classList.add('border-gray-300');
    }

    function handleFiles(files) {
        const fileArray = Array.from(files);
        const validFiles = [];
        const errors = [];
        
        // Get current image count and remaining slots
        const currentImageCount = parseInt(document.getElementById('currentImageCount').textContent || '0');
        const remainingSlots = parseInt(document.getElementById('remainingSlots').textContent || '0');
        
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
            if (selectedFiles.length + validFiles.length >= maxFiles) {
                errors.push(`Maksimal ${maxFiles} gambar baru yang bisa dipilih`);
                return;
            }
            if (selectedFiles.length + validFiles.length >= remainingSlots) {
                errors.push(`Tidak bisa menambah lebih banyak gambar. Sisa slot: ${remainingSlots}`);
                return;
            }
            validFiles.push(file);
        });
        
        // Show errors if any
        if (errors.length > 0) {
            const uniqueErrors = [...new Set(errors)];
            showModalNotification({
                type: 'error',
                title: 'Error',
                message: uniqueErrors.join(', '),
                confirmText: 'OK',
                showCancel: false
            });
            return;
        }
        
        // Add valid files
        selectedFiles = [...selectedFiles, ...validFiles];
        
        // Update file input with selected files
        updateFileInput();
        
        // Update UI
        updateSelectedInfo();
        updatePreview();
        updateRemainingSlots();
        
        if (validFiles.length > 0) {
            showModalNotification({
                type: 'success',
                title: 'Berhasil',
                message: `${validFiles.length} gambar berhasil ditambahkan`,
                confirmText: 'OK',
                showCancel: false
            });
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
    }

    function clearAllFiles() {
        confirmAction(
            'Apakah Anda yakin ingin menghapus semua gambar yang dipilih?',
            function() {
                selectedFiles = [];
                updateFileInput();
                updateSelectedInfo();
                updatePreview();
                updateRemainingSlots();
                showModalNotification({
                    type: 'success',
                    title: 'Berhasil',
                    message: 'Semua gambar berhasil dihapus',
                    confirmText: 'OK',
                    showCancel: false
                });
            }
        );
    }
    function updatePreview() {
        sortableImages.innerHTML = '';
        
        if (selectedFiles.length > 0) {
            preview.classList.remove('hidden');
            
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageDiv = document.createElement('div');
                    imageDiv.className = 'relative group';
                    imageDiv.innerHTML = `
                        <div class="relative overflow-hidden rounded-md border-2 ${index === 0 && !hasPrimaryImage ? 'border-blue-500' : 'border-gray-200'}">
                            <img src="${e.target.result}" alt="Pratinjau ${index + 1}" 
                                 class="w-full h-24 object-cover">
                            
                            <!-- Drag Handle (only visible on hover, positioned to not overlap with remove button) -->
                            <div class="absolute bottom-1 left-1 opacity-0 group-hover:opacity-100 transition-opacity cursor-move bg-black bg-opacity-60 rounded px-2 py-1">
                                <i class="fas fa-arrows-alt text-white text-xs"></i>
                            </div>
                            
                            <!-- Position/Status Badge -->
                            ${index === 0 && !hasPrimaryImage ? '<span class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-2 py-1 rounded">Utama</span>' : `<span class="absolute top-1 left-1 bg-gray-600 text-white text-xs px-2 py-1 rounded">${index + 1}</span>`}
                            
                            <!-- Remove Button (always visible but subtle, becomes prominent on hover) -->
                            <button type="button" onclick="removeFile(${index})" 
                                    class="absolute top-1 right-1 bg-red-600 text-white w-6 h-6 rounded-full hover:bg-red-700 transition-all duration-200 flex items-center justify-center text-xs opacity-80 hover:opacity-100 z-10">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    
                    // Add data attribute for sortable
                    imageDiv.setAttribute('data-index', index);
                    
                    // Prevent drag when clicking remove button
                    const removeButton = imageDiv.querySelector('button');
                    if (removeButton) {
                        removeButton.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    }
                    
                    sortableImages.appendChild(imageDiv);
                };
                reader.readAsDataURL(file);
            });
            
            // Initialize sortable after adding images
            setTimeout(() => {
                initializeSortable();
            }, 100);
        } else {
            preview.classList.add('hidden');
        }
    }
    function removeFile(index) {
        confirmAction(
            'Apakah Anda yakin ingin menghapus gambar ini?',
            function() {
                selectedFiles.splice(index, 1);
                updateFileInput();
                updateSelectedInfo();
                updatePreview();
                updateRemainingSlots();
                showModalNotification({
                    type: 'success',
                    title: 'Berhasil',
                    message: 'Gambar berhasil dihapus',
                    confirmText: 'OK',
                    showCancel: false
                });
            }
        );
    }

    function updateRemainingSlots() {
        const currentImageCount = parseInt(document.getElementById('currentImageCount').textContent || '0');
        const remainingSlots = 6 - currentImageCount - selectedFiles.length;
        document.getElementById('remainingSlots').textContent = remainingSlots;
        
        // Update color based on remaining slots
        const remainingSlotsInfo = document.getElementById('remainingSlotsInfo');
        if (remainingSlots <= 0) {
            remainingSlotsInfo.className = 'text-red-600 font-medium';
        } else if (remainingSlots <= 2) {
            remainingSlotsInfo.className = 'text-yellow-600 font-medium';
        } else {
            remainingSlotsInfo.className = 'text-blue-800';
        }
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
    window.clearAllFiles = clearAllFiles;
    // Initialize on page load
    updateSelectedInfo(); // Update initial state
    updateRemainingSlots(); // Update remaining slots
    initializeSortable(); // Initialize sortable
});

function setPrimaryImage(imageId) {
    confirmAction('Jadikan gambar ini sebagai gambar utama?', function() {
        fetch(`/seller/products/images/${imageId}/primary`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showModalNotification({
                    type: 'success',
                    title: 'Berhasil',
                    message: data.message || 'Gambar utama berhasil diubah',
                    confirmText: 'OK',
                    showCancel: false,
                    onConfirm: () => {
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                });
            } else {
                showModalNotification({
                    type: 'error',
                    title: 'Error',
                    message: data.message || 'Gagal mengubah gambar utama',
                    confirmText: 'OK',
                    showCancel: false
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showModalNotification({
                type: 'error',
                title: 'Error',
                message: 'Terjadi kesalahan saat mengubah gambar utama',
                confirmText: 'OK',
                showCancel: false
            });
        });
    });
}

function deleteImage(imageId) {
    confirmAction('Apakah Anda yakin ingin menghapus gambar ini?', function() {
        fetch(`/seller/products/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showModalNotification({
                    type: 'success',
                    title: 'Berhasil',
                    message: data.message || 'Gambar berhasil dihapus',
                    confirmText: 'OK',
                    showCancel: false,
                    onConfirm: () => {
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                });
            } else {
                showModalNotification({
                    type: 'error',
                    title: 'Error',
                    message: data.message || 'Gagal menghapus gambar',
                    confirmText: 'OK',
                    showCancel: false
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showModalNotification({
                type: 'error',
                title: 'Error',
                message: 'Terjadi kesalahan saat menghapus gambar',
                confirmText: 'OK',
                showCancel: false
            });
        });
    });
}
</script>

<style>
    .sortable-ghost {
        opacity: 0.3;
        transform: rotate(3deg) scale(0.95);
        border: 2px dashed #3B82F6 !important;
        background-color: rgba(59, 130, 246, 0.1) !important;
    }

    .sortable-chosen {
        background-color: rgba(59, 130, 246, 0.15);
        border-color: #3B82F6 !important;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        transform: scale(1.02);
    }

    .sortable-drag {
        transform: rotate(5deg) scale(1.1);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        z-index: 9999;
        opacity: 0.9;
    }

    .sortable-fallback {
        transform: rotate(5deg) scale(1.1);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        opacity: 0.9;
    }

    .sortable-selected {
        background-color: rgba(59, 130, 246, 0.2);
        border-color: #3B82F6;
    }

    /* Drop zone indicator */
    .sortable-drop-zone {
        background-color: rgba(34, 197, 94, 0.1);
        border: 2px dashed #22C55E;
        border-radius: 0.5rem;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    /* Hover effects for image containers */
    .image-container:hover .drag-handle {
        opacity: 1;
    }

    .image-container .remove-btn {
        opacity: 0.8;
        transition: all 0.2s ease;
    }

    .image-container:hover .remove-btn {
        opacity: 1;
        transform: scale(1.1);
    }

    /* Drag area styling */
    #dropArea.border-blue-500 {
        background-color: rgba(59, 130, 246, 0.05);
    }

    /* Image preview responsive grid */
    #sortableImages {
        min-height: 100px;
        gap: 1rem;
    }

    /* Smooth transitions for all sortable elements */
    #sortableImages>* {
        transition: all 0.2s ease;
    }

    /* Better drag handle visibility */
    .group:hover .absolute.bottom-1.left-1 {
        opacity: 1 !important;
    }

    @media (max-width: 768px) {
        #sortableImages {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endsection