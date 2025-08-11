@extends('layouts.app')

@section('title', 'Laporan Penjualan - ' . config('app.name'))

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-3 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
                <div>
                    <h1 class="text-xl sm:text-xl md:text-2xl font-bold text-gray-900 mb-1 sm:mb-2">Laporan Penjualan</h1>
                    <p class="text-sm sm:text-base text-gray-600">Analisis performa penjualan Anda</p>
                </div>
                <div class="w-full lg:w-auto">
                    <form method="GET" action="{{ route('seller.sales') }}" class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 lg:space-x-4">
                        <div class="flex-1 sm:flex-none">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex-1 sm:flex-none">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-4 sm:mb-6">
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-dollar-sign text-sm sm:text-base md:text-xl"></i>
                    </div>
                    <div class="ml-2 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Penjualan</p>
                        <p class="text-sm sm:text-lg md:text-2xl font-bold text-gray-900 truncate">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-shopping-cart text-sm sm:text-base md:text-xl"></i>
                    </div>
                    <div class="ml-2 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Pesanan</p>
                        <p class="text-sm sm:text-lg md:text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-chart-line text-sm sm:text-base md:text-xl"></i>
                    </div>
                    <div class="ml-2 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Rata-rata per Pesanan</p>
                        <p class="text-sm sm:text-lg md:text-2xl font-bold text-gray-900 truncate">
                            Rp {{ number_format($totalOrders > 0 ? $totalSales / $totalOrders : 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-calendar text-sm sm:text-base md:text-xl"></i>
                    </div>
                    <div class="ml-2 sm:ml-4 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Periode</p>
                        <p class="text-sm sm:text-base md:text-lg font-bold text-gray-900">
                            {{ $startDate->format('d/m') }} - {{ $endDate->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Daily Sales Chart -->
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Penjualan Harian</h2>
                <div class="h-48 sm:h-64 flex items-center justify-center">
                    @if($dailySales->count() > 0)
                        <canvas id="dailySalesChart" width="400" height="200"></canvas>
                    @else
                        <div class="text-center text-gray-500">
                            <i class="fas fa-chart-line text-2xl sm:text-3xl md:text-4xl mb-2 sm:mb-4"></i>
                            <p class="text-sm sm:text-base">Belum ada data penjualan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Produk Terlaris</h2>
                <div class="space-y-3 sm:space-y-4">
                    @forelse($productSales as $product)
                        <div class="flex items-center justify-between p-2 sm:p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                                @if($product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                                         alt="{{ $product->productname }}"
                                         class="w-8 h-8 sm:w-12 sm:h-12 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div class="w-8 h-8 sm:w-12 sm:h-12 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-image text-gray-400 text-xs sm:text-sm"></i>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-medium text-gray-900 text-xs sm:text-sm truncate">{{ $product->productname }}</h3>
                                    <p class="text-xs text-gray-600">{{ $product->sold_quantity }} terjual</p>
                                </div>
                            </div>
                            <div class="text-right ml-2">
                                <p class="font-semibold text-gray-900 text-xs sm:text-sm">
                                    Rp {{ number_format($product->productprice, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-600">per item</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-6 sm:py-8">
                            <i class="fas fa-box text-2xl sm:text-3xl md:text-4xl mb-2 sm:mb-4"></i>
                            <p class="text-sm sm:text-base">Belum ada produk yang terjual</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Daily Sales Table -->
        @if($dailySales->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-3 sm:p-6 mt-4 sm:mt-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Detail Penjualan Harian</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah Pesanan
                            </th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Penjualan
                            </th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rata-rata
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dailySales as $sale)
                        <tr>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($sale->date)->format('d F Y') }}
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                {{ $sale->orders }} pesanan
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                Rp {{ number_format($sale->orders > 0 ? $sale->total / $sale->orders : 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

@if($dailySales->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('dailySalesChart').getContext('2d');
    const dailySalesData = @json($dailySales);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailySalesData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }),
            datasets: [{
                label: 'Penjualan (Rp)',
                data: dailySalesData.map(item => item.total),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Penjualan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection