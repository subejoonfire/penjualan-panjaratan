@extends('layouts.app')

@section('title', 'Detail Kategori - ' . $category->category)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $category->category }}</h1>
                    <p class="mt-2 text-gray-600">Detail kategori dan statistik</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.categories.edit', $category) }}"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Kategori
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Kategori
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Category Information -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Kategori</h3>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0 h-16 w-16">
                                <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-tag text-blue-600 text-2xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-xl font-medium text-gray-900">{{ $category->category }}</h4>
                                <p class="text-sm text-gray-500">ID Kategori: {{ $category->id }}</p>
                            </div>
                        </div>

                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $category->productdescription ?: 'Tidak ada deskripsi' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('d M Y, H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->format('d M Y, H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="mt-6 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistik</h3>
                    </div>
                    <div class="px-6 py-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_products'] }}</div>
                                <div class="text-sm text-gray-500">Total Produk</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $stats['active_products'] }}</div>
                                <div class="text-sm text-gray-500">Produk Aktif</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_stock'])
                                    }}</div>
                                <div class="text-sm text-gray-500">Total Stok</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">
                                    @if($stats['avg_price'] > 0)
                                    Rp {{ number_format($stats['avg_price']) }}
                                    @else
                                    -
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">Harga Rata-rata</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Aksi</h3>
                    </div>
                    <div class="px-6 py-6 space-y-3">
                        <a href="{{ route('products.category', $category) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Halaman Publik
                        </a>
                        <a href="{{ route('admin.categories.edit', $category) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Kategori
                        </a>
                        @if($stats['total_products'] === 0)
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Kategori
                            </button>
                        </form>
                        @else
                        <div
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed">
                            <i class="fas fa-lock mr-2"></i>
                            Tidak Dapat Dihapus (Memiliki Produk)
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Products in this Category -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                Produk dalam Kategori Ini ({{ $category->products()->count() }})
                            </h3>
                            @if($category->products()->count() > 10)
                            <a href="{{ route('admin.products.index') }}?category={{ $category->id }}"
                                class="text-blue-600 hover:text-blue-500 text-sm">
                                Lihat Semua
                            </a>
                            @endif
                        </div>
                    </div>

                    @if($category->products()->count() > 0)
                    <div class="px-6 py-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($category->products()->with(['seller', 'images'])->latest()->take(10)->get() as
                            $product)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 h-16 w-16">
                                        @if($product->images->count() > 0)
                                        <img src="{{ url('storage/' . $product->images->first()->imageurl) }}"
                                            alt="{{ $product->productname }}" class="h-16 w-16 rounded-lg object-cover">
                                        @else
                                        <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 truncate">
                                            <a href="{{ route('products.show', $product) }}"
                                                class="hover:text-blue-600">
                                                {{ $product->productname }}
                                            </a>
                                        </h4>
                                        <p class="text-sm text-gray-500">oleh {{ $product->seller->username }}</p>
                                        <div class="mt-1 flex items-center space-x-2">
                                            <span class="text-sm font-medium text-blue-600">Rp {{
                                                number_format($product->productprice) }}</span>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Stok: {{ $product->productstock }} |
                                            Dibuat: {{ $product->created_at->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($category->products()->count() > 10)
                        <div class="mt-6 text-center">
                            <a href="{{ route('admin.products.index') }}?category={{ $category->id }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Lihat Semua {{ $category->products()->count() }} Produk
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-box text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Produk</h3>
                        <p class="text-gray-600 mb-4">Kategori ini belum memiliki produk.</p>
                        <a href="{{ route('admin.products.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Lihat Semua Produk
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Category Analytics -->
                @if($category->products()->count() > 0)
                <div class="mt-6 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Analitik Kategori</h3>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-6">
                            <!-- Top Sellers -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Penjual Teratas di Kategori Ini</h4>
                                <div class="space-y-2">
                                    @foreach($category->products()->with('seller')->get()->groupBy('seller_id')->take(5)
                                    as $sellerId => $products)
                                    @php $seller = $products->first()->seller; @endphp
                                    <div class="flex items-center justify-between py-2">
                                        <div class="flex items-center">
                                            <div
                                                class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-gray-600 text-xs"></i>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $seller->username }}</span>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $products->count() }} produk</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Rentang Harga</h4>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <div class="text-lg font-medium text-green-600">
                                            Rp {{ number_format($category->products()->min('productprice') ?? 0) }}
                                        </div>
                                        <div class="text-xs text-gray-500">Minimum</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-medium text-blue-600">
                                            Rp {{ number_format($category->products()->avg('productprice') ?? 0) }}
                                        </div>
                                        <div class="text-xs text-gray-500">Rata-rata</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-medium text-red-600">
                                            Rp {{ number_format($category->products()->max('productprice') ?? 0) }}
                                        </div>
                                        <div class="text-xs text-gray-500">Maksimum</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Produk Terbaru</h4>
                                <div class="space-y-2">
                                    @foreach($category->products()->latest()->take(5)->get() as $product)
                                    <div class="flex items-center justify-between py-2">
                                        <div class="flex-1">
                                            <a href="{{ route('products.show', $product) }}"
                                                class="text-sm text-blue-600 hover:text-blue-500 truncate block">
                                                {{ $product->productname }}
                                            </a>
                                            <p class="text-xs text-gray-500">oleh {{ $product->seller->username }}</p>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $product->created_at->diffForHumans()
                                            }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection