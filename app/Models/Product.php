<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Model untuk produk
     *
     * @var array
     */
    protected $fillable = [
        'productname',
        'productdescription',
        'productprice',
        'productstock',
        'idcategories',
        'iduserseller',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'productprice' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relasi ke Category (Many to One)
    public function category()
    {
        return $this->belongsTo(Category::class, 'idcategories');
    }

    // Relasi ke User sebagai seller (Many to One)
    public function seller()
    {
        return $this->belongsTo(User::class, 'iduserseller');
    }

    // Relasi ke ProductImage (One to Many)
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'idproduct');
    }

    // Relasi ke gambar utama
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'idproduct')->where('is_primary', true);
    }

    // Relasi ke ProductReview (One to Many)
    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'idproduct');
    }

    // Relasi ke CartDetail (One to Many)
    public function cartDetails()
    {
        return $this->hasMany(CartDetail::class, 'idproduct');
    }

    // Helper method untuk mendapatkan harga format rupiah
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->productprice, 0, ',', '.');
    }

    // Helper method untuk mendapatkan rating rata-rata
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    // Helper method untuk mendapatkan total review
    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }

    // Helper method untuk mengecek ketersediaan stok
    public function isInStock()
    {
        return $this->productstock > 0 && $this->is_active;
    }

    // Helper method untuk mengurangi stok
    public function reduceStock($quantity)
    {
        if ($this->productstock >= $quantity) {
            $this->decrement('productstock', $quantity);
            return true;
        }
        return false;
    }

    // Helper method untuk menambah stok
    public function addStock($quantity)
    {
        $this->increment('productstock', $quantity);
    }

    // Scope untuk produk aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk produk dengan stok
    public function scopeInStock($query)
    {
        return $query->where('productstock', '>', 0);
    }

    // Scope untuk produk berdasarkan kategori
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('idcategories', $categoryId);
    }

    // Scope untuk produk berdasarkan seller
    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('iduserseller', $sellerId);
    }
}