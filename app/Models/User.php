<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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

    // Relasi ke Notification (One to Many)
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'iduser');
    }

    // Relasi ke unread notifications (One to Many)
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class, 'iduser')->where('readstatus', false);
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
