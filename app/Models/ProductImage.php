<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Model untuk gambar produk
     *
     * @var array
     */
    protected $fillable = [
        'idproduct',
        'image',
        'is_primary',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // Relasi ke Product (Many to One)
    public function product()
    {
        return $this->belongsTo(Product::class, 'idproduct');
    }

    // Helper method untuk mendapatkan URL gambar lengkap
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }

    // Helper method untuk set sebagai gambar utama
    public function setAsPrimary()
    {
        // Set semua gambar produk lain menjadi false
        self::where('idproduct', $this->idproduct)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
        
        // Set gambar ini sebagai primary
        $this->update(['is_primary' => true]);
    }
}