<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Model untuk kategori produk
     *
     * @var array
     */
    protected $fillable = [
        'category',
    ];

    // Relasi ke Product (One to Many)
    public function products()
    {
        return $this->hasMany(Product::class, 'idcategories');
    }

    // Relasi ke produk aktif saja
    public function activeProducts()
    {
        return $this->hasMany(Product::class, 'idcategories')->where('is_active', true);
    }

    // Helper method untuk menghitung jumlah produk
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    // Helper method untuk menghitung jumlah produk aktif
    public function getActiveProductCountAttribute()
    {
        return $this->activeProducts()->count();
    }
}