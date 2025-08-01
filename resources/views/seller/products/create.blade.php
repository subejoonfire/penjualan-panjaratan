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
                            <input type="number" name="productstock" id="productstock" value="{{ old('productstock') }}"
                                required min="0"
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
                    <p class="mt-1 text-sm text-gray-600">Unggah maksimal 5 gambar produk (2MB per gambar). Gambar
                        pertama akan menjadi gambar utama.</p>
                </div>
                <div class="p-6">
                    <!-- Upload Info -->
                    <div class="mb-3 sm:mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5 text-sm sm:text-base"></i>
                            <div class="text-xs sm:text-sm text-blue-800">
                                <p class="font-medium">Info Upload Gambar:</p>
                                <ul class="mt-1 space-y-1">
                                    <li>• Maksimal 5 gambar produk</li>
                                    <li>• Format: JPG, PNG, GIF</li>
                                    <li>• Ukuran maksimal: 2MB per gambar</li>
                                    <li>• Gambar pertama akan menjadi gambar utama</li>
                                    <li>• Gambar saat ini: <span id="currentImageCount">0</span></li>
                                    <li id="remainingSlotsInfo">• Sisa slot untuk gambar: <span id="remainingSlots">5</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Area -->
                    <div id="dropArea"
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-400 transition-colors">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
                            <div class="flex text-sm text-gray-600 justify-center items-center">
                                <label for="images"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Unggah gambar</span>
                                    <input id="images" name="images[]" type="file" class="sr-only" multiple
                                        accept="image/*" required>
                                </label>
                                <p class="pl-1">atau seret dan lepas</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF maksimal 2MB per gambar</p>
                        </div>
                    </div>

                    <!-- Upload Info Box -->
                    <div id="selectedFilesInfo" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
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

                    <!-- Image Preview with Drag and Drop Reordering -->
                    <div id="imagePreview" class="mt-4 hidden">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Pratinjau Gambar (Seret untuk mengurutkan)
                        </h4>
                        <div id="sortableImages" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- Images will be previewed here -->
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Gambar pertama akan menjadi gambar utama produk</p>
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

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('images');
        const imagePreview = document.getElementById('imagePreview');
        const sortableImages = document.getElementById('sortableImages');
        const dropArea = document.getElementById('dropArea');
        const imageCount = document.getElementById('imageCount');
        
        let currentFiles = [];
        const maxFiles = 5;
        const maxFileSize = 2 * 1024 * 1024; // 2MB
        
        // Initialize Sortable
        const sortable = Sortable.create(sortableImages, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.group', // Allow dragging from the entire container
            filter: 'button', // Prevent dragging when clicking on buttons
            preventOnFilter: false, // Allow button clicks to work
            onStart: function(evt) {
                // Add visual feedback when dragging starts
                evt.item.style.transform = 'rotate(5deg) scale(1.05)';
            },
            onEnd: function (evt) {
                // Reset transform
                evt.item.style.transform = '';
                
                // Reorder files array
                const oldIndex = evt.oldIndex;
                const newIndex = evt.newIndex;
                
                if (oldIndex !== newIndex) {
                    const movedFile = currentFiles.splice(oldIndex, 1)[0];
                    currentFiles.splice(newIndex, 0, movedFile);
                    
                    // Update file input
                    updateFileInput();
                    // Update preview with new order
                    updateImagePreview();
                    
                    // Show success message
                    showAlert('Urutan gambar berhasil diubah', 'success');
                }
            }
        });
        
        // Drag and drop functionality
        function setupDragAndDrop() {
            // Prevent default drag behaviors on document level
            ['dragenter', 'dragover'].forEach(eventName => {
                document.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['drop'].forEach(eventName => {
                document.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
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
                // Only unhighlight if we're leaving the drop area itself
                if (!dropArea.contains(e.relatedTarget)) {
                    unhighlight();
                }
            });

            dropArea.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Drop event triggered');
                unhighlight();
                
                const files = e.dataTransfer.files;
                console.log('Files dropped:', files ? files.length : 0);
                if (files && files.length > 0) {
                    handleFiles(files);
                } else {
                    console.log('No files in drop event or files is null');
                }
            });
        }
        
        function highlight() {
            dropArea.classList.add('border-blue-500', 'bg-blue-50');
            const icon = dropArea.querySelector('.fa-cloud-upload-alt');
            if (icon) {
                icon.classList.add('text-blue-500', 'animate-bounce');
                icon.classList.remove('text-gray-400');
            }
        }
        
        function unhighlight() {
            dropArea.classList.remove('border-blue-500', 'bg-blue-50');
            const icon = dropArea.querySelector('.fa-cloud-upload-alt');
            if (icon) {
                icon.classList.remove('text-blue-500', 'animate-bounce');
                icon.classList.add('text-gray-400');
            }
        }
        
        // Setup drag and drop
        setupDragAndDrop();
        
        // Manual file input listener
        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                console.log('File input changed:', e.target.files.length, 'files selected');
                if (e.target.files.length > 0) {
                    handleFiles(e.target.files);
                }
            });
        }
        
        function handleFiles(files) {
            console.log('handleFiles called with', files.length, 'files');
            
            if (!files || files.length === 0) {
                console.log('No files provided to handleFiles');
                return;
            }
            
            const newFiles = Array.from(files).filter(file => {
                console.log('Processing file:', file.name, 'Type:', file.type, 'Size:', file.size);
                
                // Check file type
                if (!file.type.startsWith('image/')) {
                    showAlert(`File ${file.name} bukan file gambar yang valid.`, 'error');
                    return false;
                }
                
                // Check file size
                if (file.size > maxFileSize) {
                    showAlert(`File ${file.name} terlalu besar. Maksimal 2MB per gambar.`, 'error');
                    return false;
                }
                
                return true;
            });
            
            console.log('Valid files after filtering:', newFiles.length);
            
            if (newFiles.length === 0) {
                console.log('No valid files to add');
                return;
            }
            
            // Check total files limit
            if (currentFiles.length + newFiles.length > maxFiles) {
                const remainingSlots = maxFiles - currentFiles.length;
                if (remainingSlots > 0) {
                    showAlert(`Maksimal ${maxFiles} gambar. Hanya ${remainingSlots} gambar yang dapat ditambahkan.`, 'warning');
                    currentFiles = currentFiles.concat(newFiles.slice(0, remainingSlots));
                } else {
                    showAlert(`Maksimal ${maxFiles} gambar sudah tercapai.`, 'warning');
                    return; // Don't add any files if limit reached
                }
            } else {
                currentFiles = currentFiles.concat(newFiles);
            }
            
            console.log('Total files after adding:', currentFiles.length);
            
            updateFileInput();
            updateImagePreview();
            updateImageCount();
            updateSelectedInfo();
            updateRemainingSlots();
        }
        
        function updateFileInput() {
            const dt = new DataTransfer();
            currentFiles.forEach(file => dt.items.add(file));
            imageInput.files = dt.files;
        }
        
        function updateImagePreview() {
            sortableImages.innerHTML = '';
            
            if (currentFiles.length > 0) {
                imagePreview.classList.remove('hidden');
                
                currentFiles.forEach((file, index) => {
                                         const reader = new FileReader();
                     reader.onload = function(e) {
                         const imageDiv = document.createElement('div');
                         imageDiv.className = 'relative group';
                         imageDiv.innerHTML = `
                             <div class="relative overflow-hidden rounded-md border-2 ${index === 0 ? 'border-blue-500' : 'border-gray-200'}">
                                 <img src="${e.target.result}" alt="Pratinjau ${index + 1}" 
                                      class="w-full h-24 object-cover">
                                 
                                 <!-- Drag Handle (only visible on hover, positioned to not overlap with remove button) -->
                                 <div class="absolute bottom-1 left-1 opacity-0 group-hover:opacity-100 transition-opacity cursor-move bg-black bg-opacity-60 rounded px-2 py-1">
                                     <i class="fas fa-arrows-alt text-white text-xs"></i>
                                 </div>
                                 
                                 <!-- Position/Status Badge -->
                                 ${index === 0 ? '<span class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-2 py-1 rounded">Utama</span>' : `<span class="absolute top-1 left-1 bg-gray-600 text-white text-xs px-2 py-1 rounded">${index + 1}</span>`}
                                 
                                 <!-- Remove Button (always visible but subtle, becomes prominent on hover) -->
                                 <button type="button" onclick="removeImage(${index})" 
                                         class="absolute top-1 right-1 bg-red-600 text-white w-6 h-6 rounded-full hover:bg-red-700 transition-all duration-200 flex items-center justify-center text-xs opacity-80 hover:opacity-100 z-10">
                                     <i class="fas fa-times"></i>
                                 </button>
                             </div>
                         `;
                         
                         // Make the entire div draggable, but prevent drag when clicking remove button
                         imageDiv.draggable = true;
                         imageDiv.addEventListener('dragstart', function(e) {
                             // Prevent drag if clicking on remove button
                             if (e.target.closest('button')) {
                                 e.preventDefault();
                                 return false;
                             }
                         });
                         
                         sortableImages.appendChild(imageDiv);
                     };
                    reader.readAsDataURL(file);
                });
            } else {
                imagePreview.classList.add('hidden');
            }
        }
        
        function updateImageCount() {
            imageCount.textContent = `${currentFiles.length}/${maxFiles} gambar dipilih`;
            imageCount.classList.toggle('hidden', currentFiles.length === 0);
        }

        function updateRemainingSlots() {
            const currentImageCount = currentFiles.length;
            const remainingSlots = maxFiles - currentImageCount;
            document.getElementById('currentImageCount').textContent = currentImageCount;
            document.getElementById('remainingSlots').textContent = remainingSlots;
            
            // Update color based on remaining slots
            const remainingSlotsElement = document.getElementById('remainingSlots');
            if (remainingSlots <= 0) {
                remainingSlotsElement.className = 'text-red-600 font-medium';
            } else if (remainingSlots <= 2) {
                remainingSlotsElement.className = 'text-yellow-600 font-medium';
            } else {
                remainingSlotsElement.className = 'text-blue-800';
            }
        }

        function updateSelectedInfo() {
            const selectedFilesInfo = document.getElementById('selectedFilesInfo');
            const selectedCount = document.getElementById('selectedCount');
            
            if (currentFiles.length > 0) {
                selectedFilesInfo.classList.remove('hidden');
                selectedCount.textContent = currentFiles.length;
            } else {
                selectedFilesInfo.classList.add('hidden');
            }
        }

        function clearAllFiles() {
            confirmAction(
                'Apakah Anda yakin ingin menghapus semua gambar?',
                function() {
                    currentFiles = [];
                    updateFileInput();
                    updateImagePreview();
                    updateImageCount();
                    updateSelectedInfo();
                    updateRemainingSlots();
                    showAlert('Semua gambar berhasil dihapus', 'success');
                }
            );
        }

        // Make removeImage function global
        window.removeImage = function(index) {
             confirmAction(
                 'Apakah Anda yakin ingin menghapus gambar ini?',
                 function() {
                     currentFiles.splice(index, 1);
                     updateFileInput();
                     updateImagePreview();
                     updateImageCount();
                     updateSelectedInfo();
                     updateRemainingSlots();
                     showAlert('Gambar berhasil dihapus', 'success');
                 }
             );
         };
        
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

        // Initialize on page load
        updateRemainingSlots(); // Update initial state
    });
</script>

<style>
    .sortable-ghost {
        opacity: 0.4;
        transform: rotate(5deg);
    }

    .sortable-chosen {
        background-color: rgba(59, 130, 246, 0.1);
        border-color: #3B82F6;
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
    }

    @media (max-width: 768px) {
        #sortableImages {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endsection