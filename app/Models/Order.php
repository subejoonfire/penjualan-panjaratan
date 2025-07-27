<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * Model untuk pesanan
     *
     * @var array
     */
    protected $fillable = [
        'idcart',
        'order_number',
        'grandtotal',
        'status',
        'status_updated_at',
        'shipping_address',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'grandtotal' => 'decimal:2',
    ];

    // Relasi ke Cart (Many to One)
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'idcart');
    }

    // Relasi ke Transaction (One to One)
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'idorder');
    }

    // Relasi ke User melalui Cart
    public function user()
    {
        return $this->hasOneThrough(User::class, Cart::class, 'id', 'id', 'idcart', 'iduser');
    }

    // Helper method untuk mendapatkan grandtotal format rupiah
    public function getFormattedGrandtotalAttribute()
    {
        return 'Rp ' . number_format($this->grandtotal, 0, ',', '.');
    }

    // Helper method untuk mendapatkan status dalam bahasa Indonesia
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Konfirmasi',
            'processing' => 'Sedang Diproses',
            'shipped' => 'Dalam Pengiriman',
            'delivered' => 'Telah Diterima',
            'cancelled' => 'Dibatalkan'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // Helper method untuk mengecek apakah order bisa dibatalkan
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    // Helper method untuk update status
    public function updateStatus($status)
    {
        $this->update(['status' => $status]);
    }

    // Scope berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk order pending
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope untuk order yang sudah selesai
    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }
}