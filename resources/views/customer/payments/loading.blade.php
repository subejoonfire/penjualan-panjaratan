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
            </div>
        </div>
        
        <div class="text-xs text-gray-500 mb-4">
            <p>Anda akan dialihkan ke halaman pembayaran dalam beberapa detik...</p>
            <p class="mt-2">Jika tidak dialihkan otomatis, klik tombol di bawah</p>
        </div>
        
        <div class="space-y-3">
            <button onclick="redirectToPayment()" 
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 transition">
                <i class="fas fa-external-link-alt mr-2"></i>
                Lanjutkan ke Pembayaran
            </button>
            
            <button onclick="checkPaymentStatus()" 
                class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-md font-medium hover:bg-gray-200 transition">
                <i class="fas fa-sync-alt mr-2"></i>
                Cek Status Pembayaran
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

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Error Pembayaran</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="errorMessage">
                    Terjadi kesalahan saat memproses pembayaran.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="errorModalClose" 
                    class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Debug payment URL
    const paymentUrl = '{{ $paymentUrl }}';
    const transactionId = '{{ $transaction->id }}';
    console.log('Payment URL:', paymentUrl);
    console.log('Transaction ID:', transactionId);
    
    // Auto redirect after 3 seconds
    let redirectTimeout = setTimeout(function() {
        console.log('Auto redirecting to:', paymentUrl);
        redirectToPayment();
    }, 3000);
    
    function redirectToPayment() {
        console.log('Redirecting to:', paymentUrl);
        if (paymentUrl && paymentUrl !== '' && paymentUrl !== 'not_found') {
            // Clear timeout to prevent double redirect
            clearTimeout(redirectTimeout);
            window.location.href = paymentUrl;
        } else {
            console.error('Payment URL is empty or invalid');
            showError('Payment URL tidak valid. Silakan coba lagi atau hubungi customer service.');
        }
    }
    
    function checkPaymentStatus() {
        // Clear redirect timeout
        clearTimeout(redirectTimeout);
        
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengecek...';
        button.disabled = true;
        
        // Check payment status via AJAX
        fetch(`/customer/payments/${transactionId}/status`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'paid') {
                    window.location.href = '{{ route("customer.payments.index") }}?success=1';
                } else if (data.status === 'failed') {
                    showError('Pembayaran gagal. Silakan coba lagi.');
                } else {
                    showError('Status pembayaran masih pending. Silakan coba lagi dalam beberapa menit.');
                }
            } else {
                showError('Gagal mengecek status pembayaran. Silakan coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
            showError('Terjadi kesalahan saat mengecek status pembayaran.');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
    
    function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorModal').classList.remove('hidden');
    }
    
    // Close error modal
    document.getElementById('errorModalClose').addEventListener('click', function() {
        document.getElementById('errorModal').classList.add('hidden');
    });
    
    // Close modal when clicking outside
    document.getElementById('errorModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    
    // Handle page visibility change (user switches tabs)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // User came back to the tab, check if we should still redirect
            if (paymentUrl && paymentUrl !== '' && paymentUrl !== 'not_found') {
                // Reset redirect timeout
                redirectTimeout = setTimeout(function() {
                    redirectToPayment();
                }, 2000);
            }
        }
    });
</script>
@endsection