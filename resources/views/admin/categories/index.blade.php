@extends('layouts.app')

@section('title', 'Manajemen Kategori - Admin Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Manajemen Kategori</h1>
                    <p class="mt-2 text-gray-600">Kelola kategori produk</p>
                </div>
                <a href="{{ route('admin.categories.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Kategori
                </a>
            </div>
        </div>

        <!-- Search and Bulk Actions -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-wrap gap-4 justify-between">
                    <form method="GET" action="{{ route('admin.categories.index') }}" class="flex gap-4">
                        <div class="min-w-64">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari kategori..."
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>Cari
                        </button>
                        @if(request('search'))
                        <a href="{{ route('admin.categories.index') }}"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Bersihkan
                        </a>
                        @endif
                    </form>

                    <div class="flex gap-2">
                        <button id="bulkDeleteBtn" onclick="showBulkDeleteModal()"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 disabled:opacity-50"
                            disabled>
                            <i class="fas fa-trash mr-2"></i>Hapus yang Dipilih
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-list text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Kategori</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $categories->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-box text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Dengan Produk</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $categories->where('products_count',
                                    '>', 0)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Kategori Kosong</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $categories->where('products_count',
                                    0)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-chart-bar text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata Produk</dt>
                                <dd class="text-lg font-medium text-gray-900">{{
                                    number_format($categories->avg('products_count'), 1) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll"
                                    class="border-gray-300 rounded text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                    class="category-checkbox border-gray-300 rounded text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-tag text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $category->category }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $category->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $category->productdescription ? Str::limit($category->productdescription, 100) :
                                    'Tidak ada
                                    deskripsi' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $category->products_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $category->products_count }} produk
                                    </span>
                                    @if($category->products_count > 0)
                                    <a href="{{ route('products.category', $category) }}"
                                        class="ml-2 text-blue-600 hover:text-blue-500 text-sm">
                                        Lihat
                                    </a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.categories.show', $category) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        Lihat
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="text-yellow-600 hover:text-yellow-900">
                                        Edit
                                    </a>
                                    @if($category->products_count === 0)
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed"
                                        title="Tidak dapat menghapus kategori dengan produk">
                                        Hapus
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-list text-gray-400 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Kategori Ditemukan</h3>
                                <p class="text-gray-600 mb-4">
                                    @if(request('search'))
                                    Tidak ada kategori yang sesuai dengan kriteria pencarian Anda.
                                    @else
                                    Mulai dengan membuat kategori pertama Anda.
                                    @endif
                                </p>
                                @if(!request('search'))
                                <a href="{{ route('admin.categories.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Buat Kategori
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $categories->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div id="bulkDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Hapus Kategori</h3>
                <button onclick="closeBulkDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-gray-600">Apakah Anda yakin ingin menghapus kategori yang dipilih?</p>
                <p class="text-sm text-red-600 mt-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Tindakan ini tidak dapat dibatalkan. Kategori dengan produk tidak dapat dihapus.
                </p>
            </div>
            <form id="bulkDeleteForm" action="{{ route('admin.categories.bulk-delete') }}" method="POST">
                @csrf
                <div id="selectedCategoriesInput"></div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBulkDeleteModal()"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Hapus yang Dipilih
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    // Handle select all
    selectAllCheckbox.addEventListener('change', function() {
        categoryCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkDeleteButton();
    });

    // Handle individual checkboxes
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateBulkDeleteButton();
        });
    });

    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
        const totalCount = categoryCheckboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount && totalCount > 0;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    }

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
        bulkDeleteBtn.disabled = checkedCount === 0;
    }
});

function showBulkDeleteModal() {
    const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
    const selectedInput = document.getElementById('selectedCategoriesInput');
    
    selectedInput.innerHTML = '';
    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'category_ids[]';
        input.value = checkbox.value;
        selectedInput.appendChild(input);
    });
    
    document.getElementById('bulkDeleteModal').classList.remove('hidden');
}

function closeBulkDeleteModal() {
    document.getElementById('bulkDeleteModal').classList.add('hidden');
}
</script>
@endsection