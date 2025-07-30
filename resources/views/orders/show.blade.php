@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - Penjualan Panjaratan')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        Pesanan #{{ $order->order_number }}
                    </h1>
                    <p class="text-gray-600">
                        Dipesan pada {{ $order->created_at->format('d F Y, H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @switch($order->status)
                            @case('pending') bg-yellow-100 text-yellow-800 @break
                            @case('processing') bg-blue-100 text-blue-800 @break
                            @case('shipped') bg-purple-100 text-purple-800 @break
                            @case('delivered') bg-green-100 text-green-800 @break
                            @case('cancelled') bg-red-100 text-red-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch
                    ">
                        @switch($order->status)
                            @case('pending') Menunggu @break
                            @case('processing') Diproses @break
                            @case('shipped') Dikirim @break
                            @case('delivered') Selesai @break
                            @case('cancelled') Dibatalkan @break
                            @default {{ $order->status }}
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Item Pesanan</h2>
                    <div class="space-y-4">
                        @foreach($order->cart->cartDetails as $detail)
                        <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($detail->product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $detail->product->images->first()->image) }}" 
                                         alt="{{ $detail->product->productname }}"
                                         class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $detail->product->productname }}</h3>
                                                                    <p class="text-sm text-gray-600">oleh {{ $detail->product->seller->nickname ?? $detail->product->seller->username }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm text-gray-600">Jumlah: {{ $detail->quantity }}</span>
                                    <span class="font-medium text-gray-900">
                                        Rp {{ number_format($detail->quantity * $detail->productprice, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary & Actions -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                    <div class="space-y-3">
                        @php
                            $subtotal = $order->cart->cartDetails->sum(function($detail) {
                                return $detail->quantity * $detail->productprice;
                            });
                            $shipping = 15000;
                        @endphp
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="text-gray-900">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between font-semibold">
                                <span class="text-gray-900">Total</span>
                                <span class="text-gray-900">Rp {{ number_format($order->grandtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Alamat Pengiriman</h2>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ $order->shipping_address }}</p>
                </div>

                <!-- Payment Info -->
                @if($order->transaction)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Metode Pembayaran</span>
                            <span class="text-gray-900 capitalize">{{ ucfirst(str_replace('_', ' ', $order->transaction->payment_method)) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Status Pembayaran</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($order->transaction->transactionstatus === 'paid') bg-green-100 text-green-800
                                @elseif($order->transaction->transactionstatus === 'failed') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif
                            ">
                                @switch($order->transaction->transactionstatus)
                                    @case('pending') Menunggu @break
                                    @case('paid') Dibayar @break
                                    @case('failed') Gagal @break
                                    @default {{ $order->transaction->transactionstatus }}
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h2>
                    <div class="space-y-3">
                        @if(auth()->user()->role === 'admin')
                            <!-- Admin actions -->
                            <form action="{{ route('orders.status', $order) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Perbarui Status
                                </button>
                            </form>
                        @elseif(auth()->user()->role === 'seller')
                            <!-- Seller actions -->
                            @if(in_array($order->status, ['pending', 'processing']))
                            <form action="{{ route('orders.status', $order) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Kirim</option>
                                </select>
                                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Perbarui Status
                                </button>
                            </form>
                            @endif
                        @else
                            <!-- Customer actions -->
                            @if(in_array($order->status, ['pending', 'processing']))
                            <button type="button" 
                                  onclick="confirmAction('Apakah Anda yakin ingin membatalkan pesanan ini?', function() { document.getElementById('cancelOrderForm').submit(); })"
                                  class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Batalkan Pesanan
                            </button>
                            <form id="cancelOrderForm" action="{{ route('orders.cancel', $order) }}" method="POST" class="hidden">
                                @csrf
                                @method('PUT')
                            </form>
                            @endif
                        @endif
                        
                        <!-- Back button -->
                        <a href="{{ url()->previous() }}" class="block w-full text-center bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Kembali
                        </a>
                    </div>
                </div>

                @if($order->notes)
                <!-- Notes -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan</h2>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    </div>
</div>
@endsection