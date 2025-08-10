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

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 md:mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Produk Saya</h1>
                    <p class="mt-1 md:mt-2 text-sm md:text-base text-gray-600">Kelola inventaris produk Anda</p>
                </div>
                <a href="{{ route('seller.products.create') }}"
                    class="w-full md:w-auto bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Produk
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-4">
                <form method="GET" action="{{ route('seller.products.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Cari Produk</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Nama produk..."
                                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="category" class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                            <select name="category" id="category"
                                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' :
                                    ''
                                    }}>
                                    {{ $category->category }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status"
                                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Tidak
                                    Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="submit"
                            class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <i class="fas fa-search mr-1"></i>
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('seller.products.index') }}"
                            class="w-full sm:w-auto bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 text-sm text-center">
                            <i class="fas fa-times mr-1"></i>
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
        <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-3 md:gap-6">
            @foreach($products as $product)
            @if($product && $product->id)
            <div
                class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full {{ !$product->is_active ? 'opacity-75' : '' }}">
                <!-- Product Image -->
                <div class="relative aspect-w-1 aspect-h-1 bg-gray-200">
                    @if($product->primaryImage)
                    <img src="{{ url('storage/'.$product->primaryImage->image) }}" alt="{{ $product->productname }}"
                        class="w-full h-24 md:h-36 object-cover">
                    @else
                    <div class="w-full h-24 md:h-48 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-lg md:text-2xl"></i>
                    </div>
                    @endif
                    @if(!$product->is_active)
                    <div class="absolute inset-0 bg-gray-900 bg-opacity-20"></div>
                    @endif
                    <!-- Status Badge -->
                    <div class="absolute top-1 md:top-2 left-1 md:left-2">
                        <span
                            class="inline-flex items-center px-1 md:px-2 py-0.5 md:py-1 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <!-- Stock Badge -->
                    @if($product->productstock <= 0) <div
                        class="absolute top-1 md:top-2 right-1 md:right-2 bg-red-600 text-white text-xs px-1 md:px-2 py-0.5 md:py-1 rounded">
                        Habis
                </div>
                @elseif($product->productstock <= 10) <div
                    class="absolute top-1 md:top-2 right-1 md:right-2 bg-yellow-600 text-white text-xs px-1 md:px-2 py-0.5 md:py-1 rounded">
                    Stok Terbatas
            </div>
            @endif
        </div>

        <!-- Product Info & Actions -->
        <div class="flex flex-col flex-1 justify-between p-2 md:p-3">
            <div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-900 mb-1 line-clamp-2 min-h-[2rem] md:min-h-[2.5rem]">
                    {{ $product->productname }}
                    @if(!$product->is_active)
                    <span class="text-xs text-red-500 font-normal">(Tidak Aktif)</span>
                    @endif
                </h3>
                <p class="text-xs text-gray-600 mb-1 hidden md:block">{{ $product->category ? $product->category->category : 'Kategori Tidak Ditemukan' }}</p>
                <p class="text-xs text-gray-500 mb-2 line-clamp-2 min-h-[1.5rem] md:min-h-[2rem] hidden md:block">
                    @php
                    $desc = strip_tags($product->productdescription);
                    $words = explode(' ', $desc);
                    if(count($words) > 15) {
                    $desc = implode(' ', array_slice($words, 0, 15)) . '...';
                    }
                    @endphp
                    {{ $desc }}
                </p>
                <div class="mb-2">
                    <span class="text-xs md:text-sm font-bold text-blue-600">
                        Rp {{ number_format($product->productprice) }}
                    </span>
                </div>
                <!-- Rating and Sales -->
                <div class="flex items-center justify-between mb-2 hidden md:flex">
                    <div class="flex items-center">
                        @php
                        $avgRating = $product->reviews->avg('rating') ?? 0;
                        $reviewsCount = $product->reviews->count();
                        @endphp
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++) <i
                                class="fas fa-star text-xs {{ $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300' }}">
                                </i>
                                @endfor
                        </div>
                        <span class="ml-1 text-xs text-gray-500">({{ $reviewsCount }})</span>
                    </div>
                    <div class="text-xs text-gray-500">
                        Terjual {{ $product->sold_count ?? 0 }}
                    </div>
                </div>
                <!-- Stock Info -->
                <div class="text-xs text-gray-500 mb-2 hidden md:block">
                    Stok: {{ $product->productstock }} • {{ $product->images->count() }} gambar
                </div>
            </div>

            <div class="flex flex-col gap-1 md:gap-2 mt-2">
                <div class="flex gap-1 md:gap-2 w-full">
                    @if($product && $product->id)
                        <a href="{{ route('products.show', $product) }}"
                            class="flex-1 bg-gray-100 text-gray-700 px-1 md:px-2 py-1 md:py-1.5 rounded text-xs font-medium hover:bg-gray-200 text-center">
                            Lihat
                        </a>
                        <a href="{{ route('seller.products.edit', $product) }}"
                            class="flex-1 {{ !$product->is_active ? 'bg-gray-400 text-gray-200 hover:bg-gray-500' : 'bg-blue-600 text-white hover:bg-blue-700' }} px-1 md:px-2 py-1 md:py-1.5 rounded text-xs font-medium text-center">
                            Edit
                        </a>
                    @else
                        <span class="flex-1 bg-gray-300 text-gray-500 px-2 py-1.5 rounded text-xs font-medium text-center">
                            Produk Tidak Valid
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
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