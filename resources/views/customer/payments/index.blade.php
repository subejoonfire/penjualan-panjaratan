@extends('layouts.app')
@section('title', 'Pembayaran Saya')
@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-8">
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Pembayaran Saya</h1>
            <p class="mt-1 text-sm text-gray-600">Lihat dan kelola pembayaran pesanan Anda.</p>
        </div>

        <!-- Success Message -->
        @if(session('success') || request('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Berhasil!</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>{{ session('success') ?? 'Pembayaran berhasil diproses.' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="space-y-4">
            @forelse($transactions as $trx)
            <div class="bg-white shadow rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="font-semibold text-gray-900">#{{ $trx->order->order_number }}</div>
                    <div class="text-sm text-gray-600">{{ $trx->order->created_at->format('d M Y, H:i') }}</div>
                    <div class="text-sm text-gray-600">Total: <span class="font-bold">{{ $trx->formatted_amount }}</span></div>
                    <div class="text-sm text-gray-600">
                        Status: 
                        <span class="font-bold 
                            @if($trx->transactionstatus === 'paid') text-green-600
                            @elseif($trx->transactionstatus === 'failed') text-red-600
                            @else text-yellow-600
                            @endif">
                            {{ $trx->status_label }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Metode: {{ $trx->getPaymentMethodLabelAttribute() }}
                    </div>
                </div>
                <div class="mt-3 md:mt-0 flex flex-col items-end gap-2">
                    @if($trx->isPending())
                        <a href="{{ route('customer.payments.pay', $trx) }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            <i class="fas fa-credit-card mr-1"></i>Bayar
                        </a>
                    @elseif($trx->isPaid())
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded">
                            <i class="fas fa-check mr-1"></i>Sudah Dibayar
                        </span>
                    @else
                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded">
                            <i class="fas fa-times mr-1"></i>Gagal
                        </span>
                    @endif
                    <a href="{{ route('customer.orders.show', $trx->order) }}" 
                       class="text-xs text-blue-600 hover:underline mt-1">
                        <i class="fas fa-eye mr-1"></i>Lihat Pesanan
                    </a>
                </div>
            </div>
            @empty
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
                <i class="fas fa-credit-card text-4xl mb-4 text-gray-300"></i>
                <p class="text-lg font-medium mb-2">Tidak ada pembayaran ditemukan</p>
                <p class="text-sm">Belum ada transaksi pembayaran yang perlu Anda kelola.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection