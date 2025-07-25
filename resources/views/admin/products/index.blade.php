@extends('layouts.app')

@section('title', 'Manajemen Produk - Admin Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Manajemen Produk</h1>
                    <p class="mt-2 text-gray-600">Kelola semua produk di sistem</p>
                </div>
                <a href="{{ route('admin.categories.index') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    <i class="fas fa-list mr-2"></i>
                    Kelola Kategori
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Cari berdasarkan nama produk..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="min-w-48">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category" id="category"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : ''
                                }}>
                                {{ $category->category }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-48">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Tidak Aktif
                            </option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('admin.products.index') }}"
                            class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Bersihkan
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-box text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Produk</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $products->total() }}</dd>
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
                                <i class="fas fa-check-circle text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Produk Aktif</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $products->where('is_active',
                                    true)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-times-circle text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Produk Tidak Aktif</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $products->where('is_active',
                                    false)->count() }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Stok Rendah</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $products->where('stock', '<', 10)->
                                        count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Penjual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->imageurl) }}"
                                            alt="{{ $product->productname }}" class="h-12 w-12 rounded-lg object-cover">
                                        @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{
                                            Str::limit($product->productname, 40) }}</div>
                                        <div class="text-sm text-gray-500">{{ $product->images->count() }} images</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $product->category->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $product->seller->username }}</div>
                                <div class="text-sm text-gray-500">{{ $product->seller->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($product->productprice) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($product->productstock === 0) bg-red-100 text-red-800
                                        @elseif($product->productstock < 10) bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                    {{ $product->productstock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('products.show', $product) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        Lihat
                                    </a>
                                    <button
                                        onclick="toggleProductStatus('{{ $product->id }}', '{{ $product->is_active }}')"
                                        class="text-yellow-600 hover:text-yellow-900">
                                        {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <button onclick="deleteProduct('{{ $product->id }}')"
                                        class="text-red-600 hover:text-red-900">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i class="fas fa-box text-gray-400 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Produk Ditemukan</h3>
                                <p class="text-gray-600">Tidak ada produk yang sesuai dengan kriteria filter Anda.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Action Modals -->
<div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Konfirmasi Aksi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent" class="mb-4">
                <!-- Modal content will be loaded here -->
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    Batal
                </button>
                <button id="confirmBtn" onclick="confirmAction()"
                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentAction = null;
let currentProductId = null;

function toggleProductStatus(productId, currentStatus) {
    currentAction = 'toggle';
    currentProductId = productId;
    const newStatus = currentStatus === '1' ? 'deactivate' : 'activate';
    
    document.getElementById('modalTitle').innerText = 'Toggle Product Status';
    document.getElementById('modalContent').innerHTML = `
        <p class="text-gray-600">Are you sure you want to ${newStatus} this product?</p>
    `;
    document.getElementById('confirmBtn').className = 'bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700';
    document.getElementById('actionModal').classList.remove('hidden');
}

function deleteProduct(productId) {
    currentAction = 'delete';
    currentProductId = productId;
    
    document.getElementById('modalTitle').innerText = 'Delete Product';
    document.getElementById('modalContent').innerHTML = `
        <p class="text-gray-600">Are you sure you want to delete this product? This action cannot be undone.</p>
    `;
    document.getElementById('confirmBtn').className = 'bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700';
    document.getElementById('actionModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('actionModal').classList.add('hidden');
    currentAction = null;
    currentProductId = null;
}

function confirmAction() {
    if (currentAction && currentProductId) {
        // In a real application, you would make AJAX calls here
        if (currentAction === 'toggle') {
            alert('Product status would be toggled via AJAX call');
        } else if (currentAction === 'delete') {
            alert('Product would be deleted via AJAX call');
        }
        closeModal();
        // Refresh page or update UI
        // window.location.reload();
    }
}
</script>
@endsection