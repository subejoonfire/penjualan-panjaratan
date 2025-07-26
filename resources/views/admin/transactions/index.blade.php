@extends('layouts.app')

@section('title', 'Kelola Transaksi - Admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Transaksi</h1>
                    <p class="mt-2 text-gray-600">Kelola semua transaksi pembayaran dalam sistem</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="exportTransactions()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-credit-card text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $transactions->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Dibayar</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $transactions->where('transactionstatus', 'paid')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $transactions->where('transactionstatus', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-times text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Dibatalkan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $transactions->where('transactionstatus', 'cancelled')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <form method="GET" action="{{ route('admin.transactions.index') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <label for="search" class="sr-only">Cari Transaksi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search" 
                                value="{{ request('search') }}"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Cari nomor transaksi...">
                        </div>
                    </div>

                    <div>
                        <label for="status" class="sr-only">Status</label>
                        <select name="status" id="status" 
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Dibayar</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>

                    @if(request('search') || request('status'))
                    <div>
                        <a href="{{ route('admin.transactions.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <i class="fas fa-times mr-2"></i>Reset
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Transaksi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pelanggan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Metode Pembayaran
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-credit-card text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $transaction->transaction_number }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: {{ $transaction->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->order && $transaction->order->cart && $transaction->order->cart->user)
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $transaction->order->cart->user->username }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $transaction->order->cart->user->email }}
                                </div>
                                @else
                                <div class="text-sm text-gray-500">Data tidak tersedia</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @switch($transaction->payment_method)
                                        @case('bank_transfer')
                                            <i class="fas fa-university text-blue-600 mr-2"></i>
                                            <span class="text-sm text-gray-900">Transfer Bank</span>
                                            @break
                                        @case('credit_card')
                                            <i class="fas fa-credit-card text-green-600 mr-2"></i>
                                            <span class="text-sm text-gray-900">Kartu Kredit</span>
                                            @break
                                        @case('e_wallet')
                                            <i class="fas fa-wallet text-purple-600 mr-2"></i>
                                            <span class="text-sm text-gray-900">E-Wallet</span>
                                            @break
                                        @case('cod')
                                            <i class="fas fa-money-bill text-orange-600 mr-2"></i>
                                            <span class="text-sm text-gray-900">Bayar di Tempat</span>
                                            @break
                                        @default
                                            <i class="fas fa-question text-gray-600 mr-2"></i>
                                            <span class="text-sm text-gray-900">{{ $transaction->payment_method }}</span>
                                    @endswitch
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($transaction->amount) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($transaction->transactionstatus)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Pending
                                        </span>
                                        @break
                                    @case('paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Dibayar
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times mr-1"></i>Dibatalkan
                                        </span>
                                        @break
                                    @case('failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Gagal
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $transaction->transactionstatus }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewTransactionDetails({{ $transaction->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($transaction->transactionstatus === 'pending')
                                    <button onclick="updateTransactionStatus({{ $transaction->id }}, 'paid')" 
                                        class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="updateTransactionStatus({{ $transaction->id }}, 'cancelled')" 
                                        class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                    <button onclick="confirmAction('Apakah Anda yakin ingin menghapus transaksi ini?', function() { deleteTransaction({{ $transaction->id }}); })" 
                                        class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-credit-card text-gray-400 text-6xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Transaksi</h3>
                                <p class="text-gray-600">
                                    @if(request('search') || request('status'))
                                    Tidak ada transaksi yang sesuai dengan filter yang dipilih.
                                    @else
                                    Belum ada transaksi dalam sistem.
                                    @endif
                                </p>
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
<div id="transactionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Detail Transaksi</h3>
                <button onclick="closeTransactionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="transactionModalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function viewTransactionDetails(transactionId) {
    fetch(`/admin/transactions/${transactionId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('transactionModalContent').innerHTML = data.html;
                document.getElementById('transactionModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading transaction details:', error);
            showAlert('Gagal memuat detail transaksi', 'error');
        });
}

function closeTransactionModal() {
    document.getElementById('transactionModal').classList.add('hidden');
}

function updateTransactionStatus(transactionId, status) {
    const statusText = status === 'paid' ? 'dibayar' : 'dibatalkan';
    confirmAction(`Apakah Anda yakin ingin mengubah status transaksi menjadi ${statusText}?`, function() {
        fetch(`/admin/transactions/${transactionId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert(data.message || 'Gagal memperbarui status transaksi', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating transaction status:', error);
            showAlert('Gagal memperbarui status transaksi', 'error');
        });
    });
}

function deleteTransaction(transactionId) {
    fetch(`/admin/transactions/${transactionId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Transaksi berhasil dihapus', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert(data.message || 'Gagal menghapus transaksi', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting transaction:', error);
        showAlert('Gagal menghapus transaksi', 'error');
    });
}

function exportTransactions() {
    const search = document.getElementById('search').value;
    const status = document.getElementById('status').value;
    
    let url = '/admin/transactions/export?';
    if (search) url += `search=${encodeURIComponent(search)}&`;
    if (status) url += `status=${encodeURIComponent(status)}`;
    
    window.open(url, '_blank');
}

// Close modal when clicking outside
document.getElementById('transactionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTransactionModal();
    }
});
</script>
@endpush