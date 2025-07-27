@extends('layouts.app')

@section('title', 'Tambah Kategori Baru - Dashboard Admin')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tambah Kategori Baru</h1>
                    <p class="mt-2 text-gray-600">Buat kategori produk baru</p>
                </div>
                <a href="{{ route('admin.categories.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Kategori
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="px-6 py-6">
                    <!-- Category Name -->
                    <div class="mb-6">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Kategori <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="category" id="category" value="{{ old('category') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                      @error('category') border-red-300 @enderror"
                            placeholder="Masukkan nama kategori...">
                        @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Pilih nama yang unik dan deskriptif untuk kategori ini
                        </p>
                    </div>

                    <!-- Tips -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Panduan Kategori</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Gunakan nama kategori yang jelas dan spesifik</li>
                                        <li>Hindari duplikasi kategori yang sudah ada</li>
                                        <li>Kategori tidak dapat dihapus jika berisi produk</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col md:flex-row items-center justify-end space-y-2 md:space-y-0 md:space-x-4">
                    <a href="{{ route('admin.categories.index') }}"
                        class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Buat Kategori
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Pratinjau</h3>
                <p class="text-sm text-gray-600">Lihat bagaimana kategori Anda akan ditampilkan</p>
            </div>
            <div class="px-6 py-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-tag text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div id="preview-name" class="text-lg font-medium text-gray-900">
                                Nama Kategori
                            </div>
                            <div class="mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    0 produk
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('category');
    const previewName = document.getElementById('preview-name');

    // Update preview in real-time
    nameInput.addEventListener('input', function() {
        const value = this.value.trim();
        previewName.textContent = value || 'Nama Kategori';
    });

    // Initialize preview with existing values
    if (nameInput.value) {
        previewName.textContent = nameInput.value;
    }
});
</script>
@endsection