<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Notification;
use App\Models\ProductReview;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display customer dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Customer statistics
        $totalOrders = Order::whereHas('cart', function($query) use ($user) {
            $query->where('iduser', $user->id);
        })->count();
        
        $totalSpent = Transaction::whereHas('order.cart', function($query) use ($user) {
            $query->where('iduser', $user->id);
        })->where('transactionstatus', 'paid')->sum('amount');
        
        $pendingOrders = Order::whereHas('cart', function($query) use ($user) {
            $query->where('iduser', $user->id);
        })->where('status', 'pending')->count();
        
        // Notifications - sudah tersedia dari AppServiceProvider
        
        // Recent orders
        $recentOrders = Order::with(['cart.cartDetails.product', 'transaction'])
            ->whereHas('cart', function($query) use ($user) {
                $query->where('iduser', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Cart items count
        $cartItemsCount = 0;
        $activeCart = $user->activeCart;
        if ($activeCart) {
            $cartItemsCount = $activeCart->cartDetails()->sum('quantity');
        }
        
        // Favorite products (most reviewed by user)
        $favoriteProducts = Product::whereHas('reviews', function($query) use ($user) {
            $query->where('iduser', $user->id);
        })->with('reviews')->limit(5)->get();
        
        return view('customer.dashboard', compact(
            'totalOrders',
            'totalSpent', 
            'pendingOrders',

            'recentOrders',
            'cartItemsCount',
            'favoriteProducts'
        ));
    }
    
    /**
     * Display customer orders
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['cart.cartDetails.product', 'transaction'])
            ->whereHas('cart', function($q) use ($user) {
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
     * Show order detail
     */
    public function orderDetail(Order $order)
    {
        $user = Auth::user();
        
        // Check if order belongs to user
        if ($order->cart->iduser !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        $order->load(['cart.cartDetails.product.images', 'transaction']);
        
        return view('customer.orders.show', compact('order'));
    }
    
    /**
     * Cancel order
     */
    public function cancelOrder(Order $order)
    {
        $user = Auth::user();
        
        // Check if order belongs to user
        if ($order->cart->iduser !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Order cannot be cancelled');
        }
        
        $order->update(['status' => 'cancelled']);
        
        // Create notification
        Notification::create([
            'iduser' => $user->id,
            'title' => 'Order Cancelled',
            'notification' => 'Your order #' . $order->order_number . ' has been cancelled',
            'type' => 'order',
            'readstatus' => false
        ]);
        
        return back()->with('success', 'Order cancelled successfully');
    }
    
    /**
     * Add product review
     */
    public function addReview(Request $request, Product $product)
    {
        $user = Auth::user();
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);
        
        // Check if user has purchased this product
        $hasPurchased = Order::whereHas('cart', function($query) use ($user) {
            $query->where('iduser', $user->id);
        })->whereHas('cart.cartDetails', function($query) use ($product) {
            $query->where('idproduct', $product->id);
        })->whereHas('transaction', function($query) {
            $query->where('transactionstatus', 'paid');
        })->exists();
        
        if (!$hasPurchased) {
            return back()->with('error', 'You can only review products you have purchased');
        }
        
        // Check if user already reviewed this product
        $existingReview = ProductReview::where('iduser', $user->id)
            ->where('idproduct', $product->id)
            ->first();
        
        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'review' => $request->review
            ]);
            
            return back()->with('success', 'Review updated successfully');
        } else {
            ProductReview::create([
                'iduser' => $user->id,
                'idproduct' => $product->id,
                'rating' => $request->rating,
                'review' => $request->review
            ]);
            
            return back()->with('success', 'Review added successfully');
        }
    }
    
    /**
     * Display notifications
     */
    public function notifications()
    {
        // Data notifikasi sudah tersedia dari AppServiceProvider
        // Gunakan $userNotifications yang sudah di-share
        return view('customer.notifications.index');
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();
        
        if ($notification->iduser !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        $notification->update(['readstatus' => true]);
        
        return back()->with('success', 'Notification marked as read');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        $user->notifications()
            ->where('readstatus', false)
            ->update(['readstatus' => true]);
        
        return back()->with('success', 'All notifications marked as read');
    }
}
