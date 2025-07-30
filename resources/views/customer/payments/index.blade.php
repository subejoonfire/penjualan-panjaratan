@extends('layouts.app')
@section('title', 'Pembayaran Saya')
@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-8">
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Pembayaran Saya</h1>
            <p class="mt-1 text-sm text-gray-600">Lihat dan kelola pembayaran pesanan Anda.</p>
        </div>
        <div class="space-y-4">
            @forelse($transactions as $trx)
            <div class="bg-white shadow rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="font-semibold text-gray-900">#{{ $trx->order->order_number }}</div>
                    <div class="text-sm text-gray-600">{{ $trx->order->created_at->format('d M Y, H:i') }}</div>
                    <div class="text-sm text-gray-600">Total: <span class="font-bold">{{ $trx->formatted_amount }}</span></div>
                    <div class="text-sm text-gray-600">Status: <span class="font-bold">{{ $trx->status_label }}</span></div>
                </div>
                <div class="mt-3 md:mt-0 flex flex-col items-end gap-2">
                    @if($trx->isPending())
                        <a href="{{ route('customer.payments.pay', $trx) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Bayar</a>
                    @else
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded">Sudah Dibayar</span>
                    @endif
                    <a href="{{ route('customer.orders.show', $trx->order) }}" class="text-xs text-blue-600 hover:underline mt-1">Lihat Pesanan</a>
                </div>
            </div>
            @empty
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
                Tidak ada pembayaran ditemukan.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection