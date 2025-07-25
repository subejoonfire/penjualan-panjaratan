<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Model untuk detail transaksi
     *
     * @var array
     */
    protected $fillable = [
        'idtransaction',
        'productdescription',
        'amount',
        'type',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relasi ke Transaction (Many to One)
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'idtransaction');
    }

    // Helper method untuk mendapatkan amount format rupiah
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Helper method untuk mendapatkan type dalam bahasa Indonesia
    public function getTypeLabelAttribute()
    {
        $types = [
            'product' => 'Produk',
            'shipping' => 'Ongkos Kirim',
            'tax' => 'Pajak',
            'discount' => 'Diskon'
        ];

        return $types[$this->type] ?? $this->type;
    }

    // Scope berdasarkan type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
