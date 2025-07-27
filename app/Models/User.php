<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * Atribut yang dapat diisi untuk model User
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'nickname',
        'password',
        'role',
        'email_verified_at',
        'phone_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke UserAddress (One to Many)
    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'iduser');
    }

    // Relasi ke Product sebagai seller (One to Many)
    public function products()
    {
        return $this->hasMany(Product::class, 'iduserseller');
    }

    // Relasi ke ProductReview (One to Many)
    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'iduser');
    }

    // Relasi ke Cart (One to Many)
    public function carts()
    {
        return $this->hasMany(Cart::class, 'iduser');
    }

    // Relasi ke Cart aktif (One to One)
    public function activeCart()
    {
        return $this->hasOne(Cart::class, 'iduser')->where('checkoutstatus', 'active');
    }

    // Relasi ke Cart aktif sebagai property
    public function cart()
    {
        return $this->activeCart();
    }

    // Relasi ke Order melalui Cart (Has Many Through)
    public function orders()
    {
        return $this->hasManyThrough(Order::class, Cart::class, 'iduser', 'idcart', 'id', 'id');
    }

    /**
     * Get notifications for the user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'iduser');
    }

    /**
     * Get unread notifications for the user
     */
    public function unreadNotifications()
    {
        return $this->notifications()->where('readstatus', false);
    }

    /**
     * Get unread notification count efficiently with caching
     */
    public function getUnreadNotificationCountAttribute()
    {
        return cache()->remember("user_notifications_count_{$this->id}", 300, function () {
            return $this->unreadNotifications()->count();
        });
    }

    /**
     * Get recent notifications for navbar/dropdown with caching
     */
    public function getRecentNotifications($limit = 5)
    {
        return cache()->remember("user_recent_notifications_{$this->id}", 180, function () use ($limit) {
            return $this->notifications()
                ->select(['id', 'title', 'notification', 'type', 'readstatus', 'created_at'])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear notification cache when updated
     */
    public function clearNotificationCache()
    {
        cache()->forget("user_notifications_count_{$this->id}");
        cache()->forget("user_recent_notifications_{$this->id}");
    }

    /**
     * Get user statistics efficiently
     */
    public function getStatsAttribute()
    {
        return cache()->remember("user_stats_{$this->id}", 600, function () {
            $stats = [];
            
            if ($this->isCustomer()) {
                $orderStats = $this->orders()
                    ->selectRaw('
                        COUNT(*) as total_orders,
                        SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders,
                        SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as completed_orders,
                        SUM(grandtotal) as total_spent
                    ')
                    ->first();
                
                $stats = [
                    'total_orders' => $orderStats->total_orders ?? 0,
                    'pending_orders' => $orderStats->pending_orders ?? 0,
                    'completed_orders' => $orderStats->completed_orders ?? 0,
                    'total_spent' => $orderStats->total_spent ?? 0,
                    'wishlist_count' => $this->wishlists()->count(),
                ];
            } elseif ($this->isSeller()) {
                $productStats = $this->products()
                    ->selectRaw('
                        COUNT(*) as total_products,
                        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products,
                        SUM(productstock) as total_stock
                    ')
                    ->first();
                
                $stats = [
                    'total_products' => $productStats->total_products ?? 0,
                    'active_products' => $productStats->active_products ?? 0,
                    'total_stock' => $productStats->total_stock ?? 0,
                ];
            }
            
            return $stats;
        });
    }

    // Relasi ke Wishlist (One to Many)
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }

    // Helper method untuk mengecek role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSeller()
    {
        return $this->role === 'seller';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    // Helper method untuk mendapatkan alamat default
    public function defaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }
}
