<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     * Dashboard admin dengan statistik lengkap
     */
    public function index()
    {
        // Statistik umum
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalProducts = Product::count();
        $totalActiveProducts = Product::where('is_active', true)->count();
        $totalOrders = Order::count();
        $totalRevenue = Transaction::where('transactionstatus', 'paid')->sum('amount');

        // Statistik orders berdasarkan status
        $orderStats = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Statistik transaksi berdasarkan status
        $transactionStats = Transaction::select('transactionstatus', DB::raw('count(*) as total'))
            ->groupBy('transactionstatus')
            ->pluck('total', 'transactionstatus')
            ->toArray();
        // Revenue per bulan (6 bulan terakhir)
        $monthlyRevenue = Transaction::where('transactionstatus', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw("strftime('%m', created_at) as month"),
                DB::raw("strftime('%Y', created_at) as year"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        // Top selling products
        $topProducts = Product::withCount(['cartDetails as sold_quantity' => function ($query) {
            $query->whereHas('cart.order.transaction', function ($q) {
                $q->where('transactionstatus', 'paid');
            });
        }])
            ->orderBy('sold_quantity', 'desc')
            ->limit(10)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['cart.user', 'transaction'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Category statistics
        $categoryStats = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCustomers',
            'totalSellers',
            'totalProducts',
            'totalActiveProducts',
            'totalOrders',
            'totalRevenue',
            'orderStats',
            'transactionStats',
            'monthlyRevenue',
            'topProducts',
            'recentOrders',
            'categoryStats'
        ));
    }

    /**
     * Manage users
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by username or email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('nickname', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->withCount(['products', 'carts', 'notifications'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Manage products
     */
    public function products(Request $request)
    {
        $query = Product::with(['category', 'seller']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('idcategories', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by product name
        if ($request->filled('search')) {
            $query->where('productname', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Manage orders
     */
    public function orders(Request $request)
    {
        $query = Order::with(['cart.user', 'transaction']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Manage transactions
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with(['order.cart.user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('transactionstatus', $request->status);
        }

        // Search by transaction number
        if ($request->filled('search')) {
            $query->where('transaction_number', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Send notification to users
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'message' => 'required|string',
            'type' => 'required|in:order,payment,product,system',
            'target' => 'required|in:all,customers,sellers',
        ]);

        $query = User::query();

        if ($request->target === 'customers') {
            $query->where('role', 'customer');
        } elseif ($request->target === 'sellers') {
            $query->where('role', 'seller');
        }

        $users = $query->get();

        foreach ($users as $user) {
            Notification::create([
                'iduser' => $user->id,
                'title' => $request->title,
                'notification' => $request->message,
                'type' => $request->type,
                'readstatus' => false,
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil dikirim ke ' . $users->count() . ' pengguna.');
    }
}
