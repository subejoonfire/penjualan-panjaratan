@extends('layouts.app')

@section('title', 'Dashboard Pembeli - Penjualan Panjaratan')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dasbor</h1>
            <p class="mt-2 text-gray-600">Selamat datang kembali, {{ auth()->user()->nickname ??
                auth()->user()->username }}!</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Orders -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Pesanan</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($totalOrders) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Pengeluaran</dt>
                                <dd class="text-lg font-medium text-gray-900">Rp {{ number_format($totalSpent) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-clock text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pesanan Menunggu</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($pendingOrders) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Item di Keranjang</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($cartItemsCount) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('customer.cart.index') }}"
                            class="font-medium text-purple-600 hover:text-purple-500">
                            Lihat keranjang
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('products.index') }}"
                        class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="flex-shrink-0">
                            <i class="fas fa-search text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-600">Jelajahi Produk</p>
                            <p class="text-sm text-gray-500">Temukan item baru</p>
                        </div>
                    </a>

                    <a href="{{ route('customer.orders.index') }}"
                        class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="flex-shrink-0">
                            <i class="fas fa-list text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-600">Pesanan Saya</p>
                            <p class="text-sm text-gray-500">Lacak pembelian Anda</p>
                        </div>
                    </a>

                    <a href="{{ route('customer.notifications.index') }}"
                        class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bell text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-600">Notifikasi</p>
                            <p class="text-sm text-gray-500">{{ $unreadNotifications }} belum dibaca</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Pesanan Terbaru</h3>
                    <a href="{{ route('customer.orders.index') }}"
                        class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Lihat semua
                    </a>
                </div>
            </div>
            <div class="overflow-hidden">
                @if($recentOrders->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recentOrders as $order)
                    <li class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->cart->cartDetails->count() }} item • Rp
                                        {{ number_format($order->grandtotal) }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <a href="{{ route('customer.orders.show', $order) }}"
                                    class="text-blue-600 hover:text-blue-500">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="px-6 py-8 text-center">
                    <i class="fas fa-shopping-bag text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Anda belum melakukan pesanan apapun</p>
                    <a href="{{ route('products.index') }}"
                        class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Mulai Berbelanja
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Favorite Products -->
        @if($favoriteProducts->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Produk yang Pernah Anda Ulas</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4">
                    @foreach($favoriteProducts as $product)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                        <div class="aspect-w-1 aspect-h-1">
                            @if($product->images->count() > 0)
                                                            <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                                alt="{{ $product->productname }}" class="w-full h-48 object-cover">
                            @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $product->productname }}</h4>
                            <p class="text-sm text-gray-600">Rp {{ number_format($product->productprice) }}</p>
                            <div class="mt-2 flex items-center">
                                @php
                                $avgRating = $product->reviews->avg('rating');
                                @endphp
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-xs {{ $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <span class="ml-2 text-xs text-gray-500">({{ $product->reviews->count() }})</span>
                            </div>
                            <a href="{{ route('products.show', $product) }}"
                                class="mt-3 block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md text-sm font-medium hover:bg-blue-700">
                                Lihat Produk
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection