<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display cart contents
     */
    public function index()
    {
        $user = Auth::user();
        $cart = $user->activeCart;

        if (!$cart) {
            return view('customer.cart.index', [
                'cartDetails' => collect(),
                'subtotal' => 0,
                'shippingCost' => 0,
                'tax' => 0,
                'total' => 0
            ]);
        }

        $cartDetails = $cart->cartDetails()
            ->with(['product.images', 'product.seller', 'product.category'])
            ->get();

        $subtotal = $cartDetails->sum(function ($detail) {
            return $detail->quantity * $detail->productprice;
        });

        $shippingCost = 15000; // Fixed shipping cost
        $tax = 0; // No tax for now
        $total = $subtotal + $shippingCost + $tax;

        return view('customer.cart.index', compact('cartDetails', 'subtotal', 'shippingCost', 'tax', 'total'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, Product $product)
    {
        try {
            $user = Auth::user();

            // Set default quantity to 1 if not provided
            $quantity = $request->input('quantity', 1);

            $request->validate([
                'quantity' => 'integer|min:1|max:' . $product->productstock
            ]);

            // Check if product is active
            if (!$product->is_active) {
                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Produk tidak tersedia']);
                }
                return back()->with('error', 'Produk tidak tersedia');
            }

            // Check stock
            if ($product->productstock < $quantity) {
                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
                }
                return back()->with('error', 'Stok tidak mencukupi');
            }

            // Get or create active cart
            $cart = $user->activeCart;
            if (!$cart) {
                $cart = Cart::create([
                    'iduser' => $user->id,
                    'checkoutstatus' => 'active'
                ]);
            }

            // Check if product already in cart
            $existingDetail = $cart->cartDetails()
                ->where('idproduct', $product->id)
                ->first();

            if ($existingDetail) {
                $newQuantity = $existingDetail->quantity + $quantity;

                if ($newQuantity > $product->productstock) {
                    if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
                    }
                    return back()->with('error', 'Stok tidak mencukupi');
                }

                $existingDetail->update(['quantity' => $newQuantity]);
            } else {
                CartDetail::create([
                    'idcart' => $cart->id,
                    'idproduct' => $product->id,
                    'quantity' => $quantity,
                    'productprice' => $product->productprice
                ]);
            }

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil ditambahkan ke keranjang',
                    'cart_count' => $cart->cartDetails()->count()
                ]);
            }
            return back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, CartDetail $cartDetail)
    {
        try {
            $user = Auth::user();

            // Check if cart detail belongs to user
            if ($cartDetail->cart->iduser !== $user->id) {
                abort(403, 'Unauthorized');
            }

            $request->validate([
                'quantity' => 'required|integer|min:1|max:' . $cartDetail->product->productstock
            ]);

            // Check stock
            if ($cartDetail->product->productstock < $request->quantity) {
                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
                }
                return back()->with('error', 'Stok tidak mencukupi');
            }

            $cartDetail->update(['quantity' => $request->quantity]);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kuantitas berhasil diperbarui'
                ]);
            }
            return back()->with('success', 'Keranjang berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(CartDetail $cartDetail)
    {
        try {
            $user = Auth::user();

            // Check if cart detail belongs to user
            if ($cartDetail->cart->iduser !== $user->id) {
                abort(403, 'Unauthorized');
            }

            $cartDetail->delete();

            if (request()->ajax() || request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil dihapus dari keranjang'
                ]);
            }
            return back()->with('success', 'Item berhasil dihapus dari keranjang');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson() || request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        try {
            $user = Auth::user();
            $cart = $user->activeCart;

            if ($cart) {
                $cart->cartDetails()->delete();
            }

            if (request()->ajax() || request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Keranjang berhasil dikosongkan'
                ]);
            }
            return back()->with('success', 'Keranjang berhasil dikosongkan');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson() || request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show checkout page
     */
    public function checkout($cartId = null)
    {
        $user = Auth::user();

        if ($cartId) {
            // Direct checkout (single product)
            $cart = Cart::where('id', $cartId)->where('iduser', $user->id)->first();
            if (!$cart || $cart->cartDetails()->count() === 0) {
                return redirect()->route('customer.cart.index')->with('error', 'Keranjang checkout direct kosong');
            }
        } else {
            // Normal cart checkout
            $cart = $user->activeCart;
            if (!$cart || $cart->cartDetails()->count() === 0) {
                return redirect()->route('customer.cart.index')->with('error', 'Keranjang belanja kosong');
            }
        }

        $cartDetails = $cart->cartDetails()->with('product.images', 'product.seller')->get();
        $addresses = $user->addresses ?? collect();
        $defaultAddress = $addresses->where('is_default', true)->first();
        $subtotal = $cartDetails->sum(function ($detail) {
            return $detail->quantity * $detail->productprice;
        });
        $shippingCost = 15000;
        $total = $subtotal + $shippingCost;
        $addresses = $user->addresses;
        $defaultAddress = $user->defaultAddress();

        // Ambil payment methods dari API Duitku
        $paymentMethods = [];
        try {
            $apiKey = '8ac867d0e05e06d2e26797b29aec2c7a';
            $merchantCode = 'DS24203';
            $url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
            $amount = $total;
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

            if ($response->successful() && isset($response['paymentFee'])) {
                $paymentMethods = $response['paymentFee'];
            }
        } catch (\Exception $e) {
            \Log::error('Error getting payment methods', ['error' => $e->getMessage()]);
        }

        return view('customer.checkout', compact('cartDetails', 'subtotal', 'shippingCost', 'total', 'addresses', 'defaultAddress', 'cartId', 'paymentMethods'));
    }

    /**
     * Process checkout
     */
    public function processCheckout(Request $request)
    {
        try {
            // Log checkout process start
            \Log::info('Checkout process started', ['user_id' => Auth::id()]);

            // Log all request data for debugging
            \Log::info('Request data received', [
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'all_data' => $request->all(),
                'has_csrf_token' => $request->has('_token'),
                'content_type' => $request->header('Content-Type'),
                'accept' => $request->header('Accept')
            ]);

            $user = Auth::user();

            if (!$user) {
                \Log::warning('Unauthenticated checkout attempt');
                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
                }
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }

            $cart = null;

            if ($request->filled('cart_id')) {
                $cart = Cart::where('id', $request->cart_id)->where('iduser', $user->id)->first();
            } else {
                $cart = $user->activeCart;
            }

            if (!$cart || $cart->cartDetails()->count() === 0) {
                \Log::warning('Empty cart checkout attempt', ['user_id' => $user->id, 'cart_id' => $cart?->id]);
                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Keranjang belanja kosong']);
                }
                return back()->with('error', 'Keranjang belanja kosong');
            }

            \Log::info('Using active cart', ['cart_id' => $cart->id]);



            // Validate request data
            $validator = \Validator::make($request->all(), [
                'address_id' => 'nullable|exists:user_addresses,id',
                'shipping_address' => 'nullable|string',
                'payment_method' => 'required|string',
                'notes' => 'nullable|string|max:500'
            ], [
                'payment_method.required' => 'Metode pembayaran harus dipilih'
            ]);

            if ($validator->fails()) {
                \Log::warning('Checkout validation failed', [
                    'user_id' => $user->id,
                    'errors' => $validator->errors()->toArray()
                ]);

                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data checkout tidak valid',
                        'errors' => $validator->errors()
                    ]);
                }
                return back()->withErrors($validator)->withInput();
            }

            // Validasi payment_method - karena sudah dari API, kita trust saja
            if (empty($request->payment_method)) {
                \Log::warning('Empty payment method', ['user_id' => $user->id]);
                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Metode pembayaran harus dipilih']);
                }
                return back()->withErrors(['payment_method' => 'Metode pembayaran harus dipilih'])->withInput();
            }

            // Log checkout data for debugging
            \Log::info('Validating checkout data', [
                'address_id' => $request->address_id,
                'payment_method' => $request->payment_method,
                'has_shipping_address' => $request->filled('shipping_address'),
                'shipping_address_value' => $request->shipping_address
            ]);

            // Get shipping address
            $shippingAddress = '';
            $hasValidAddress = false;

            // Check if user has selected a saved address
            if ($request->filled('address_id')) {
                $address = $user->addresses()->find($request->address_id);
                if ($address) {
                    $shippingAddress = $address->address;
                    $hasValidAddress = true;
                    \Log::info('Using saved address', ['address_id' => $request->address_id, 'user_id' => $user->id]);
                } else {
                    \Log::warning('Selected address not found', ['address_id' => $request->address_id, 'user_id' => $user->id]);
                }
            }

            // Check if user has entered manual address
            if (!$hasValidAddress && $request->filled('shipping_address')) {
                $manualAddress = trim($request->shipping_address);
                if (!empty($manualAddress)) {
                    $shippingAddress = $manualAddress;
                    $hasValidAddress = true;
                    \Log::info('Using manual address', ['user_id' => $user->id]);
                }
            }

            if (!$hasValidAddress || empty($shippingAddress)) {
                \Log::warning('Missing shipping address', [
                    'user_id' => $user->id,
                    'address_id' => $request->address_id,
                    'has_shipping_address' => $request->filled('shipping_address'),
                    'shipping_address_value' => $request->shipping_address,
                    'has_valid_address' => $hasValidAddress,
                    'shipping_address_final' => $shippingAddress
                ]);

                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Alamat pengiriman harus diisi']);
                }
                return back()->withErrors(['shipping_address' => 'Alamat pengiriman harus diisi'])->withInput();
            }

            \Log::info('Shipping address validated successfully', [
                'user_id' => $user->id,
                'shipping_address' => $shippingAddress
            ]);

            // Check if at least one address option is provided
            $hasAddressId = $request->filled('address_id');
            $hasManualAddress = $request->filled('shipping_address') && !empty(trim($request->shipping_address));

            if (!$hasAddressId && !$hasManualAddress) {
                \Log::warning('No address provided', [
                    'user_id' => $user->id,
                    'has_address_id' => $hasAddressId,
                    'has_manual_address' => $hasManualAddress
                ]);

                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Silakan pilih alamat pengiriman atau masukkan alamat manual']);
                }
                return back()->withErrors(['shipping_address' => 'Silakan pilih alamat pengiriman atau masukkan alamat manual'])->withInput();
            }

            DB::beginTransaction();

            $cartDetails = $cart->cartDetails()->with('product')->get();

            // Check stock again
            foreach ($cartDetails as $detail) {
                if ($detail->product->productstock < $detail->quantity) {
                    DB::rollback();
                    return back()->with('error', 'Stok tidak mencukupi untuk ' . $detail->product->productname);
                }
            }

            $subtotal = $cartDetails->sum(function ($detail) {
                return $detail->quantity * $detail->productprice;
            });

            $shippingCost = 15000;
            $total = $subtotal + $shippingCost;

            // Generate order number with date format
            $date = now()->format('Ymd');
            $maxAttempts = 5;
            $orderNumber = null;
            for ($i = 0; $i < $maxAttempts; $i++) {
                $orderCount = Order::whereDate('created_at', today())->count() + 1 + $i;
                $orderNumberCandidate = 'ORD-' . $date . '-' . str_pad($orderCount, 6, '0', STR_PAD_LEFT);
                if (!Order::where('order_number', $orderNumberCandidate)->exists()) {
                    $orderNumber = $orderNumberCandidate;
                    break;
                }
            }
            if (!$orderNumber) {
                $orderNumber = 'ORD-' . $date . '-' . strtoupper(uniqid());
            }

            // Create order
            $order = Order::create([
                'idcart' => $cart->id,
                'order_number' => $orderNumber,
                'grandtotal' => $total,
                'shipping_address' => $shippingAddress,
                'status' => 'pending',
                'notes' => $request->notes
            ]);

            // Generate transaction number with date format
            $transactionCount = Transaction::whereDate('created_at', today())->count() + 1;
            $transactionNumber = 'TRX-' . $date . '-' . str_pad($transactionCount, 6, '0', STR_PAD_LEFT);

            // Create transaction
            $transaction = Transaction::create([
                'idorder' => $order->id,
                'transaction_number' => $transactionNumber,
                'amount' => $total,
                'payment_method' => $request->payment_method,
                'transactionstatus' => 'pending'
            ]);

            // Update cart status
            $cart->update(['checkoutstatus' => 'completed']);

            // Update product stock
            foreach ($cartDetails as $detail) {
                $detail->product->decrement('productstock', $detail->quantity);
            }

            // Create notification
            Notification::create([
                'iduser' => $user->id,
                'title' => 'Pesanan Dibuat',
                'notification' => 'Pesanan #' . $order->order_number . ' berhasil dibuat',
                'type' => 'order',
                'readstatus' => false
            ]);

            DB::commit();

            // Jika COD, langsung ke halaman order
            if ($request->payment_method === 'cod') {
                return redirect()->route('customer.orders.show', $order)
                    ->with('success', 'Pesanan berhasil dibuat');
            }

            // Jika bukan COD, redirect ke halaman pembayaran
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat',
                    'redirect_url' => route('customer.payments.pay', $transaction)
                ]);
            }
            return redirect()->route('customer.payments.pay', $transaction)
                ->with('success', 'Pesanan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollback();

            // Log the error
            \Log::error('Checkout process failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses checkout: ' . $e->getMessage()
                ]);
            }
            return back()->with('error', 'Terjadi kesalahan saat memproses checkout: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Get cart count for AJAX
     */
    public function getCartCount()
    {
        $user = Auth::user();
        $cart = $user->activeCart;

        $count = 0;
        if ($cart) {
            $count = $cart->cartDetails()->count(); // Count unique products, not quantities
        }

        return response()->json(['count' => $count]);
    }

    /**
     * Get cart items for AJAX
     */
    public function getCartItems()
    {
        $user = Auth::user();
        $cart = $user->activeCart;

        if (!$cart) {
            return response()->json(['items' => [], 'total' => 0]);
        }

        $items = $cart->cartDetails()
            ->with(['product.images'])
            ->get()
            ->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product_name' => $detail->product->productname,
                    'product_image' => $detail->product->images->first()?->image,
                    'productprice' => $detail->productprice,
                    'quantity' => $detail->quantity,
                    'subtotal' => $detail->quantity * $detail->productprice
                ];
            });

        $total = $items->sum('subtotal');

        return response()->json(['items' => $items, 'total' => $total]);
    }

    public function directCheckout(Request $request, $productId)
    {
        try {
            $user = Auth::user();
            $request->validate(['quantity' => 'required|integer|min:1']);
            $product = \App\Models\Product::findOrFail($productId);

            if ($product->productstock < $request->quantity) {
                return response()->json(['success' => false, 'message' => 'Stok tidak cukup']);
            }

            // Buat cart sementara untuk single item checkout
            $tempCart = Cart::create([
                'iduser' => $user->id,
                'checkoutstatus' => 'active'
            ]);

            // Tambahkan produk ke cart
            $tempCart->cartDetails()->create([
                'idproduct' => $product->id,
                'quantity' => $request->quantity,
                'productprice' => $product->productprice
            ]);

            // Redirect ke halaman checkout direct
            return response()->json([
                'success' => true,
                'redirect_url' => route('checkout.direct.view', ['cartId' => $tempCart->id])
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
