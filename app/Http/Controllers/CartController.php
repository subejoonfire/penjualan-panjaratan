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
        
        // Cache cart data for 1 minute for better UX
        $cacheKey = "cart_display_{$user->id}";
        $cartData = cache()->remember($cacheKey, 60, function () use ($user) {
            $cart = $user->activeCart;

            if (!$cart) {
                return [
                    'cartDetails' => collect(),
                    'subtotal' => 0,
                    'itemCount' => 0,
                    'isEmpty' => true
                ];
            }

            // Optimized query with minimal data
            $cartDetails = $cart->cartDetails()
                ->with([
                    'product:id,productname,productprice,productstock,is_active',
                    'product.images:id,idproduct,imagepath' => function ($query) {
                        $query->take(1);
                    },
                    'product.seller:id,username'
                ])
                ->get();

            // Calculate totals efficiently
            $subtotal = $cartDetails->sum(function ($detail) {
                return $detail->quantity * $detail->productprice;
            });

            return [
                'cartDetails' => $cartDetails,
                'subtotal' => $subtotal,
                'itemCount' => $cartDetails->sum('quantity'),
                'isEmpty' => $cartDetails->isEmpty()
            ];
        });

        // Constants that don't need caching
        $shippingCost = 15000;
        $tax = 0;
        $total = $cartData['subtotal'] + $shippingCost + $tax;

        return view('customer.cart.index', array_merge($cartData, [
            'shippingCost' => $shippingCost,
            'tax' => $tax,
            'total' => $total
        ]));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, Product $product)
    {
        $user = Auth::user();

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . max(1, $product->productstock)
        ], [
            'quantity.required' => 'Jumlah produk harus diisi',
            'quantity.integer' => 'Jumlah produk harus berupa angka',
            'quantity.min' => 'Jumlah produk minimal 1',
            'quantity.max' => 'Jumlah produk melebihi stok yang tersedia'
        ]);

        // Check if product is active and in stock
        if (!$product->is_active || $product->productstock <= 0) {
            return back()->with('error', 'Produk tidak tersedia atau stok habis');
        }

        // Rate limiting for cart additions (prevent spam)
        $rateLimitKey = 'cart_add:' . $user->id;
        if (cache()->get($rateLimitKey, 0) >= 10) {
            return back()->with('error', 'Terlalu banyak percobaan menambah produk. Coba lagi dalam 1 menit.');
        }

        DB::beginTransaction();
        try {
            // Lock product for stock checking
            $product = Product::lockForUpdate()->find($product->id);
            
            // Recheck stock after lock
            if ($product->productstock < $request->quantity) {
                throw new \Exception('Stok tidak mencukupi');
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
                ->lockForUpdate()
                ->first();

            if ($existingDetail) {
                $newQuantity = $existingDetail->quantity + $request->quantity;

                if ($newQuantity > $product->productstock) {
                    throw new \Exception('Total jumlah di keranjang melebihi stok yang tersedia');
                }

                $existingDetail->update(['quantity' => $newQuantity]);
            } else {
                CartDetail::create([
                    'idcart' => $cart->id,
                    'idproduct' => $product->id,
                    'quantity' => $request->quantity,
                    'productprice' => $product->productprice
                ]);
            }

            DB::commit();
            
            // Clear cache for cart count
            cache()->forget("cart_count_{$user->id}");
            
            return back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            // Increment rate limiting on error
            $attempts = cache()->get($rateLimitKey, 0) + 1;
            cache()->put($rateLimitKey, $attempts, 60);
            
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, CartDetail $cartDetail)
    {
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
            return back()->with('error', 'Stok tidak mencukupi');
        }

        $cartDetail->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Keranjang berhasil diperbarui');
    }

    /**
     * Remove item from cart
     */
    public function remove(CartDetail $cartDetail)
    {
        $user = Auth::user();

        // Check if cart detail belongs to user
        if ($cartDetail->cart->iduser !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $cartDetail->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $user = Auth::user();
        $cart = $user->activeCart;

        if ($cart) {
            $cart->cartDetails()->delete();
        }

        return back()->with('success', 'Keranjang berhasil dikosongkan');
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->activeCart;

        if (!$cart || $cart->cartDetails()->count() === 0) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang belanja kosong');
        }

        $cartDetails = $cart->cartDetails()->with(['product.images'])->get();

        // Check stock for all items
        foreach ($cartDetails as $detail) {
            if ($detail->product->productstock < $detail->quantity) {
                return redirect()->route('customer.cart.index')
                    ->with('error', 'Stok tidak mencukupi untuk ' . $detail->product->productname);
            }
        }

        $subtotal = $cartDetails->sum(function ($detail) {
            return $detail->quantity * $detail->productprice;
        });

        $shippingCost = 15000; // Fixed shipping cost
        $total = $subtotal + $shippingCost;

        $addresses = $user->addresses;
        $defaultAddress = $user->defaultAddress();

        return view('customer.checkout', compact(
            'cartDetails',
            'subtotal',
            'shippingCost',
            'total',
            'addresses',
            'defaultAddress'
        ));
    }

    /**
     * Process checkout - Enhanced with better security and performance
     */
    public function processCheckout(Request $request)
    {
        $user = Auth::user();
        
        // Rate limiting checkout attempts
        $rateLimitKey = 'checkout_attempts:' . $user->id;
        if (cache()->get($rateLimitKey, 0) >= 3) {
            return back()->with('error', 'Terlalu banyak percobaan checkout. Coba lagi dalam 5 menit.');
        }

        $cart = $user->activeCart;
        if (!$cart || $cart->cartDetails()->count() === 0) {
            return back()->with('error', 'Keranjang belanja kosong');
        }

        $request->validate([
            'address_id' => 'nullable|exists:user_addresses,id',
            'shipping_address' => 'nullable|string|max:500',
            'payment_method' => 'required|in:bank_transfer,credit_card,e_wallet,cod',
            'notes' => 'nullable|string|max:500'
        ], [
            'payment_method.required' => 'Metode pembayaran harus dipilih',
            'payment_method.in' => 'Metode pembayaran tidak valid',
            'shipping_address.max' => 'Alamat pengiriman terlalu panjang',
            'notes.max' => 'Catatan terlalu panjang'
        ]);

        // Validate and get shipping address
        $shippingAddress = $this->getValidatedShippingAddress($request, $user);
        if (!$shippingAddress) {
            return back()->withErrors(['shipping_address' => 'Alamat pengiriman harus diisi']);
        }

        DB::beginTransaction();
        try {
            // Lock cart for processing
            $cart = Cart::lockForUpdate()->find($cart->id);
            
            // Get cart details with product lock
            $cartDetails = $cart->cartDetails()
                ->with(['product' => function ($query) {
                    $query->lockForUpdate();
                }])
                ->get();

            if ($cartDetails->isEmpty()) {
                throw new \Exception('Keranjang belanja kosong');
            }

            // Comprehensive stock validation
            $stockErrors = [];
            foreach ($cartDetails as $detail) {
                $product = $detail->product;
                if (!$product->is_active) {
                    $stockErrors[] = $product->productname . ' tidak tersedia';
                } elseif ($product->productstock < $detail->quantity) {
                    $stockErrors[] = $product->productname . ' stok tidak mencukupi (tersisa: ' . $product->productstock . ')';
                }
            }

            if (!empty($stockErrors)) {
                throw new \Exception('Error stok: ' . implode(', ', $stockErrors));
            }

            // Calculate totals with validation
            $subtotal = $cartDetails->sum(function ($detail) {
                return $detail->quantity * $detail->productprice;
            });

            if ($subtotal <= 0) {
                throw new \Exception('Total belanja tidak valid');
            }

            $shippingCost = 15000;
            $total = $subtotal + $shippingCost;

            // Generate unique order numbers
            $orderNumber = $this->generateOrderNumber($user->id);
            $transactionNumber = $this->generateTransactionNumber($user->id);

            // Create order with enhanced data
            $order = Order::create([
                'idcart' => $cart->id,
                'order_number' => $orderNumber,
                'grandtotal' => $total,
                'shipping_address' => $shippingAddress,
                'status' => 'pending',
                'notes' => $request->notes ? trim($request->notes) : null
            ]);

            // Create transaction
            $transaction = Transaction::create([
                'idorder' => $order->id,
                'transaction_number' => $transactionNumber,
                'amount' => $total,
                'payment_method' => $request->payment_method,
                'transactionstatus' => 'pending'
            ]);

            // Update product stock atomically
            foreach ($cartDetails as $detail) {
                $affected = $detail->product->where('id', $detail->product->id)
                    ->where('productstock', '>=', $detail->quantity)
                    ->decrement('productstock', $detail->quantity);
                
                if ($affected === 0) {
                    throw new \Exception('Stok ' . $detail->product->productname . ' telah habis');
                }
            }

            // Update cart status
            $cart->update(['checkoutstatus' => 'completed']);

            // Create notification asynchronously
            dispatch(function () use ($user, $order) {
                Notification::create([
                    'iduser' => $user->id,
                    'title' => 'Pesanan Dibuat',
                    'notification' => 'Pesanan #' . $order->order_number . ' berhasil dibuat dengan total ' . number_format($order->grandtotal),
                    'type' => 'order',
                    'readstatus' => false
                ]);
            })->afterResponse();

            // Clear relevant caches
            cache()->forget("cart_count_{$user->id}");
            cache()->forget("cart_display_{$user->id}");
            cache()->forget("customer_dashboard_{$user->id}");

            DB::commit();

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat! Nomor pesanan: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollback();
            
            // Increment rate limiting on error
            $attempts = cache()->get($rateLimitKey, 0) + 1;
            cache()->put($rateLimitKey, $attempts, 300); // 5 minutes
            
            \Log::error('Checkout failed for user ' . $user->id . ': ' . $e->getMessage());
            
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Validate and get shipping address
     */
    private function getValidatedShippingAddress(Request $request, $user)
    {
        if ($request->filled('address_id')) {
            $address = $user->addresses()->find($request->address_id);
            return $address ? $address->address : null;
        }
        
        if ($request->filled('shipping_address')) {
            return trim($request->shipping_address);
        }
        
        return null;
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber($userId)
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad($userId, 4, '0', STR_PAD_LEFT) . '-' . mt_rand(1000, 9999);
        } while (Order::where('order_number', $orderNumber)->exists());
        
        return $orderNumber;
    }

    /**
     * Generate unique transaction number
     */
    private function generateTransactionNumber($userId)
    {
        do {
            $transactionNumber = 'TRX-' . date('Ymd') . '-' . str_pad($userId, 4, '0', STR_PAD_LEFT) . '-' . mt_rand(1000, 9999);
        } while (Transaction::where('transaction_number', $transactionNumber)->exists());
        
        return $transactionNumber;
    }

    /**
     * Get cart count for AJAX
     */
    public function getCartCount()
    {
        $user = Auth::user();
        
        // Cache cart count for 2 minutes to reduce database queries
        $cacheKey = "cart_count_{$user->id}";
        $count = cache()->remember($cacheKey, 120, function () use ($user) {
            $cart = $user->activeCart;
            return $cart ? $cart->cartDetails()->count() : 0;
        });

        return response()->json([
            'count' => $count,
            'cache_time' => now()->toISOString()
        ]);
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
}