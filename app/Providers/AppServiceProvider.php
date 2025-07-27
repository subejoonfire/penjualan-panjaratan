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
        
        // Share notification data dengan semua view
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                
                // Share data notifikasi untuk semua view
                $view->with([
                    'unreadNotifications' => $user->unreadNotifications()->count(),
                    'allNotifications' => $user->notifications()->latest()->limit(5)->get(),
                    'userNotifications' => $user->notifications()->latest()->paginate(10)
                ]);
            }
        });
    }
}
