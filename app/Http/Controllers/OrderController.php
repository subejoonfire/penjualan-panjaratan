<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders based on user role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            return $this->adminIndex($request);
        } elseif ($user->role === 'seller') {
            return $this->sellerIndex($request);
        } else {
            return $this->customerIndex($request);
        }
    }

    /**
     * Admin order listing
     */
    private function adminIndex(Request $request)
    {
        $query = Order::with(['cart.user', 'cart.cartDetails.product', 'transaction']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('cart.user', function ($userQuery) use ($request) {
                      $userQuery->where('username', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Seller order listing
     */
    private function sellerIndex(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['cart.user', 'cart.cartDetails.product', 'transaction'])
            ->whereHas('cart.cartDetails.product', function ($q) use ($user) {
                $q->where('iduserseller', $user->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('seller.orders.index', compact('orders'));
    }

    /**
     * Customer order listing
     */
    private function customerIndex(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['cart.cartDetails.product', 'transaction'])
            ->whereHas('cart', function ($q) use ($user) {
                $q->where('iduser', $user->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order (typically handled by checkout)
     */
    public function create()
    {
        // This is typically handled by the cart checkout process
        return redirect()->route('customer.cart.index');
    }

    /**
     * Store a newly created order (typically handled by checkout)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $cart = $user->activeCart;

        if (!$cart || $cart->cartDetails()->count() === 0) {
            return back()->with('error', 'Keranjang kosong');
        }

        $request->validate([
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:transfer,cod,ewallet',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $cartDetails = $cart->cartDetails()->with('product')->get();

            // Check stock
            foreach ($cartDetails as $detail) {
                if ($detail->product->productstock < $detail->quantity) {
                    throw new \Exception('Stok tidak mencukupi untuk ' . $detail->product->productname);
                }
            }

            $subtotal = $cartDetails->sum(function ($detail) {
                return $detail->quantity * $detail->product->productprice;
            });

            $shippingCost = 15000;
            $total = $subtotal + $shippingCost;

            // Create order
            $order = Order::create([
                'idcart' => $cart->id,
                'order_number' => 'ORD-' . time() . '-' . $user->id,
                'grandtotal' => $total,
                'shipping_address' => $request->shipping_address,
                'status' => 'pending',
                'notes' => $request->notes
            ]);

            // Create transaction
            Transaction::create([
                'idorder' => $order->id,
                'transaction_number' => 'TRX-' . time() . '-' . $user->id,
                'amount' => $total,
                'paymentmethod' => $request->payment_method,
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
                'notification' => 'Pesanan Anda #' . $order->order_number . ' berhasil dibuat',
                'type' => 'order',
                'readstatus' => false
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Check authorization
        if ($user->role === 'customer') {
            if ($order->cart->iduser !== $user->id) {
                abort(403, 'Tidak diizinkan');
            }
        } elseif ($user->role === 'seller') {
            $hasSellerProducts = $order->cart->cartDetails()
                ->whereHas('product', function ($query) use ($user) {
                    $query->where('iduserseller', $user->id);
                })->exists();
            
            if (!$hasSellerProducts) {
                abort(403, 'Tidak diizinkan');
            }
        }

        $order->load(['cart.user', 'cart.cartDetails.product.images', 'transaction']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $user = Auth::user();
        
        // Only admin can edit orders
        if ($user->role !== 'admin') {
            abort(403, 'Tidak diizinkan');
        }

        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->adminUpdate($request, $order);
        } elseif ($user->role === 'seller') {
            return $this->sellerUpdate($request, $order);
        } else {
            return $this->customerUpdate($request, $order);
        }
    }

    /**
     * Admin order update
     */
    private function adminUpdate(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        $order->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        // Create notification
        Notification::create([
            'iduser' => $order->cart->iduser,
            'title' => 'Status Pesanan Diperbarui',
            'notification' => 'Status pesanan #' . $order->order_number . ' diubah menjadi ' . $request->status,
            'type' => 'order',
            'readstatus' => false
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui');
    }

    /**
     * Seller order update
     */
    private function sellerUpdate(Request $request, Order $order)
    {
        $user = Auth::user();

        // Check if order contains seller's products
        $hasSellerProducts = $order->cart->cartDetails()
            ->whereHas('product', function ($query) use ($user) {
                $query->where('iduserseller', $user->id);
            })->exists();

        if (!$hasSellerProducts) {
            abort(403, 'Tidak diizinkan');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        // Create notification
        Notification::create([
            'iduser' => $order->cart->iduser,
            'title' => 'Status Pesanan Diperbarui',
            'notification' => 'Status pesanan #' . $order->order_number . ' diubah menjadi ' . $request->status,
            'type' => 'order',
            'readstatus' => false
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui');
    }

    /**
     * Customer order update (cancel only)
     */
    private function customerUpdate(Request $request, Order $order)
    {
        $user = Auth::user();

        // Check if order belongs to user
        if ($order->cart->iduser !== $user->id) {
            abort(403, 'Tidak diizinkan');
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan');
        }

        $order->update(['status' => 'cancelled']);

        // Create notification
        Notification::create([
            'iduser' => $user->id,
            'title' => 'Pesanan Dibatalkan',
            'notification' => 'Pesanan #' . $order->order_number . ' telah dibatalkan',
            'type' => 'order',
            'readstatus' => false
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan');
    }

    /**
     * Remove the specified order (admin only)
     */
    public function destroy(Order $order)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Tidak diizinkan');
        }

        // Cannot delete orders with paid transactions
        if ($order->transaction && $order->transaction->transactionstatus === 'paid') {
            return back()->with('error', 'Tidak dapat menghapus pesanan yang sudah dibayar');
        }

        $orderNumber = $order->order_number;
        
        // Delete related transaction first
        if ($order->transaction) {
            $order->transaction->delete();
        }
        
        $order->delete();

        return back()->with('success', "Pesanan {$orderNumber} berhasil dihapus");
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        return $this->update($request, $order);
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        $user = Auth::user();

        // Check if order belongs to user (for customers)
        if ($user->role === 'customer' && $order->cart->iduser !== $user->id) {
            abort(403, 'Tidak diizinkan');
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan');
        }

        $order->update(['status' => 'cancelled']);

        // Create notification
        Notification::create([
            'iduser' => $order->cart->iduser,
            'title' => 'Pesanan Dibatalkan',
            'notification' => 'Pesanan #' . $order->order_number . ' telah dibatalkan',
            'type' => 'order',
            'readstatus' => false
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan');
    }
}
