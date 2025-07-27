<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        
        // Remove notification view composer since navbar uses AJAX for dynamic loading
        // This prevents duplicate queries on every page load
    }
}
