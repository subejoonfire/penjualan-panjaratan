@extends('layouts.app')

@section('title', 'Notifikasi - Dashboard Penjual')

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-4 sm:mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Notifikasi</h1>
                    <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Kelola semua notifikasi penjual</p>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <button onclick="markAllAsRead()" 
                        class="bg-blue-600 text-white px-2 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm">
                        <i class="fas fa-check-double mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Tandai Semua Dibaca</span>
                        <span class="sm:hidden">Tandai Semua</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-3 md:grid-cols-3 gap-3 sm:gap-6 mb-4 sm:mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bell text-base sm:text-xl md:text-2xl text-blue-500"></i>
                        </div>
                        <div class="ml-2 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total</dt>
                                <dd class="text-sm sm:text-base md:text-lg font-medium text-gray-900">{{ number_format($stats['total']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-envelope text-base sm:text-xl md:text-2xl text-red-500"></i>
                        </div>
                        <div class="ml-2 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Belum Dibaca</dt>
                                <dd class="text-sm sm:text-base md:text-lg font-medium text-gray-900">{{ number_format($stats['unread']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-day text-base sm:text-xl md:text-2xl text-green-500"></i>
                        </div>
                        <div class="ml-2 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Hari Ini</dt>
                                <dd class="text-sm sm:text-base md:text-lg font-medium text-gray-900">{{ number_format($stats['today']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-4 sm:mb-6">
            <div class="px-3 sm:px-6 py-2 sm:py-3 border-b border-gray-200">
                <h3 class="text-sm sm:text-base font-medium text-gray-900">Filter Notifikasi</h3>
            </div>
            <div class="px-3 sm:px-6 py-2 sm:py-3">
                <form method="GET" action="{{ route('seller.notifications.index') }}" class="space-y-3 sm:grid sm:grid-cols-1 md:grid-cols-3 sm:gap-3 sm:space-y-0">
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full text-xs sm:text-sm border-gray-300 rounded-md shadow-sm">
                            <option value="">Semua Status</option>
                            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                        </select>
                    </div>

                    <div>
                        <label for="type" class="block text-xs font-medium text-gray-700 mb-1">Jenis</label>
                        <select name="type" id="type" class="w-full text-xs sm:text-sm border-gray-300 rounded-md shadow-sm">
                            <option value="">Semua Jenis</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 sm:flex-none bg-blue-600 text-white px-2 sm:px-3 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm">
                            <i class="fas fa-filter mr-1"></i>Filter
                        </button>
                        <a href="{{ route('seller.notifications.index') }}" 
                           class="flex-1 sm:flex-none bg-gray-300 text-gray-700 px-2 sm:px-3 py-2 rounded-md hover:bg-gray-400 text-xs sm:text-sm text-center">
                            <i class="fas fa-times mr-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-3 sm:px-6 py-2 sm:py-4 border-b border-gray-200">
                <h3 class="text-sm sm:text-base md:text-lg font-medium text-gray-900">Daftar Notifikasi</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($notifications as $notification)
                    <div class="px-3 sm:px-6 py-3 sm:py-4 hover:bg-gray-50 {{ !$notification->readstatus ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                        <div class="flex items-start justify-between space-x-2 sm:space-x-4">
                            <div class="flex items-start space-x-2 sm:space-x-4 flex-1">
                                <div class="flex-shrink-0 mt-1">
                                    @php
                                        $iconClass = 'fas fa-bell text-blue-500';
                                        switch($notification->type) {
                                            case 'order':
                                                $iconClass = 'fas fa-shopping-cart text-green-500';
                                                break;
                                            case 'payment':
                                                $iconClass = 'fas fa-credit-card text-yellow-500';
                                                break;
                                            case 'product':
                                                $iconClass = 'fas fa-box text-purple-500';
                                                break;
                                            case 'promotion':
                                                $iconClass = 'fas fa-percentage text-red-500';
                                                break;
                                            case 'system':
                                                $iconClass = 'fas fa-cog text-gray-500';
                                                break;
                                            case 'review':
                                                $iconClass = 'fas fa-star text-orange-500';
                                                break;
                                        }
                                    @endphp
                                    <i class="{{ $iconClass }} text-sm sm:text-base md:text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs sm:text-sm font-medium text-gray-900 {{ !$notification->readstatus ? 'font-bold' : '' }} line-clamp-2">
                                            {{ $notification->title }}
                                        </h4>
                                        @if(!$notification->readstatus)
                                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                                Baru
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2">
                                        @if(strlen($notification->notification) > 80)
                                            {{ substr($notification->notification, 0, 80) }}...
                                            <a href="{{ route('seller.notifications.show', $notification) }}" 
                                               class="text-blue-600 hover:text-blue-800 ml-1">
                                                lihat
                                            </a>
                                        @else
                                            {{ $notification->notification }}
                                        @endif
                                    </p>
                                    <div class="flex items-center justify-between mt-1 sm:mt-2">
                                        <p class="text-xs text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }} • 
                                            <span class="capitalize">{{ $notification->type }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 flex items-center space-x-1 sm:space-x-2">
                                <a href="{{ route('seller.notifications.show', $notification) }}" 
                                   class="text-blue-600 hover:text-blue-900 text-xs sm:text-sm p-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$notification->readstatus)
                                    <button onclick="markAsRead({{ $notification->id }})" 
                                            class="text-green-600 hover:text-green-900 text-xs sm:text-sm p-1">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-3 sm:px-6 py-8 sm:py-12 text-center">
                        <i class="fas fa-bell-slash text-gray-400 text-2xl sm:text-3xl md:text-4xl mb-3 sm:mb-4"></i>
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Tidak ada notifikasi</h3>
                        <p class="text-sm sm:text-base text-gray-500">Tidak ada notifikasi yang ditemukan dengan filter yang dipilih.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
                    {{ $notifications->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/seller/notifications/${notificationId}/read`, {
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

function markAllAsRead() {
    confirmAction('Tandai semua notifikasi sebagai dibaca?', function() {
        fetch('/seller/notifications/mark-all-read', {
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
    });
}
</script>
@endsection