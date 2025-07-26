<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * Model untuk review produk
     *
     * @var array
     */
    protected $fillable = [
        'idproduct',
        'iduser',
        'productreviews',
        'rating',
    ];

    // Relasi ke Product (Many to One)
    public function product()
    {
        return $this->belongsTo(Product::class, 'idproduct');
    }

    // Relasi ke User (Many to One)
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser');
    }

    // Helper method untuk mendapatkan rating dalam bentuk bintang
    public function getStarsAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    // Scope untuk review berdasarkan rating
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    // Scope untuk review terbaru
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}