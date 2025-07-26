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
            'quantity' => 'required|integer|min:1|max:' . $product->productstock
        ]);

        // Check if product is active
        if (!$product->is_active) {
            return back()->with('error', 'Produk tidak tersedia');
        }

        // Check stock
        if ($product->productstock < $request->quantity) {
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
            $newQuantity = $existingDetail->quantity + $request->quantity;

            if ($newQuantity > $product->productstock) {
                return back()->with('error', 'Stok tidak mencukupi');
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

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
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
            'payment_method' => 'required|in:transfer,cod,ewallet',
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
            $cart->update(['checkoutstatus' => 'checked_out']);

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
        $cart = $user->activeCart;

        $count = 0;
        if ($cart) {
            $count = $cart->cartDetails()->sum('quantity');
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
}
