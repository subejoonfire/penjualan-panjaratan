@extends('layouts.app')

@section('title', 'Manajemen Pesanan - Dashboard Penjual')

@push('styles')
<style>
    .order-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .order-pending { border-left-color: #f59e0b; }
    .order-processing { border-left-color: #3b82f6; }
    .order-shipped { border-left-color: #8b5cf6; }
    .order-delivered { border-left-color: #10b981; }
    .order-cancelled { border-left-color: #ef4444; }

    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
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
                    <h1 class="text-3xl font-bold text-gray-900">Manajemen Pesanan</h1>
                    <p class="mt-2 text-gray-600">Kelola pesanan untuk produk Anda dengan efisien</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('seller.transactions.index') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-credit-card mr-2"></i>
                        Lihat Transaksi
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-blue-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Pesanan</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-yellow-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Menunggu</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-blue-600">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-cogs text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Diproses</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['processing']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-purple-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-truck text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Dikirim</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['shipped']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-green-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Selesai</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['delivered']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-emerald-600">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pendapatan</dt>
                                <dd class="text-lg font-bold text-gray-900">Rp {{ number_format($totalRevenue) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow-lg rounded-lg mb-6 border border-gray-200">
            <div class="px-6 py-3 border-b border-gray-200">
                <h3 class="text-base font-medium text-gray-900">Filter Pesanan</h3>
            </div>
            <div class="p-4">
                <form method="GET" action="{{ route('seller.orders.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Cari Pesanan</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Nomor pesanan, nama pelanggan..."
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 mr-2 text-sm">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('seller.orders.index') }}"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 text-sm">
                            <i class="fas fa-times mr-1"></i>Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white shadow-lg rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Pesanan</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($orders as $order)
                    @php
                        $sellerItems = $order->cart->cartDetails->filter(function($item) {
                            return $item->product->iduserseller === auth()->id();
                        });
                        $totalAmount = $sellerItems->sum(function($item) {
                            return $item->quantity * $item->productprice;
                        });
                    @endphp
                    <div class="order-card order-{{ $order->status }} p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Order Info -->
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center
                                        {{ $order->status === 'pending' ? 'bg-yellow-100' : '' }}
                                        {{ $order->status === 'processing' ? 'bg-blue-100' : '' }}
                                        {{ $order->status === 'shipped' ? 'bg-purple-100' : '' }}
                                        {{ $order->status === 'delivered' ? 'bg-green-100' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-100' : '' }}">
                                        <i class="fas 
                                            {{ $order->status === 'pending' ? 'fa-clock text-yellow-600' : '' }}
                                            {{ $order->status === 'processing' ? 'fa-cogs text-blue-600' : '' }}
                                            {{ $order->status === 'shipped' ? 'fa-truck text-purple-600' : '' }}
                                            {{ $order->status === 'delivered' ? 'fa-check-circle text-green-600' : '' }}
                                            {{ $order->status === 'cancelled' ? 'fa-times-circle text-red-600' : '' }}"></i>
                                    </div>
                                </div>

                                <!-- Order Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-lg font-medium text-gray-900">{{ $order->order_number }}</h4>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            @switch($order->status)
                                                @case('pending') Menunggu @break
                                                @case('processing') Diproses @break
                                                @case('shipped') Dikirim @break
                                                @case('delivered') Selesai @break
                                                @case('cancelled') Dibatalkan @break
                                                @default {{ ucfirst($order->status) }}
                                            @endswitch
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <!-- Customer Info -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pelanggan</p>
                                            <div class="flex items-center mt-1">
                                                <div class="flex-shrink-0 w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-gray-600 text-xs"></i>
                                                </div>
                                                <div class="ml-2">
                                                    <p class="text-sm font-medium text-gray-900">{{ $order->cart->user->nickname ?? $order->cart->user->username }}</p>
                                                    <p class="text-xs text-gray-500">{{ $order->cart->user->email }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Products Info -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Produk Anda</p>
                                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $sellerItems->count() }} produk</p>
                                            <p class="text-xs text-gray-500">
                                                @foreach($sellerItems->take(2) as $item)
                                                    {{ $item->product->productname }}@if(!$loop->last), @endif
                                                @endforeach
                                                @if($sellerItems->count() > 2)
                                                    +{{ $sellerItems->count() - 2 }} lainnya
                                                @endif
                                            </p>
                                        </div>

                                        <!-- Amount Info -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Anda</p>
                                            <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($totalAmount) }}</p>
                                            <p class="text-xs text-gray-500">{{ $sellerItems->sum('quantity') }} item</p>
                                        </div>
                                    </div>

                                    <!-- Date and Actions -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $order->created_at->diffForHumans() }}
                                        </div>
                                        
                                        <div class="flex items-center space-x-3">
                                            <button onclick="viewOrderDetails('{{ $order->id }}')"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-eye mr-1"></i>
                                                Detail
                                            </button>
                                            @if(in_array($order->status, ['pending', 'processing']))
                                            <button onclick="updateOrderStatus('{{ $order->id }}')"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-edit mr-1"></i>
                                                Update Status
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-shopping-cart text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Pesanan Ditemukan</h3>
                        <p class="text-gray-600 mb-6">
                            @if(request()->hasAny(['search', 'status']))
                                Tidak ada pesanan yang sesuai dengan kriteria filter Anda.
                            @else
                                Belum ada pesanan untuk produk Anda. Pesanan akan muncul di sini ketika pelanggan membeli produk Anda.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('seller.orders.index') }}"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-times mr-2"></i>
                                Bersihkan Filter
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden z-50">
    <div class="relative mx-auto p-5 border max-w-6xl w-full max-h-[90vh] shadow-lg rounded-md bg-white overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="orderModalTitle" class="text-lg font-medium text-gray-900">Detail Pesanan</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="orderModalContent">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden z-50">
    <div class="relative mx-auto p-5 border w-96 max-h-[90vh] shadow-lg rounded-md bg-white overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Status Pesanan</h3>
                <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="statusForm">
                <div class="mb-4">
                    <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                    <select id="newStatus"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="processing">Diproses</option>
                        <option value="shipped">Dikirim</option>
                        <option value="delivered">Selesai</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStatusModal()"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentOrderId = null;

    function viewOrderDetails(orderId) {
        currentOrderId = orderId;
        document.getElementById('orderModalTitle').innerText = `Detail Pesanan #${orderId}`;
        document.getElementById('orderModalContent').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-4"></i>
                <p class="text-gray-600">Memuat detail pesanan...</p>
            </div>
        `;
        document.getElementById('orderModal').classList.remove('hidden');
        
        fetch(`/seller/orders/${orderId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('orderModalContent').innerHTML = data.html;
                } else {
                    document.getElementById('orderModalContent').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-red-400 text-2xl mb-4"></i>
                            <p class="text-red-600">Gagal memuat detail pesanan</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('orderModalContent').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-red-400 text-2xl mb-4"></i>
                        <p class="text-red-600">Terjadi kesalahan saat memuat data</p>
                    </div>
                `;
            });
    }

    function updateOrderStatus(orderId) {
        currentOrderId = orderId;
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeOrderModal() {
        document.getElementById('orderModal').classList.add('hidden');
        currentOrderId = null;
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
        currentOrderId = null;
        document.getElementById('statusForm').reset();
    }

    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const newStatus = document.getElementById('newStatus').value;
        if (currentOrderId && newStatus) {
            fetch(`/seller/orders/${currentOrderId}/status`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(async response => {
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    data = null;
                }
                if (response.ok && data && data.success) {
                    closeStatusModal();
                    showAlert('Status pesanan berhasil diupdate', 'success');
                    setTimeout(() => location.reload(), 1200);
                } else if (response.status === 422 && data && data.errors) {
                    // Laravel validation error
                    const errors = Object.values(data.errors).flat();
                    showAlert(errors, 'error');
                } else if (data && data.message) {
                    showAlert(data.message, 'error');
                } else {
                    showAlert('Terjadi kesalahan saat mengupdate status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat mengupdate status', 'error');
            });
        }
    });
</script>
@endsection