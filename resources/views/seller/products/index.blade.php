@extends('layouts.app')

@section('title', 'Produk Saya - Dashboard Penjual')

@push('styles')
<style>
    .product-inactive {
        filter: grayscale(30%) brightness(90%);
        transition: all 0.3s ease;
    }
    
    .product-inactive:hover {
        filter: grayscale(20%) brightness(95%);
        transform: translateY(-2px);
    }
    
    .product-active:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Produk Saya</h1>
                    <p class="mt-2 text-gray-600">Kelola inventaris produk Anda</p>
                </div>
                <a href="{{ route('seller.products.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Produk
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-4">
                <form method="GET" action="{{ route('seller.products.index') }}" class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-48">
                        <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Cari Produk</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Nama produk..."
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="min-w-40">
                        <label for="category" class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category" id="category"
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : ''
                                }}>
                                {{ $category->category }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-36">
                        <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <i class="fas fa-filter mr-1"></i>Filter
                        </button>
                        @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('seller.products.index') }}"
                            class="ml-2 bg-gray-600 text-white px-3 py-2 rounded-md hover:bg-gray-700 text-sm">
                            <i class="fas fa-times mr-1"></i>Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 {{ !$product->is_active ? 'product-inactive opacity-75 bg-gray-50 border border-gray-200' : 'product-active hover:shadow-lg' }}">
                <!-- Product Image -->
                <div class="aspect-w-1 aspect-h-1 relative">
                    @if($product->primaryImage)
                    <img src="{{ url('storage/'.$product->primaryImage->image) }}" alt="{{ $product->productname }}"
                        class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center {{ !$product->is_active ? 'bg-gray-300' : '' }}">
                        <i class="fas fa-image text-gray-400 text-3xl"></i>
                    </div>
                    @endif
                    
                    @if(!$product->is_active)
                    <div class="absolute inset-0 bg-gray-900 bg-opacity-20"></div>
                    @endif

                    <!-- Status Badge -->
                    <div class="absolute top-2 left-2">
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>

                    <!-- Stock Badge -->
                    @if($product->productstock < 10) <div class="absolute top-2 right-2">
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $product->productstock === 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $product->productstock === 0 ? 'Stok Habis' : 'Stok Rendah' }}
                        </span>
                </div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="p-4 {{ !$product->is_active ? 'bg-gray-50' : '' }}">
                <h3 class="text-lg font-medium mb-2 truncate {{ !$product->is_active ? 'text-gray-600' : 'text-gray-900' }}">
                    {{ $product->productname }}
                    @if(!$product->is_active)
                        <span class="text-xs text-gray-500 font-normal">(Tidak Aktif)</span>
                    @endif
                </h3>
                <p class="text-sm mb-2 {{ !$product->is_active ? 'text-gray-500' : 'text-gray-600' }}">{{ $product->category->category }}</p>
                <p class="text-lg font-bold mb-2 {{ !$product->is_active ? 'text-gray-500' : 'text-blue-600' }}">Rp {{ number_format($product->productprice) }}</p>

                <div class="flex items-center justify-between text-sm mb-4 {{ !$product->is_active ? 'text-gray-500' : 'text-gray-600' }}">
                    <span>Stok: {{ $product->productstock }}</span>
                    <span>{{ $product->images->count() }} gambar</span>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="{{ route('products.show', $product) }}"
                        class="flex-1 px-3 py-2 rounded-md text-sm font-medium text-center {{ !$product->is_active ? 'bg-gray-200 text-gray-500 hover:bg-gray-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Lihat
                    </a>
                    <a href="{{ route('seller.products.edit', $product) }}"
                        class="flex-1 px-3 py-2 rounded-md text-sm font-medium text-center {{ !$product->is_active ? 'bg-gray-400 text-gray-200 hover:bg-gray-500' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                        Edit
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="px-4 py-3 border-t {{ !$product->is_active ? 'bg-gray-100 border-gray-300' : 'bg-gray-50 border-gray-200' }}">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs {{ !$product->is_active ? 'text-gray-400' : 'text-gray-500' }}">Dilihat</p>
                        <p class="text-sm font-medium {{ !$product->is_active ? 'text-gray-600' : 'text-gray-900' }}">{{ $product->view_count ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-xs {{ !$product->is_active ? 'text-gray-400' : 'text-gray-500' }}">Terjual</p>
                                                                        <p class="text-sm font-medium {{ !$product->is_active ? 'text-gray-600' : 'text-gray-900' }}">{{ $product->sold_count }}</p>
                    </div>
                    <div>
                        <p class="text-xs {{ !$product->is_active ? 'text-gray-400' : 'text-gray-500' }}">Rating</p>
                        <p class="text-sm font-medium {{ !$product->is_active ? 'text-gray-600' : 'text-gray-900' }}">
                            @if($product->reviews()->count() > 0)
                                {{ number_format($product->reviews()->avg('rating'), 1) }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @endif
    @else
    <!-- Empty State -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-12 text-center">
            <i class="fas fa-box text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Produk Ditemukan</h3>
            <p class="text-gray-600 mb-6">
                @if(request()->hasAny(['search', 'category', 'status']))
                Tidak ada produk yang sesuai dengan kriteria filter Anda. Coba sesuaikan filter Anda.
                @else
                Anda belum menambahkan produk apapun. Mulai dengan membuat listing produk pertama Anda.
                @endif
            </p>
            @if(!request()->hasAny(['search', 'category', 'status']))
            <a href="{{ route('seller.products.create') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Tambah Produk Pertama
            </a>
            @else
            <a href="{{ route('seller.products.index') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                <i class="fas fa-refresh mr-2"></i>
                Bersihkan Filter
            </a>
            @endif
        </div>
    </div>
    @endif
</div>
</div>
@endsection