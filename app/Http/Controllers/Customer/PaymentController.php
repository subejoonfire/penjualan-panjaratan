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
        try {
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

            if ($response->successful() && isset($response['paymentUrl'])) {
                // Show loading page first, then redirect
                return view('customer.payments.loading', [
                    'paymentUrl' => $response['paymentUrl'],
                    'transaction' => $transaction
                ]);
            }

            Log::error('Duitku error', ['response' => $response->json(), 'params' => $params]);
            return back()->with('error', 'Gagal menghubungkan ke pembayaran.');
        } catch (\Exception $e) {
            Log::error('Duitku connection error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghubungkan ke pembayaran: ' . $e->getMessage());
        }
    }

    public function checkout(Request $request)
    {
        $apiKey = '8ac867d0e05e06d2e26797b29aec2c7a';
        $merchantCode = 'DS24203';
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        $amount = (int) ($request->amount ?? 10000);
        $datetime = now()->format('Y-m-d H:i:s');
        $signature = hash('sha256', $merchantCode . $amount . $datetime . $apiKey);
        $params = [
            'merchantcode' => $merchantCode,
            'amount' => $amount,
            'datetime' => $datetime,
            'signature' => $signature
        ];

        $paymentMethods = [];
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $params);

            if ($response->successful() && isset($response['paymentFee'])) {
                $paymentMethods = $response['paymentFee'];
                \Log::info('Duitku checkout payment methods loaded: ' . count($paymentMethods) . ' methods');
            } else {
                \Log::warning('Duitku checkout payment methods failed, using fallback');
            }
        } catch (\Exception $e) {
            \Log::error('Duitku checkout payment methods exception: ' . $e->getMessage());
        }

        // Get cart data
        $user = auth()->user();
        $cart = $user->activeCart;
        $cartDetails = $cart ? $cart->cartDetails()->with('product.images', 'product.seller')->get() : collect();
        $addresses = $user->addresses ?? collect();
        $defaultAddress = $addresses->where('is_default', true)->first();
        $subtotal = $cartDetails->sum(function ($detail) {
            return $detail->quantity * $detail->productprice;
        });
        $shippingCost = 15000;
        $total = $subtotal + $shippingCost;

        return view('customer.checkout', compact('cart', 'cartDetails', 'addresses', 'defaultAddress', 'subtotal', 'shippingCost', 'total', 'paymentMethods'));
    }
    public function getPaymentMethods(Request $request)
    {
        $apiKey = '8ac867d0e05e06d2e26797b29aec2c7a';
        $merchantCode = 'DS24203';
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        $amount = (int) ($request->amount ?? 10000);
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

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['paymentFee']) && is_array($data['paymentFee'])) {
                return response()->json($data);
            } else {
                return response()->json(['error' => 'Payment methods not available']);
            }
        } else {
            return response()->json(['error' => 'Gagal mengambil metode pembayaran dari Duitku']);
        }
    }

    // Callback Duitku (update status pembayaran)
    public function callback(Request $request)
    {
        try {
            $merchantOrderId = $request->merchantOrderId;
            $resultCode = $request->resultCode;
            $signature = $request->signature;

            // Verify signature
            $apiKey = '8ac867d0e05e06d2e26797b29aec2c7a';
            $merchantCode = 'DS24203';
            $expectedSignature = md5($merchantCode . $merchantOrderId . $apiKey);

            if ($signature !== $expectedSignature) {
                Log::error('Duitku callback signature mismatch', [
                    'expected' => $expectedSignature,
                    'received' => $signature
                ]);
                return response('Invalid signature', 400);
            }

            $transaction = Transaction::where('transaction_number', $merchantOrderId)->first();
            if (!$transaction) {
                Log::error('Duitku callback: Transaction not found', ['merchantOrderId' => $merchantOrderId]);
                return response('Order not found', 404);
            }

            if ($resultCode == '00') {
                // Payment successful
                $transaction->update(['transactionstatus' => 'paid']);
                $transaction->order->update(['status' => 'processing']);

                // Create success notification
                \App\Models\Notification::create([
                    'iduser' => $transaction->order->cart->iduser,
                    'title' => 'Pembayaran Berhasil',
                    'notification' => 'Pembayaran untuk pesanan #' . $transaction->order->order_number . ' berhasil',
                    'type' => 'payment',
                    'readstatus' => false
                ]);

                Log::info('Duitku callback: Payment successful', [
                    'transaction_id' => $transaction->id,
                    'order_number' => $transaction->order->order_number
                ]);
            } else {
                // Payment failed
                $transaction->update(['transactionstatus' => 'failed']);
                $transaction->order->update(['status' => 'cancelled']);

                // Create failed notification
                \App\Models\Notification::create([
                    'iduser' => $transaction->order->cart->iduser,
                    'title' => 'Pembayaran Gagal',
                    'notification' => 'Pembayaran untuk pesanan #' . $transaction->order->order_number . ' gagal',
                    'type' => 'payment',
                    'readstatus' => false
                ]);

                Log::info('Duitku callback: Payment failed', [
                    'transaction_id' => $transaction->id,
                    'order_number' => $transaction->order->order_number,
                    'result_code' => $resultCode
                ]);
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Duitku callback error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return response('Internal server error', 500);
        }
    }
}
