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
     * Process checkout
     */
    public function processCheckout(Request $request)
    {
        $user = Auth::user();
        $cart = $user->activeCart;

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

        DB::beginTransaction();

        try {
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

            // Create order
            $order = Order::create([
                'idcart' => $cart->id,
                'order_number' => 'ORD-' . time() . '-' . $user->id,
                'grandtotal' => $total,
                'shipping_address' => $shippingAddress,
                'status' => 'pending',
                'notes' => $request->notes
            ]);

            // Create transaction
            $transaction = Transaction::create([
                'idorder' => $order->id,
                'transaction_number' => 'TRX-' . time() . '-' . $user->id,
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

            return redirect()->route('customer.orders.show', $order)
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