@extends('layouts.app')

@section('title', 'Manajemen Pesanan - Admin Dashboard')

@push('styles')
<style>
    .status-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
        margin: 0 2px;
    }

    .status-button:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .status-button.confirm {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .status-button.confirm:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }

    .status-button.ship {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .status-button.ship:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .status-button.complete {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }

    .status-button.complete:hover {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
    }

    .status-button.cancel {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .status-button.cancel:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .status-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .status-button:disabled:hover {
        transform: none;
        box-shadow: none;
    }

    .status-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .status-button:hover .status-tooltip {
        opacity: 1;
        visibility: visible;
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Pesanan</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola semua pesanan dari pelanggan</p>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filter Pesanan</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Pesanan</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Nomor pesanan, nama pelanggan..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Pesanan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                <div class="text-sm text-gray-500">{{ $order->cart->cartDetails->count() }} produk</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->cart->user->nickname ?? $order->cart->user->username }}</div>
                                <div class="text-sm text-gray-500">{{ $order->cart->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($order->grandtotal) }}</div>
                                <div class="text-sm text-gray-500">{{ $order->cart->cartDetails->sum('quantity') }} item</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewOrderDetails('{{ $order->id }}')"
                                        class="text-blue-600 hover:text-blue-900 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>Lihat
                                    </button>
                                    
                                    <!-- Status Action Buttons -->
                                    <div class="flex items-center space-x-1">
                                        @if($order->status === 'pending')
                                            <button onclick="updateOrderStatus('{{ $order->id }}', 'processing')" 
                                                    class="status-button confirm relative" title="Konfirmasi Pesanan">
                                                <i class="fas fa-check"></i>
                                                <span class="status-tooltip">Konfirmasi</span>
                                            </button>
                                            <button onclick="updateOrderStatus('{{ $order->id }}', 'cancelled')" 
                                                    class="status-button cancel relative" title="Batalkan Pesanan">
                                                <i class="fas fa-times"></i>
                                                <span class="status-tooltip">Batalkan</span>
                                            </button>
                                        @elseif($order->status === 'processing')
                                            <button onclick="updateOrderStatus('{{ $order->id }}', 'shipped')" 
                                                    class="status-button ship relative" title="Kirim Pesanan">
                                                <i class="fas fa-truck"></i>
                                                <span class="status-tooltip">Kirim</span>
                                            </button>
                                            <button onclick="updateOrderStatus('{{ $order->id }}', 'cancelled')" 
                                                    class="status-button cancel relative" title="Batalkan Pesanan">
                                                <i class="fas fa-times"></i>
                                                <span class="status-tooltip">Batalkan</span>
                                            </button>
                                        @elseif($order->status === 'shipped')
                                            <button onclick="updateOrderStatus('{{ $order->id }}', 'delivered')" 
                                                    class="status-button complete relative" title="Selesai">
                                                <i class="fas fa-check-double"></i>
                                                <span class="status-tooltip">Selesai</span>
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-500">Tidak dapat diupdate</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Pesanan</h3>
                                <p class="text-gray-600">Tidak ada pesanan yang sesuai dengan kriteria filter.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
    <div class="relative mx-auto p-5 border w-11/12 max-w-6xl max-h-[90vh] shadow-lg rounded-md bg-white overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="orderModalTitle" class="text-xl font-medium text-gray-900">Detail Pesanan</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="orderModalContent">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
    let currentOrderId = null;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
        
        fetch(`/admin/orders/${orderId}/details`)
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
                        <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-4"></i>
                        <p class="text-red-600">Terjadi kesalahan saat memuat detail pesanan</p>
                    </div>
                `;
            });
    }

    function updateOrderStatus(orderId, newStatus) {
        if (confirm('Apakah Anda yakin ingin mengubah status pesanan ini?')) {
            fetch(`/admin/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification('Gagal memperbarui status pesanan', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat memperbarui status', 'error');
            });
        }
    }

    function closeOrderModal() {
        document.getElementById('orderModal').classList.add('hidden');
        currentOrderId = null;
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Close modals when clicking outside
    document.getElementById('orderModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeOrderModal();
        }
    });
</script>
@endsection