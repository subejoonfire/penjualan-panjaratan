<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Model untuk notifikasi
     *
     * @var array
     */
    protected $fillable = [
        'iduser',
        'title',
        'notification',
        'type',
        'readstatus',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'readstatus' => 'boolean',
    ];

    // Relasi ke User (Many to One)
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser');
    }

    // Helper method untuk mendapatkan type dalam bahasa Indonesia
    public function getTypeLabelAttribute()
    {
        $types = [
            'order' => 'Pesanan',
            'payment' => 'Pembayaran',
            'product' => 'Produk',
            'system' => 'Sistem'
        ];

        return $types[$this->type] ?? $this->type;
    }

    // Helper method untuk mengecek apakah sudah dibaca
    public function isRead()
    {
        return $this->readstatus;
    }

    // Helper method untuk mark sebagai dibaca
    public function markAsRead()
    {
        $this->update(['readstatus' => true]);
    }

    // Helper method untuk mark sebagai belum dibaca
    public function markAsUnread()
    {
        $this->update(['readstatus' => false]);
    }

    // Helper method untuk mendapatkan waktu relatif
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Scope untuk notifikasi yang belum dibaca
    public function scopeUnread($query)
    {
        return $query->where('readstatus', false);
    }

    // Scope untuk notifikasi yang sudah dibaca
    public function scopeRead($query)
    {
        return $query->where('readstatus', true);
    }

    // Scope berdasarkan type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope untuk notifikasi terbaru
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}