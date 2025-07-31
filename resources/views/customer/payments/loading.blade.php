@extends('layouts.app')

@section('title', 'Memproses Pembayaran')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="animate-spin rounded-full h-20 w-20 border-4 border-blue-200 border-t-blue-600 mx-auto mb-6"></div>
        
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Memproses Pembayaran</h2>
        
        <div class="space-y-4 mb-8">
            <div class="flex items-center justify-center text-sm text-gray-600">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span>Pesanan berhasil dibuat</span>
            </div>
            <div class="flex items-center justify-center text-sm text-gray-600">
                <i class="fas fa-spinner fa-spin text-blue-500 mr-3"></i>
                <span>Mengalihkan ke pembayaran...</span>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Detail Pesanan</h3>
            <div class="text-sm text-gray-600 space-y-1">
                <div>Order: #{{ $transaction->order->order_number }}</div>
                <div>Total: Rp {{ number_format($transaction->amount) }}</div>
                <div>Metode: {{ $transaction->getPaymentMethodLabelAttribute() }}</div>
                <div>Payment URL: {{ $paymentUrl }}</div>
            </div>
        </div>
        
        <div class="text-xs text-gray-500">
            <p>Anda akan dialihkan ke halaman pembayaran dalam beberapa detik...</p>
            <p class="mt-2">Jika tidak dialihkan otomatis, klik tombol di bawah</p>
        </div>
        
        <div class="mt-6">
            <button onclick="redirectToPayment()" 
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 transition">
                <i class="fas fa-external-link-alt mr-2"></i>
                Lanjutkan ke Pembayaran
            </button>
        </div>
        
        <div class="mt-4">
            <a href="{{ route('customer.payments.index') }}" 
                class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i>
                Kembali ke Daftar Pembayaran
            </a>
        </div>
    </div>
</div>

<script>
    // Debug payment URL
    const paymentUrl = '{{ $paymentUrl }}';
    console.log('Payment URL:', paymentUrl);
    
    // Auto redirect after 3 seconds
    setTimeout(function() {
        console.log('Auto redirecting to:', paymentUrl);
        redirectToPayment();
    }, 3000);
    
    function redirectToPayment() {
        console.log('Redirecting to:', paymentUrl);
        if (paymentUrl && paymentUrl !== '') {
            window.location.href = paymentUrl;
        } else {
            console.error('Payment URL is empty or invalid');
            alert('Error: Payment URL tidak valid');
        }
    }
</script>
@endsection