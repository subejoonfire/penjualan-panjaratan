<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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
        'view_count',

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

    // Helper method untuk increment view count
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    // Helper method untuk mendapatkan jumlah terjual (cached)
    public function getSoldCountAttribute()
    {
        return cache()->remember("product_sold_count_{$this->id}", 1800, function () {
            return $this->cartDetails()
                ->whereHas('cart.order', function ($query) {
                    $query->where('status', 'delivered');
                })
                ->sum('quantity');
        });
    }

    // Optimized scope untuk sold count dengan subquery
    public function scopeWithSoldCount($query)
    {
        return $query->addSelect([
            'sold_count' => DB::table('cart_details')
                ->select(DB::raw('COALESCE(SUM(quantity), 0)'))
                ->join('carts', 'cart_details.idcart', '=', 'carts.id')
                ->join('orders', 'carts.id', '=', 'orders.idcart')
                ->whereColumn('cart_details.idproduct', 'products.id')
                ->where('orders.status', 'delivered')
        ]);
    }

    // Clear sold count cache
    public function clearSoldCountCache()
    {
        cache()->forget("product_sold_count_{$this->id}");
    }

    // Optimized method untuk product dengan review stats
    public function scopeWithReviewStats($query)
    {
        return $query->addSelect([
            'avg_rating' => DB::table('product_reviews')
                ->select(DB::raw('ROUND(AVG(rating), 1)'))
                ->whereColumn('idproduct', 'products.id'),
            'review_count' => DB::table('product_reviews')
                ->select(DB::raw('COUNT(*)'))
                ->whereColumn('idproduct', 'products.id')
        ]);
    }

    // Helper untuk mendapatkan product stats efficiently
    public function getStatsAttribute()
    {
        return cache()->remember("product_stats_{$this->id}", 3600, function () {
            $stats = DB::table('product_reviews')
                ->where('idproduct', $this->id)
                ->selectRaw('
                    COUNT(*) as review_count,
                    ROUND(AVG(rating), 1) as avg_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as rating_2,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as rating_1
                ')
                ->first();

            return [
                'sold_count' => $this->sold_count,
                'review_count' => $stats->review_count ?? 0,
                'avg_rating' => $stats->avg_rating ?? 0,
                'rating_distribution' => [
                    5 => $stats->rating_5 ?? 0,
                    4 => $stats->rating_4 ?? 0,
                    3 => $stats->rating_3 ?? 0,
                    2 => $stats->rating_2 ?? 0,
                    1 => $stats->rating_1 ?? 0,
                ]
            ];
        });
    }
}