@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pesanan Saya</h1>
            <p class="mt-2 text-gray-600">Track and manage your orders</p>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('customer.orders.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                        <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                        @if(request()->hasAny(['status']))
                            <a href="{{ route('customer.orders.index') }}" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders List -->
        <div class="space-y-6">
            @forelse($orders as $order)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Order #{{ $order->order_number }}</h3>
                                <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="text-lg font-medium text-gray-900">Rp {{ number_format($order->total_amount) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        <!-- Order Items -->
                        <div class="space-y-4">
                            @foreach($order->cart->cartDetails as $detail)
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        @if($detail->product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $detail->product->images->first()->imageurl) }}" 
                                                 alt="{{ $detail->product->productname }}" 
                                                 class="w-16 h-16 rounded-lg object-cover">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $detail->product->productname }}</h4>
                                        <p class="text-sm text-gray-600">Qty: {{ $detail->quantity }} × Rp {{ number_format($detail->product->price) }}</p>
                                        <p class="text-sm text-gray-500">Seller: {{ $detail->product->seller->username }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">
                                            Rp {{ number_format($detail->quantity * $detail->product->price) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Summary -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex justify-between">
                                <div class="text-sm text-gray-600">
                                    <p><strong>Payment:</strong> {{ ucfirst($order->transaction->paymentmethod ?? 'N/A') }}</p>
                                    <p><strong>Payment Status:</strong> 
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            @if($order->transaction && $order->transaction->transactionstatus === 'paid') bg-green-100 text-green-800
                                            @elseif($order->transaction && $order->transaction->transactionstatus === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->transaction && $order->transaction->transactionstatus === 'failed') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($order->transaction->transactionstatus ?? 'N/A') }}
                                        </span>
                                    </p>
                                    @if($order->shipping_address)
                                        <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-medium text-gray-900">Total: Rp {{ number_format($order->total_amount) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Actions -->
                        <div class="mt-6 flex justify-between items-center">
                            <div class="flex space-x-3">
                                <a href="{{ route('customer.orders.show', $order) }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                                    View Details
                                </a>
                                @if(in_array($order->status, ['pending', 'confirmed']))
                                    <form action="{{ route('customer.orders.cancel', $order) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700"
                                                onclick="return confirm('Are you sure you want to cancel this order?')">
                                            Cancel Order
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            @if($order->status === 'delivered' && $order->cart->cartDetails->whereNull('reviewed_at')->count() > 0)
                                <div class="text-sm text-gray-600">
                                    <a href="{{ route('customer.orders.show', $order) }}#reviews" class="text-blue-600 hover:text-blue-500">
                                        Leave a Review
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-shopping-bag text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Orders Found</h3>
                        <p class="text-gray-600 mb-6">You haven't placed any orders yet or no orders match your filter criteria.</p>
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Start Shopping
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection