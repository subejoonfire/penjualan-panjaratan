<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Model untuk keranjang belanja
     *
     * @var array
     */
    protected $fillable = [
        'iduser',
        'checkoutstatus',
    ];

    // Relasi ke User (Many to One)
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser');
    }

    // Relasi ke CartDetail (One to Many)
    public function cartDetails()
    {
        return $this->hasMany(CartDetail::class, 'idcart');
    }

    // Relasi ke Order (One to One)
    public function order()
    {
        return $this->hasOne(Order::class, 'idcart');
    }

    // Helper method untuk mendapatkan total harga cart
    public function getTotalPriceAttribute()
    {
        return $this->cartDetails->sum(function ($detail) {
            return $detail->quantity * $detail->productprice;
        });
    }

    // Helper method untuk mendapatkan total item
    public function getTotalItemsAttribute()
    {
        return $this->cartDetails->sum('quantity');
    }

    // Helper method untuk mendapatkan jumlah produk unik
    public function getUniqueProductsCountAttribute()
    {
        return $this->cartDetails->count();
    }

    // Helper method untuk menambah produk ke cart
    public function addProduct($productId, $quantity = 1)
    {
        $product = Product::find($productId);

        if (!$product || !$product->isInStock()) {
            return false;
        }

        $existingDetail = $this->cartDetails()->where('idproduct', $productId)->first();

        if ($existingDetail) {
            $existingDetail->increment('quantity', $quantity);
        } else {
            $this->cartDetails()->create([
                'idproduct' => $productId,
                'quantity' => $quantity,
                'productprice' => $product->productprice,
            ]);
        }

        return true;
    }

    // Helper method untuk update quantity produk
    public function updateProductQuantity($productId, $quantity)
    {
        $detail = $this->cartDetails()->where('idproduct', $productId)->first();

        if ($detail) {
            if ($quantity <= 0) {
                $detail->delete();
            } else {
                $detail->update(['quantity' => $quantity]);
            }
            return true;
        }

        return false;
    }

    // Helper method untuk menghapus produk dari cart
    public function removeProduct($productId)
    {
        return $this->cartDetails()->where('idproduct', $productId)->delete();
    }

    // Helper method untuk mengosongkan cart
    public function clear()
    {
        return $this->cartDetails()->delete();
    }

    // Scope untuk cart aktif
    public function scopeActive($query)
    {
        return $query->where('checkoutstatus', 'active');
    }

    // Scope untuk cart yang sudah checkout
    public function scopeCheckedOut($query)
    {
        return $query->where('checkoutstatus', 'checkout');
    }
}
