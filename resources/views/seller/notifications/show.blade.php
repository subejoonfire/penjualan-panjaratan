@extends('layouts.app')

@section('title', 'Detail Notifikasi - Dashboard Penjual')

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-4 sm:mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Detail Notifikasi</h1>
                    <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Informasi lengkap notifikasi</p>
                </div>
                <a href="{{ route('seller.notifications.index') }}" 
                   class="bg-gray-600 text-white px-2 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-gray-700 text-xs sm:text-sm">
                    <i class="fas fa-arrow-left mr-1 sm:mr-2"></i>
                    <span class="hidden sm:inline">Kembali ke Notifikasi</span>
                    <span class="sm:hidden">Kembali</span>
                </a>
            </div>
        </div>

        <!-- Notification Detail -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        @php
                            $iconClass = 'fas fa-bell text-blue-500';
                            $bgClass = 'bg-blue-100';
                            switch($notification->type) {
                                case 'order':
                                    $iconClass = 'fas fa-shopping-cart text-green-500';
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
                        <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 {{ $bgClass }} rounded-full flex items-center justify-center">
                            <i class="{{ $iconClass }} text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base md:text-lg font-medium text-gray-900 line-clamp-2">{{ $notification->title }}</h3>
                            <p class="text-xs sm:text-sm text-gray-500">
                                Jenis: <span class="capitalize font-medium">{{ $notification->type }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($notification->readstatus)
                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                <span class="hidden sm:inline">Dibaca</span>
                                <span class="sm:hidden">✓</span>
                            </span>
                        @else
                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-exclamation mr-1"></i>
                                <span class="hidden sm:inline">Baru</span>
                                <span class="sm:hidden">!</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-3 sm:px-6 py-4 sm:py-6">
                <!-- Notification Content -->
                <div class="prose max-w-none">
                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
                        <h4 class="text-xs sm:text-sm font-medium text-gray-900 mb-2">Isi Notifikasi:</h4>
                        <p class="text-sm sm:text-base text-gray-700 leading-relaxed">{{ $notification->notification }}</p>
                    </div>
                </div>

                <!-- Notification Meta Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                        <h4 class="text-xs sm:text-sm font-medium text-gray-900 mb-2 sm:mb-3">Informasi Notifikasi</h4>
                        <dl class="space-y-1.5 sm:space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-xs sm:text-sm text-gray-500">ID:</dt>
                                <dd class="text-xs sm:text-sm font-medium text-gray-900">#{{ $notification->id }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs sm:text-sm text-gray-500">Jenis:</dt>
                                <dd class="text-xs sm:text-sm font-medium text-gray-900 capitalize">{{ $notification->type }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs sm:text-sm text-gray-500">Status:</dt>
                                <dd class="text-xs sm:text-sm font-medium text-gray-900">
                                    {{ $notification->readstatus ? 'Sudah Dibaca' : 'Belum Dibaca' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs sm:text-sm text-gray-500">Dibuat:</dt>
                                <dd class="text-xs sm:text-sm font-medium text-gray-900">{{ $notification->created_at->format('d M Y H:i') }}</dd>
                            </div>
                            @if($notification->readstatus && $notification->updated_at != $notification->created_at)
                            <div class="flex justify-between">
                                <dt class="text-xs sm:text-sm text-gray-500">Dibaca:</dt>
                                <dd class="text-xs sm:text-sm font-medium text-gray-900">{{ $notification->updated_at->format('d M Y H:i') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                        <h4 class="text-xs sm:text-sm font-medium text-gray-900 mb-2 sm:mb-3">Waktu Relatif</h4>
                        <dl class="space-y-1.5 sm:space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-xs sm:text-sm text-gray-500">Dibuat:</dt>
                                <dd class="text-xs sm:text-sm font-medium text-gray-900">{{ $notification->created_at->diffForHumans() }}</dd>
                            </div>
                            @if($notification->readstatus && $notification->updated_at != $notification->created_at)
                            <div class="flex justify-between">
                                <dt class="text-xs sm:text-sm text-gray-500">Dibaca:</dt>
                                <dd class="text-xs sm:text-sm font-medium text-gray-900">{{ $notification->updated_at->diffForHumans() }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-xs sm:text-sm text-gray-500">Usia:</dt>
                                <dd class="text-xs sm:text-sm font-medium text-gray-900">
                                    {{ $notification->created_at->diffInDays(now()) }} hari
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        @if(!$notification->readstatus)
                            <button onclick="markAsRead()" 
                                    class="bg-green-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-xs sm:text-sm">
                                <i class="fas fa-check mr-1 sm:mr-2"></i>
                                <span class="hidden sm:inline">Tandai Dibaca</span>
                                <span class="sm:hidden">Dibaca</span>
                            </button>
                        @endif
                    </div>
                    <div class="text-xs sm:text-sm text-gray-500">
                        Notifikasi #{{ $notification->id }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead() {
    fetch(`/seller/notifications/{{ $notification->id }}/read`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection