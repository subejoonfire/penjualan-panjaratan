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

        // Get payment methods from Duitku
        $paymentController = new \App\Http\Controllers\Customer\PaymentController();
        $request = new \Illuminate\Http\Request();
        $request->merge(['amount' => $total]);
        
        $paymentMethods = [];
        try {
            $response = $paymentController->getPaymentMethods($request);
            $paymentMethodsData = $response->getData();
            
            // Handle both array and object responses
            if (is_object($paymentMethodsData)) {
                $paymentMethodsData = (array) $paymentMethodsData;
            }
            
            // Check if there's an error in the response
            if (isset($paymentMethodsData['error'])) {
                \Log::info('Duitku payment methods error: ' . $paymentMethodsData['error']);
                $paymentMethods = [];
            } elseif (isset($paymentMethodsData['paymentFee']) && is_array($paymentMethodsData['paymentFee'])) {
                $paymentMethods = $paymentMethodsData['paymentFee'];
                \Log::info('Duitku payment methods loaded: ' . count($paymentMethods) . ' methods');
            } else {
                \Log::info('Duitku payment methods not found, using fallback');
                $paymentMethods = [];
            }
        } catch (\Exception $e) {
            \Log::error('Error getting payment methods: ' . $e->getMessage());
            $paymentMethods = [];
        }

        return view('customer.checkout', compact('cartDetails', 'subtotal', 'shippingCost', 'total', 'addresses', 'defaultAddress', 'cartId', 'paymentMethods'));
    }

    /**
     * Process checkout
     */
    public function processCheckout(Request $request)
    {
        try {
            $user = Auth::user();
            $cart = null;
            if ($request->filled('cart_id')) {
                $cart = Cart::where('id', $request->cart_id)->where('iduser', $user->id)->first();
            } else {
                $cart = $user->activeCart;
            }

            if (!$cart || $cart->cartDetails()->count() === 0) {
                return back()->with('error', 'Keranjang belanja kosong');
            }

            $request->validate([
                'address_id' => 'nullable|exists:user_addresses,id',
                'shipping_address' => 'nullable|string',
                'payment_method' => 'required|in:bank_transfer,credit_card,e_wallet,cod',
                'notes' => 'nullable|string|max:500'
            ]);

            // Get shipping address
            $shippingAddress = '';
            if ($request->filled('address_id')) {
                $address = $user->addresses()->find($request->address_id);
                if ($address) {
                    $shippingAddress = $address->address;
                }
            } elseif ($request->filled('shipping_address')) {
                $shippingAddress = $request->shipping_address;
            } else {
                return back()->withErrors(['shipping_address' => 'Alamat pengiriman harus diisi']);
            }

            if (empty($shippingAddress)) {
                return back()->withErrors(['shipping_address' => 'Alamat pengiriman harus diisi']);
            }

            DB::beginTransaction();

            $cartDetails = $cart->cartDetails()->with('product')->get();

            // Check stock again
            foreach ($cartDetails as $detail) {
                if ($detail->product->productstock < $detail->quantity) {
                    throw new \Exception('Stok tidak mencukupi untuk ' . $detail->product->productname);
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
                // Fallback: use random string
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
            return redirect()->route('customer.payments.pay', $transaction)
                ->with('success', 'Pesanan berhasil dibuat');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
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