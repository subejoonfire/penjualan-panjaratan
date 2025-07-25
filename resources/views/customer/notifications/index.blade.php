@extends('layouts.app')

@section('title', 'Notifikasi - Penjualan Panjaratan')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Notifikasi</h1>
                    <p class="mt-2 text-gray-600">Dapatkan info terbaru tentang pesanan dan aktivitas akun Anda</p>
                </div>
                @if($notifications->where('readstatus', false)->count() > 0)
                    <form action="{{ route('customer.notifications.mark-all-read') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Tandai Semua Sudah Dibaca
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        @if($notifications->count() > 0)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @foreach($notifications as $notification)
                        <div class="px-6 py-4 hover:bg-gray-50 {{ !$notification->readstatus ? 'bg-blue-50' : '' }}">
                            <div class="flex items-start space-x-4">
                                <!-- Notification Icon -->
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                                        @if($notification->type === 'order') bg-blue-100
                                        @elseif($notification->type === 'payment') bg-green-100
                                        @elseif($notification->type === 'product') bg-purple-100
                                        @else bg-gray-100
                                        @endif">
                                        @if($notification->type === 'order')
                                            <i class="fas fa-shopping-bag text-blue-600"></i>
                                        @elseif($notification->type === 'payment')
                                            <i class="fas fa-credit-card text-green-600"></i>
                                        @elseif($notification->type === 'product')
                                            <i class="fas fa-box text-purple-600"></i>
                                        @else
                                            <i class="fas fa-bell text-gray-600"></i>
                                        @endif
                                    </div>
                                </div>

                                <!-- Notification Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-gray-900 {{ !$notification->readstatus ? 'font-semibold' : '' }}">
                                                {{ $notification->title }}
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ $notification->notification }}
                                            </p>
                                            <p class="mt-2 text-xs text-gray-400">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2 ml-4">
                                            @if(!$notification->readstatus)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Baru
                                                </span>
                                                <form action="{{ route('customer.notifications.read', $notification) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-blue-600 hover:text-blue-500 text-sm">
                                                        Tandai Sudah Dibaca
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-bell text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Notifikasi</h3>
                    <p class="text-gray-600 mb-6">Anda belum memiliki notifikasi. Jika ada pesanan atau pembaruan akun, notifikasi akan muncul di sini.</p>
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>
                        Lihat Produk
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection