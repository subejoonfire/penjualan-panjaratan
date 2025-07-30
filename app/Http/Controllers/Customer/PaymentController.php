<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // Daftar pembayaran customer (unpaid & paid)
    public function index(Request $request)
    {
        $user = Auth::user();
        $transactions = Transaction::whereHas('order.cart', function ($q) use ($user) {
            $q->where('iduser', $user->id);
        })
            ->with(['order.cart.cartDetails.product.images'])
            ->orderByDesc('created_at')
            ->get();
        return view('customer.payments.index', compact('transactions'));
    }

    // Redirect ke Duitku untuk pembayaran
    public function pay(Transaction $transaction)
    {
        $user = Auth::user();
        if ($transaction->order->cart->iduser !== $user->id)
            abort(403);
        if ($transaction->isPaid())
            return redirect()->route('customer.payments.index')->with('success', 'Sudah dibayar');

        // Duitku API
        $apiKey = '8ac867d0e05e06d2e26797b29aec2c7a';
        $merchantCode = 'DS24203'; // Ganti sesuai merchantCode Duitku kamu
        $paymentAmount = (int) $transaction->amount;
        // Duitku butuh paymentMethod, default ke 'VC' (Virtual Account) jika null
        $paymentMethod = $transaction->payment_method ?: 'VC';
        $merchantOrderId = $transaction->transaction_number;
        $productDetails = 'Pembayaran Pesanan #' . $transaction->order->order_number;
        $email = $user->email;
        $phoneNumber = $user->phone ?? '';
        $callbackUrl = route('customer.payments.callback');
        $returnUrl = route('customer.payments.index');
        // Perbaiki signature sesuai dokumentasi Duitku
        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);
        $expiryPeriod = 60;
        $additionalParam = '';
        $merchantUserInfo = $user->nickname ?? $user->username;
        $customerVaName = $user->nickname ?? $user->username;
        // Item details
        $itemDetails = [];
        if ($transaction->order->cart) {
            foreach ($transaction->order->cart->cartDetails as $detail) {
                $itemDetails[] = [
                    'name' => $detail->product->productname,
                    'price' => (int) $detail->productprice,
                    'quantity' => (int) $detail->quantity
                ];
            }
        } else {
            // direct checkout (tanpa cart)
            $dt = $transaction->order->detailTransactions()->first();
            if ($dt && $dt->product) {
                $itemDetails[] = [
                    'name' => $dt->product->productname,
                    'price' => (int) $dt->price,
                    'quantity' => (int) $dt->quantity
                ];
            }
        }
        // Customer detail
        $address = $user->defaultAddress()?->address ?? $transaction->order->shipping_address;
        $customerDetail = [
            'firstName' => $user->nickname ?? $user->username,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'billingAddress' => [
                'firstName' => $user->nickname ?? $user->username,
                'address' => $address,
                'city' => '',
                'postalCode' => '',
                'phone' => $phoneNumber,
                'countryCode' => 'ID'
            ],
            'shippingAddress' => [
                'firstName' => $user->nickname ?? $user->username,
                'address' => $address,
                'city' => '',
                'postalCode' => '',
                'phone' => $phoneNumber,
                'countryCode' => 'ID'
            ]
        ];
        $params = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => $paymentAmount,
            'paymentMethod' => $paymentMethod,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $productDetails,
            'additionalParam' => $additionalParam,
            'merchantUserInfo' => $merchantUserInfo,
            'customerVaName' => $customerVaName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'itemDetails' => $itemDetails,
            'customerDetail' => $customerDetail,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'signature' => $signature,
            'expiryPeriod' => $expiryPeriod
        ];
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $params);
        // dd($response);
        if ($response->successful() && isset($response['paymentUrl'])) {
            return redirect($response['paymentUrl']);
        }
        Log::error('Duitku error', ['response' => $response->json(), 'params' => $params]);
        return back()->with('error', 'Gagal menghubungkan ke pembayaran.');
    }

    /**
     * Get available payment methods from Duitku
     */
    public function getPaymentMethods(Request $request)
    {
        $apiKey = '8ac867d0e05e06d2e26797b29aec2c7a'; // Ganti sesuai API key Duitku kamu
        $merchantCode = 'DS24203'; // Ganti sesuai merchantCode Duitku kamu
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        $amount = (int) ($request->amount ?? 10000); // Nominal contoh, bisa diganti sesuai kebutuhan
        $datetime = now()->format('Y-m-d H:i:s');
        $signature = hash('sha256', $merchantCode . $amount . $datetime . $apiKey);
        $params = [
            'merchantcode' => $merchantCode,
            'amount' => $amount,
            'datetime' => $datetime,
            'signature' => $signature
        ];
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $params);
        dd($response);
        if ($response->successful()) {
            return $response->json();
        }
        return ['error' => 'Gagal mengambil metode pembayaran'];
    }

    // Callback Duitku (update status pembayaran)
    public function callback(Request $request)
    {
        $merchantOrderId = $request->merchantOrderId;
        $resultCode = $request->resultCode;
        $transaction = Transaction::where('transaction_number', $merchantOrderId)->first();
        if (!$transaction)
            return response('Order not found', 404);
        if ($resultCode == '00') {
            $transaction->markAsPaid();
            $transaction->order->updateStatus('processing');
        } else {
            $transaction->markAsFailed();
        }
        return response('OK', 200);
    }
}