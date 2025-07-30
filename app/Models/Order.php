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
        'shipping_address',
        'status_updated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'grandtotal' => 'decimal:2',
        'status_updated_at' => 'datetime',
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
        $this->update([
            'status' => $status,
            'status_updated_at' => now()
        ]);
    }

    // Helper method untuk mengecek apakah status bisa diupdate dalam 3 jam
    public function canUpdateStatus($targetStatus)
    {
        // Jika status sudah delivered/cancelled, tidak bisa diupdate
        if (in_array($this->status, ['delivered', 'cancelled'])) {
            return false;
        }

        $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
        $currentIdx = array_search($this->status, $statusOrder);
        $targetIdx = array_search($targetStatus, $statusOrder);

        if ($currentIdx === false || $targetIdx === false) {
            return false;
        }

        // Jika ingin mundur status, tidak bisa
        if ($targetIdx < $currentIdx) {
            return false;
        }

        // Jika ingin ke status yang sama atau sebelumnya dan sudah 3 jam, tidak bisa
        $statusUpdatedAt = $this->status_updated_at ?? $this->created_at;
        $diffHours = $statusUpdatedAt->diffInHours(now());
        
        if ($diffHours >= 3 && $targetIdx <= $currentIdx) {
            return false;
        }

        return true;
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

    // Helper method untuk mengecek apakah order masih bisa diupdate (dalam 6 jam)
    public function canBeUpdated()
    {
        // Jika status sudah delivered atau cancelled, tidak bisa diupdate
        if (in_array($this->status, ['delivered', 'cancelled'])) {
            return false;
        }

        // Jika masih pending, selalu bisa diupdate
        if ($this->status === 'pending') {
            return true;
        }

        // Untuk status lainnya, cek apakah masih dalam 6 jam
        $sixHoursAgo = now()->subHours(6);
        return $this->updated_at->gt($sixHoursAgo);
    }

    // Helper method untuk mendapatkan waktu tersisa update (dalam menit)
    public function getRemainingUpdateTimeAttribute()
    {
        if (!$this->canBeUpdated() || $this->status === 'pending') {
            return 0;
        }

        $sixHoursFromUpdate = $this->updated_at->addHours(6);
        $now = now();
        
        if ($sixHoursFromUpdate->gt($now)) {
            return $now->diffInMinutes($sixHoursFromUpdate);
        }
        
        return 0;
    }
}