@extends('layouts.app')

@section('title', 'Transaksi - Dashboard Penjual')

@push('styles')
<style>
    .transaction-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .transaction-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .transaction-paid { border-left-color: #10b981; }
    .transaction-pending { border-left-color: #f59e0b; }
    .transaction-cancelled { border-left-color: #ef4444; }
    .transaction-failed { border-left-color: #dc2626; }

    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .payment-icon {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
</style>
@endpush

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-4 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Transaksi Penjualan</h1>
                    <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Monitor pembayaran dan transaksi dari penjualan produk Anda</p>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <a href="{{ route('seller.orders.index') }}" 
                       class="bg-blue-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm">
                        <i class="fas fa-shopping-cart mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Lihat Pesanan</span>
                        <span class="sm:hidden">Pesanan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-6 mb-4 sm:mb-8">
            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-blue-500">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-credit-card text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-2 sm:ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Transaksi</dt>
                                <dd class="text-base sm:text-lg md:text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-yellow-500">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-hourglass-half text-white text-xs sm:text-sm payment-icon"></i>
                            </div>
                        </div>
                        <div class="ml-2 sm:ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Pending</dt>
                                <dd class="text-base sm:text-lg md:text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-green-500">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-2 sm:ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Lunas</dt>
                                <dd class="text-base sm:text-lg md:text-2xl font-bold text-gray-900">{{ number_format($stats['paid']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-red-500">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-times-circle text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-2 sm:ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Dibatalkan</dt>
                                <dd class="text-base sm:text-lg md:text-2xl font-bold text-gray-900">{{ number_format($stats['cancelled'] + $stats['failed']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-emerald-600 col-span-2 md:col-span-1">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-emerald-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-2 sm:ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Pendapatan</dt>
                                <dd class="text-sm sm:text-base md:text-lg font-bold text-gray-900">Rp {{ number_format($totalRevenue) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow-lg rounded-lg mb-4 sm:mb-6 border border-gray-200">
            <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Filter Transaksi</h3>
            </div>
            <div class="p-3 sm:p-6">
                <form method="GET" action="{{ route('seller.transactions.index') }}" class="space-y-3 sm:grid sm:grid-cols-1 md:grid-cols-3 sm:gap-4 sm:space-y-0">
                    <div>
                        <label for="search" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Cari Transaksi</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Nomor transaksi, nama atau email pelanggan..."
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Status Pembayaran</label>
                        <select name="status" id="status"
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="flex-1 sm:flex-none bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <i class="fas fa-search mr-1 sm:mr-2"></i>Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('seller.transactions.index') }}"
                            class="flex-1 sm:flex-none bg-gray-300 text-gray-700 px-4 sm:px-6 py-2 rounded-md hover:bg-gray-400 text-center text-sm">
                            <i class="fas fa-times mr-1 sm:mr-2"></i>Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions List -->
        <div class="bg-white shadow-lg rounded-lg border border-gray-200">
            <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Daftar Transaksi</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                    @php
                        $sellerItems = $transaction->order->cart->cartDetails->filter(function($item) {
                            return $item->product->iduserseller === auth()->id();
                        });
                        $totalAmount = $sellerItems->sum(function($item) {
                            return $item->quantity * $item->productprice;
                        });
                    @endphp
                    <div class="transaction-card transaction-{{ $transaction->transactionstatus }} p-3 sm:p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-start space-x-2 sm:space-x-4 flex-1">
                                <!-- Transaction Icon -->
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center
                                        {{ $transaction->transactionstatus === 'paid' ? 'bg-green-100' : '' }}
                                        {{ $transaction->transactionstatus === 'pending' ? 'bg-yellow-100' : '' }}
                                        {{ $transaction->transactionstatus === 'cancelled' ? 'bg-red-100' : '' }}
                                        {{ $transaction->transactionstatus === 'failed' ? 'bg-red-100' : '' }}">
                                        <i class="fas text-xs sm:text-sm
                                            {{ $transaction->transactionstatus === 'paid' ? 'fa-check-circle text-green-600' : '' }}
                                            {{ $transaction->transactionstatus === 'pending' ? 'fa-hourglass-half text-yellow-600 payment-icon' : '' }}
                                            {{ $transaction->transactionstatus === 'cancelled' ? 'fa-ban text-red-600' : '' }}
                                            {{ $transaction->transactionstatus === 'failed' ? 'fa-times-circle text-red-600' : '' }}"></i>
                                    </div>
                                </div>

                                <!-- Transaction Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2 space-y-1 sm:space-y-0">
                                        <div>
                                            <h4 class="text-sm sm:text-base md:text-lg font-medium text-gray-900 truncate">{{ $transaction->transaction_number }}</h4>
                                            <p class="text-xs sm:text-sm text-gray-500">Pesanan: {{ $transaction->order->order_number }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-xs sm:text-sm font-medium self-start sm:self-auto
                                            {{ $transaction->transactionstatus === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $transaction->transactionstatus === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $transaction->transactionstatus === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $transaction->transactionstatus === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                            @switch($transaction->transactionstatus)
                                                @case('paid') 
                                                    <i class="fas fa-check mr-1"></i>Lunas 
                                                @break
                                                @case('pending') 
                                                    <i class="fas fa-clock mr-1"></i>Pending 
                                                @break
                                                @case('cancelled') 
                                                    <i class="fas fa-ban mr-1"></i>Dibatalkan 
                                                @break
                                                @case('failed') 
                                                    <i class="fas fa-times mr-1"></i>Gagal 
                                                @break
                                                @default {{ ucfirst($transaction->transactionstatus) }}
                                            @endswitch
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-3 sm:mb-4">
                                        <!-- Customer Info -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pelanggan</p>
                                            <div class="flex items-center mt-1">
                                                <div class="flex-shrink-0 w-4 h-4 sm:w-6 sm:h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-gray-600 text-xs"></i>
                                                </div>
                                                <div class="ml-1 sm:ml-2 min-w-0 flex-1">
                                                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $transaction->order->cart->user->nickname ?? $transaction->order->cart->user->username }}</p>
                                                    <p class="text-xs text-gray-500 truncate">{{ $transaction->order->cart->user->email }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Method -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Metode Pembayaran</p>
                                            <div class="flex items-center mt-1">
                                                <i class="fas fa-credit-card text-blue-500 mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                                <p class="text-xs sm:text-sm font-medium text-gray-900">
                                                    {{ $transaction->getPaymentMethodLabelAttribute() }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Amount -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Bagian Anda</p>
                                            <p class="text-sm sm:text-base md:text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($totalAmount) }}</p>
                                            <p class="text-xs text-gray-500">{{ $sellerItems->sum('quantity') }} item</p>
                                        </div>

                                        <!-- Products Info -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Produk Anda</p>
                                            <p class="text-xs sm:text-sm font-medium text-gray-900 mt-1">{{ $sellerItems->count() }} produk</p>
                                            <p class="text-xs text-gray-500 line-clamp-2">
                                                @foreach($sellerItems->take(2) as $item)
                                                    {{ $item->product->productname }}@if(!$loop->last), @endif
                                                @endforeach
                                                @if($sellerItems->count() > 2)
                                                    +{{ $sellerItems->count() - 2 }} lainnya
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Date and Actions -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                                        <div class="flex items-center text-xs sm:text-sm text-gray-500">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $transaction->created_at->format('d M Y, H:i') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $transaction->created_at->diffForHumans() }}
                                        </div>
                                        
                                        <div class="flex items-center space-x-2 sm:space-x-3">
                                            <button onclick="viewTransactionDetails('{{ $transaction->id }}')"
                                                class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 border border-gray-300 shadow-sm text-xs sm:text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-eye mr-1"></i>
                                                Detail
                                            </button>
                                            @if($transaction->transactionstatus === 'paid')
                                            <span class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 text-xs sm:text-sm font-medium text-green-700 bg-green-100 rounded-md">
                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                <span class="hidden sm:inline">Komisi Diterima</span>
                                                <span class="sm:hidden">Diterima</span>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-3 sm:px-6 py-8 sm:py-12 text-center">
                        <i class="fas fa-credit-card text-gray-400 text-3xl sm:text-4xl md:text-6xl mb-3 sm:mb-4"></i>
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Tidak Ada Transaksi Ditemukan</h3>
                        <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
                            @if(request()->hasAny(['search', 'status']))
                                Tidak ada transaksi yang sesuai dengan kriteria filter Anda.
                            @else
                                Belum ada transaksi untuk produk Anda. Transaksi akan muncul di sini ketika pelanggan melakukan pembayaran.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('seller.transactions.index') }}"
                                class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-times mr-1 sm:mr-2"></i>
                                Bersihkan Filter
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div id="transactionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden z-50">
    <div class="relative mx-auto p-3 sm:p-5 border max-w-6xl w-full max-h-[90vh] shadow-lg rounded-md bg-white overflow-y-auto">
        <div class="mt-2 sm:mt-3">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h3 id="transactionModalTitle" class="text-base sm:text-lg font-medium text-gray-900">Detail Transaksi</h3>
                <button onclick="closeTransactionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>
            <div id="transactionModalContent">
                <!-- Transaction details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
    let currentTransactionId = null;

    function viewTransactionDetails(transactionId) {
        currentTransactionId = transactionId;
        document.getElementById('transactionModalTitle').innerText = `Detail Transaksi #${transactionId}`;
        document.getElementById('transactionModalContent').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-4"></i>
                <p class="text-gray-600">Memuat detail transaksi...</p>
            </div>
        `;
        document.getElementById('transactionModal').classList.remove('hidden');
        
        fetch(`/seller/transactions/${transactionId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('transactionModalContent').innerHTML = data.html;
                } else {
                    document.getElementById('transactionModalContent').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-red-400 text-2xl mb-4"></i>
                            <p class="text-red-600">Gagal memuat detail transaksi</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('transactionModalContent').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-red-400 text-2xl mb-4"></i>
                        <p class="text-red-600">Terjadi kesalahan saat memuat data</p>
                    </div>
                `;
            });
    }

    function closeTransactionModal() {
        document.getElementById('transactionModal').classList.add('hidden');
        currentTransactionId = null;
    }
</script>
@endsection