@extends('layouts.app')

@section('title', 'Detail Notifikasi - ' . env('MAIL_FROM_NAME', 'Penjualan Panjaratan'))

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Notifikasi</h1>
                    <p class="mt-2 text-gray-600">Informasi lengkap notifikasi Anda</p>
                </div>
                <a href="{{ route('customer.notifications.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Notifikasi
                </a>
            </div>
        </div>

        <!-- Notification Detail -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @php
                            $iconClass = 'fas fa-bell text-blue-500';
                            $bgClass = 'bg-blue-100';
                            switch($notification->type) {
                                case 'order':
                                    $iconClass = 'fas fa-shopping-bag text-green-500';
                                    $bgClass = 'bg-green-100';
                                    break;
                                case 'payment':
                                    $iconClass = 'fas fa-credit-card text-yellow-500';
                                    $bgClass = 'bg-yellow-100';
                                    break;
                                case 'product':
                                    $iconClass = 'fas fa-box text-purple-500';
                                    $bgClass = 'bg-purple-100';
                                    break;
                                case 'promotion':
                                    $iconClass = 'fas fa-percentage text-red-500';
                                    $bgClass = 'bg-red-100';
                                    break;
                                case 'system':
                                    $iconClass = 'fas fa-cog text-gray-500';
                                    $bgClass = 'bg-gray-100';
                                    break;
                                case 'review':
                                    $iconClass = 'fas fa-star text-orange-500';
                                    $bgClass = 'bg-orange-100';
                                    break;
                            }
                        @endphp
                        <div class="flex-shrink-0 w-12 h-12 {{ $bgClass }} rounded-full flex items-center justify-center">
                            <i class="{{ $iconClass }} text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $notification->title }}</h3>
                            <p class="text-sm text-gray-500">
                                Jenis: <span class="capitalize font-medium">{{ $notification->type }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>
                            Dibaca
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-6">
                <!-- Notification Content -->
                <div class="prose max-w-none">
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Isi Notifikasi:</h4>
                        <p class="text-gray-700 leading-relaxed text-base">{{ $notification->notification }}</p>
                    </div>
                </div>

                <!-- Notification Meta Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Notifikasi</h4>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">ID:</dt>
                                <dd class="text-sm font-medium text-gray-900">#{{ $notification->id }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Jenis:</dt>
                                <dd class="text-sm font-medium text-gray-900 capitalize">{{ $notification->type }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Status:</dt>
                                <dd class="text-sm font-medium text-gray-900">Sudah Dibaca</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Tanggal:</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $notification->created_at->format('d M Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Waktu:</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $notification->created_at->format('H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Waktu Relatif</h4>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Diterima:</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $notification->created_at->diffForHumans() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Usia:</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    @if($notification->created_at->diffInDays(now()) == 0)
                                        Hari ini
                                    @elseif($notification->created_at->diffInDays(now()) == 1)
                                        1 hari
                                    @else
                                        {{ $notification->created_at->diffInDays(now()) }} hari
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Quick Actions based on notification type -->
                @if($notification->type === 'order')
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Aksi Terkait</h4>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('customer.orders.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Lihat Pesanan Saya
                        </a>
                    </div>
                </div>
                @elseif($notification->type === 'product')
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Aksi Terkait</h4>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                            <i class="fas fa-search mr-2"></i>
                            Jelajahi Produk
                        </a>
                    </div>
                </div>
                @elseif($notification->type === 'payment')
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Aksi Terkait</h4>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('customer.orders.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            <i class="fas fa-receipt mr-2"></i>
                            Lihat Riwayat Pembayaran
                        </a>
                    </div>
                </div>
                @endif

                <!-- Bottom Navigation -->
                <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                    <div>
                        <a href="{{ route('customer.notifications.index') }}" 
                           class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Kembali ke Notifikasi
                        </a>
                    </div>
                    <div class="text-sm text-gray-500">
                        Notifikasi #{{ $notification->id }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection