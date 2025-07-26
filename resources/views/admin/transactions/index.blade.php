@extends('layouts.app')

@section('title', 'Transaksi Management - Admin Dashboard')

@section('content')
<div class="py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Transaksi Management</h1>
            <p class="mt-2 text-gray-600">Manage all transactions in the system</p>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.transactions.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Transaksi</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Cari berdasarkan nomor transaksi..."
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="min-w-48">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Dibayar</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.transactions.index') }}" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Bersihkan
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-receipt text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Transaksi</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $transactions->total() }}</dd>
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
                                <i class="fas fa-check-circle text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Dibayar</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $transactions->where('transactionstatus', 'paid')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-green-600 font-medium">
                            Rp {{ number_format($transactions->where('transactionstatus', 'paid')->sum('amount')) }}
                        </span>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $transactions->where('transactionstatus', 'pending')->count() }}</dd>
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
                                <i class="fas fa-times-circle text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Gagal</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $transactions->where('transactionstatus', 'failed')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->transaction_number }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $transaction->id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->order->order_number }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->order->cart->cartDetails->count() }} items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600 text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $transaction->order->cart->user->username }}</div>
                                            <div class="text-sm text-gray-500">{{ $transaction->order->cart->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Rp {{ number_format($transaction->amount) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($transaction->paymentmethod === 'transfer')
                                            <i class="fas fa-university text-blue-600 mr-2"></i>
                                        @elseif($transaction->paymentmethod === 'cod')
                                            <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                                        @elseif($transaction->paymentmethod === 'ewallet')
                                            <i class="fas fa-mobile-alt text-purple-600 mr-2"></i>
                                        @else
                                            <i class="fas fa-credit-card text-gray-600 mr-2"></i>
                                        @endif
                                        <span class="text-sm text-gray-900">{{ ucfirst($transaction->paymentmethod) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($transaction->transactionstatus === 'paid') bg-green-100 text-green-800
                                        @elseif($transaction->transactionstatus === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($transaction->transactionstatus === 'failed') bg-red-100 text-red-800
                                        @elseif($transaction->transactionstatus === 'cancelled') bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($transaction->transactionstatus) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->created_at->format('d M Y') }}
                                    <div class="text-xs text-gray-400">{{ $transaction->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewTransactionDetails('{{ $transaction->id }}')" class="text-blue-600 hover:text-blue-900">
                                            Lihat
                                        </button>
                                        @if($transaction->transactionstatus === 'pending')
                                            <button onclick="updateTransactionStatus('{{ $transaction->id }}')" class="text-green-600 hover:text-green-900">
                                                Perbarui
                                            </button>
                                        @endif
                                        <button onclick="generateReceipt('{{ $transaction->id }}')" class="text-purple-600 hover:text-purple-900">
                                            Resi
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Transaksi Ditemukan</h3>
                                    <p class="text-gray-600">Tidak ada transaksi yang sesuai dengan kriteria filter Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div id="transactionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="transactionModalTitle" class="text-lg font-medium text-gray-900">Transaksi Details</h3>
                <button onclick="closeTransactionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="transactionModalContent" class="max-h-96 overflow-y-auto">
                <!-- Transaction details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Status Transaksi</h3>
                <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="statusForm">
                <div class="mb-4">
                    <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                    <select id="newStatus" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="pending">Pending</option>
                        <option value="paid">Dibayar</option>
                        <option value="failed">Gagal</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Tambahkan catatan tentang perubahan status..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStatusModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Perbarui Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentTransactionId = null;

function viewTransactionDetails(transactionId) {
    currentTransactionId = transactionId;
    document.getElementById('transactionModalTitle').innerText = `Transaksi Details - #${transactionId}`;
    document.getElementById('transactionModalContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-4"></i>
            <p class="text-gray-600">Loading transaction details...</p>
        </div>
    `;
    document.getElementById('transactionModal').classList.remove('hidden');
    
    // In a real application, you would make an AJAX call here to fetch transaction details
    setTimeout(() => {
        document.getElementById('transactionModalContent').innerHTML = `
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Transaksi Information</h4>
                    <p class="text-sm text-gray-600">Transaksi details would be loaded via AJAX here</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Order Details</h4>
                    <p class="text-sm text-gray-600">Related order information would be displayed here</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Payment Information</h4>
                    <p class="text-sm text-gray-600">Payment details and history would be shown here</p>
                </div>
            </div>
        `;
    }, 1000);
}

function updateTransactionStatus(transactionId) {
    currentTransactionId = transactionId;
    document.getElementById('statusModal').classList.remove('hidden');
}

function generateReceipt(transactionId) {
    // In a real application, this would generate and download a PDF receipt
    alert(`Receipt for transaction #${transactionId} would be generated and downloaded`);
}

function closeTransactionModal() {
    document.getElementById('transactionModal').classList.add('hidden');
    currentTransactionId = null;
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    currentTransactionId = null;
    document.getElementById('statusForm').reset();
}

document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const newStatus = document.getElementById('newStatus').value;
    const notes = document.getElementById('notes').value;
    
    if (currentTransactionId && newStatus) {
        // In a real application, you would make an AJAX call here
        alert(`Transaction status would be updated to: ${newStatus}${notes ? '\nNotes: ' + notes : ''}`);
        closeStatusModal();
        // Refresh page or update UI
        // window.location.reload();
    }
});
</script>
@endsection