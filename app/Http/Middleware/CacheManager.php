<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheManager
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Clear relevant cache after successful POST/PUT/DELETE operations
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $this->clearRelevantCache($request);
        }

        return $response;
    }

    /**
     * Clear cache based on the request route
     */
    private function clearRelevantCache(Request $request)
    {
        $method = $request->method();
        $route = $request->route()->getName();

        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return;
        }

        // Clear product-related cache
        if (str_contains($route, 'products')) {
            $this->clearProductCache($request);
        }

        // Clear cart-related cache
        if (str_contains($route, 'cart')) {
            $this->clearCartCache($request);
        }

        // Clear order-related cache
        if (str_contains($route, 'orders')) {
            $this->clearOrderCache($request);
        }

        // Clear user-related cache
        if (str_contains($route, 'profile') || str_contains($route, 'notifications')) {
            $this->clearUserCache($request);
        }
    }

    /**
     * Clear product-related cache
     */
    private function clearProductCache(Request $request)
    {
        // Clear product listing cache
        Cache::tags(['products'])->flush();
        
        // Clear categories cache
        Cache::forget('categories_with_product_count');
        Cache::forget('products_price_range');

        // Clear specific product cache if product ID is available
        if ($productId = $request->route('product')) {
            if (is_object($productId)) {
                $productId = $productId->id;
            }
            Cache::forget("product_details_{$productId}");
            Cache::forget("product_stats_{$productId}");
            Cache::forget("product_sold_count_{$productId}");
        }

        // Clear seller dashboard cache if seller is updating products
        if (auth()->check() && auth()->user()->isSeller()) {
            Cache::forget("seller_dashboard_stats_" . auth()->id());
        }
    }

    /**
     * Clear cart-related cache
     */
    private function clearCartCache(Request $request)
    {
        if (auth()->check()) {
            $userId = auth()->id();
            Cache::forget("cart_count_{$userId}");
            Cache::forget("cart_display_{$userId}");
            
            // Clear customer dashboard cache
            if (auth()->user()->isCustomer()) {
                Cache::forget("customer_dashboard_{$userId}");
            }
        }
    }

    /**
     * Clear order-related cache
     */
    private function clearOrderCache(Request $request)
    {
        if (auth()->check()) {
            $userId = auth()->id();
            
            // Clear user-specific caches
            Cache::forget("customer_dashboard_{$userId}");
            Cache::forget("seller_dashboard_stats_{$userId}");
            Cache::forget("user_stats_{$userId}");

            // Clear product sold count cache if order status changes
            if ($orderId = $request->route('order')) {
                if (is_object($orderId)) {
                    $order = $orderId;
                } else {
                    $order = \App\Models\Order::find($orderId);
                }

                if ($order) {
                    foreach ($order->cart->cartDetails as $detail) {
                        Cache::forget("product_sold_count_{$detail->idproduct}");
                    }
                }
            }
        }
    }

    /**
     * Clear user-related cache
     */
    private function clearUserCache(Request $request)
    {
        if (auth()->check()) {
            $userId = auth()->id();
            
            // Clear notification cache
            Cache::forget("user_notifications_count_{$userId}");
            Cache::forget("user_recent_notifications_{$userId}");
            
            // Clear dashboard cache
            Cache::forget("customer_dashboard_{$userId}");
            Cache::forget("seller_dashboard_stats_{$userId}");
            Cache::forget("user_stats_{$userId}");
        }
    }
}