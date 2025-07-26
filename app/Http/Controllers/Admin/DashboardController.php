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
        $query = Transaction::with(['order.cart.user', 'order.cart.cartDetails.product.images', 'order.cart.cartDetails.product.seller']);

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
     * Get transaction details for modal
     */
    public function transactionDetails(Transaction $transaction)
    {
        $transaction->load([
            'order.cart.user', 
            'order.cart.cartDetails.product.images', 
            'order.cart.cartDetails.product.seller'
        ]);
        
        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'html' => view('admin.transactions.details', compact('transaction'))->render()
        ]);
    }

    /**
     * Update transaction status
     */
    public function updateTransactionStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled,failed'
        ]);

        $transaction->update(['transactionstatus' => $request->status]);

        // If transaction is paid, also update order status to processing
        if ($request->status === 'paid' && $transaction->order) {
            $transaction->order->update(['status' => 'processing']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status transaksi berhasil diperbarui',
            'transaction' => $transaction
        ]);
    }

    /**
     * Delete transaction
     */
    public function deleteTransaction(Transaction $transaction)
    {
        try {
            $transaction->delete();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export transactions
     */
    public function exportTransactions(Request $request)
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

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $filename = 'transactions_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID Transaksi',
                'Nomor Transaksi', 
                'Pelanggan',
                'Email',
                'Metode Pembayaran',
                'Jumlah',
                'Status',
                'Tanggal Transaksi'
            ]);

            // CSV Data
            foreach ($transactions as $transaction) {
                $customerName = $transaction->order && $transaction->order->cart && $transaction->order->cart->user 
                    ? $transaction->order->cart->user->username 
                    : 'N/A';
                $customerEmail = $transaction->order && $transaction->order->cart && $transaction->order->cart->user 
                    ? $transaction->order->cart->user->email 
                    : 'N/A';

                fputcsv($file, [
                    $transaction->id,
                    $transaction->transaction_number,
                    $customerName,
                    $customerEmail,
                    $transaction->payment_method,
                    $transaction->amount,
                    $transaction->transactionstatus,
                    $transaction->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

    /**
     * Get order details for modal
     */
    public function orderDetails(Order $order)
    {
        $order->load([
            'cart.user', 
            'cart.cartDetails.product.images', 
            'cart.cartDetails.product.category',
            'cart.cartDetails.product.seller',
            'transaction'
        ]);
        
        return response()->json([
            'success' => true,
            'order' => $order,
            'html' => view('admin.orders.details', compact('order'))->render()
        ]);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui',
            'order' => $order
        ]);
    }

    /**
     * Toggle product status (active/inactive)
     */
    public function toggleProductStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        
        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()->with('success', "Produk berhasil {$status}");
    }
}
