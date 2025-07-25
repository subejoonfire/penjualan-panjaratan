<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Model untuk detail keranjang belanja
     *
     * @var array
     */
    protected $fillable = [
        'idcart',
        'idproduct',
        'quantity',
        'productprice',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'productprice' => 'decimal:2',
    ];

    // Relasi ke Cart (Many to One)
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'idcart');
    }

    // Relasi ke Product (Many to One)
    public function product()
    {
        return $this->belongsTo(Product::class, 'idproduct');
    }

    // Helper method untuk mendapatkan subtotal
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->productprice;
    }

    // Helper method untuk mendapatkan subtotal format rupiah
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    // Helper method untuk mendapatkan harga format rupiah
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->productprice, 0, ',', '.');
    }
}
