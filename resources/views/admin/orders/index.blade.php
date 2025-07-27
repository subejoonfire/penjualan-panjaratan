@extends('layouts.app')

@section('title', 'Manajemen Pesanan - Admin Dashboard')

@section('content')
<div class="py-6">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Pesanan</h1>
            <p class="mt-2 text-gray-600">Kelola semua pesanan di sistem</p>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Pesanan</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Cari berdasarkan nomor pesanan..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="min-w-48">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Menunggu
                            </option>
                            <option value="processing" {{ request('status')==='processing' ? 'selected' : '' }}>Diproses
                            </option>
                            <option value="shipped" {{ request('status')==='shipped' ? 'selected' : '' }}>Dikirim
                            </option>
                            <option value="delivered" {{ request('status')==='delivered' ? 'selected' : '' }}>Diterima
                            </option>
                            <option value="cancelled" {{ request('status')==='cancelled' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.orders.index') }}"
                            class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Bersihkan
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Pesanan</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $orders->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

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
                                <dt class="text-sm font-medium text-gray-500 truncate">Menunggu</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $orders->where('status',
                                    'pending')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Diproses</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $orders->where('status',
                                    'processing')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-truck text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Diterima</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $orders->where('status',
                                    'delivered')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-times text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Dibatalkan</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $orders->where('status',
                                    'cancelled')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                <div class="text-sm text-gray-500">ID: {{ $order->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->cart->user->username
                                            }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->cart->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->cart->cartDetails->count() }} items</div>
                                <div class="text-sm text-gray-500">{{ $order->cart->cartDetails->sum('quantity') }} qty
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($order->grandtotal) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ',
                                    $order->transaction->payment_method ?? 'N/A')) }}</div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->transaction && $order->transaction->transactionstatus === 'paid') bg-green-100 text-green-800
                                        @elseif($order->transaction && $order->transaction->transactionstatus === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->transaction && $order->transaction->transactionstatus === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                    {{ ucfirst($order->transaction->transactionstatus ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                    @switch($order->status)
                                    @case('pending') Menunggu @break
                                    @case('processing') Diproses @break
                                    @case('shipped') Dikirim @break
                                    @case('delivered') Diterima @break
                                    @case('cancelled') Dibatalkan @break
                                    @default {{ ucfirst($order->status) }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d M Y') }}
                                <div class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="viewOrderDetails('{{ $order->id }}')"
                                        class="text-blue-600 hover:text-blue-900 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>Lihat
                                    </button>
                                    <button onclick="updateOrderStatus('{{ $order->id }}')"
                                        class="text-green-600 hover:text-green-900 transition-colors">
                                        <i class="fas fa-edit mr-1"></i>Update
                                    </button>
                                    @if($order->status === 'pending')
                                    <button onclick="cancelOrder('{{ $order->id }}')"
                                        class="text-red-600 hover:text-red-900 transition-colors">
                                        <i class="fas fa-times mr-1"></i>Batal
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
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
<div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white my-8">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="orderModalTitle" class="text-xl font-medium text-gray-900">Detail Pesanan</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="orderModalContent" class="max-h-[70vh] overflow-y-auto">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Status Pesanan</h3>
                <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="statusForm">
                <div class="mb-4">
                    <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                    <select id="newStatus"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="pending">Menunggu</option>
                        <option value="processing">Diproses</option>
                        <option value="shipped">Dikirim</option>
                        <option value="delivered">Diterima</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStatusModal()"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-spinner fa-spin mr-2 hidden" id="updateLoader"></i>
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentOrderId = null;

// CSRF Token Setup
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

function viewOrderDetails(orderId) {
    currentOrderId = orderId;
    document.getElementById('orderModalTitle').innerText = `Detail Pesanan #${orderId}`;
    document.getElementById('orderModalContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-blue-500 text-3xl mb-4"></i>
            <p class="text-gray-600">Memuat detail pesanan...</p>
        </div>
    `;
    document.getElementById('orderModal').classList.remove('hidden');
    
    // AJAX call to fetch order details
    fetch(`/admin/orders/${orderId}/details`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('orderModalContent').innerHTML = data.html;
        } else {
            document.getElementById('orderModalContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-4"></i>
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

function updateOrderStatus(orderId) {
    currentOrderId = orderId;
    document.getElementById('statusModal').classList.remove('hidden');
}

function cancelOrder(orderId) {
    confirmAction('Apakah Anda yakin ingin membatalkan pesanan ini?', function() {
        updateStatus(orderId, 'cancelled');
    });
}

function updateStatus(orderId, status) {
    const loader = document.getElementById('updateLoader');
    if (loader) loader.classList.remove('hidden');
    
    fetch(`/admin/orders/${orderId}/status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (loader) loader.classList.add('hidden');
        
        if (data.success) {
            showNotification(data.message, 'success');
            closeStatusModal();
            // Refresh page to show updated status
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('Gagal memperbarui status pesanan', 'error');
        }
    })
    .catch(error => {
        if (loader) loader.classList.add('hidden');
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memperbarui status', 'error');
    });
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
    currentOrderId = null;
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
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

// Status form submission
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const newStatus = document.getElementById('newStatus').value;
    
    if (currentOrderId && newStatus) {
        updateStatus(currentOrderId, newStatus);
    }
});

// Close modals when clicking outside
document.getElementById('orderModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOrderModal();
    }
});

document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});
</script>
@endsection