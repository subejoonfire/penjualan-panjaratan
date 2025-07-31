<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * Model untuk transaksi
     *
     * @var array
     */
    protected $fillable = [
        'idorder',
        'transaction_number',
        'transactionstatus',
        'payment_method',
        'amount',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relasi ke Order (Many to One)
    public function order()
    {
        return $this->belongsTo(Order::class, 'idorder');
    }

    // Relasi ke DetailTransaction (One to Many)
    public function detailTransactions()
    {
        return $this->hasMany(DetailTransaction::class, 'idtransaction');
    }

    // Helper method untuk mendapatkan amount format rupiah
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Helper method untuk mendapatkan status dalam bahasa Indonesia
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'failed' => 'Pembayaran Gagal',
            'refunded' => 'Dikembalikan'
        ];

        return $statuses[$this->transactionstatus] ?? $this->transactionstatus;
    }

    // Helper method untuk mendapatkan payment method dalam bahasa Indonesia
    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            // Virtual Account methods
            'VA' => 'MAYBANK VA',
            'BT' => 'PERMATA VA',
            'B1' => 'CIMB NIAGA VA',
            'A1' => 'ATM BERSAMA VA',
            'I1' => 'BNI VA',
            'M2' => 'MANDIRI VA H2H',
            'AG' => 'ARTHA GRAHA VA',
            'BC' => 'BCA VA',
            'BR' => 'BRI VA',
            'NC' => 'BNC VA',
            'BV' => 'BSI VA',
            
            // E-Wallet methods
            'DA' => 'DANA',
            'OV' => 'OVO',
            'SP' => 'SHOPEEPAY QRIS',
            'SA' => 'SHOPEEPAY APP',
            'SL' => 'SHOPEEPAY LINK',
            'LA' => 'LINKAJA APP PCT',
            'LQ' => 'LINKAJA QRIS',
            'OL' => 'OVO LINK',
            'JP' => 'JENIUS PAY',
            'GQ' => 'GUDANG VOUCHER QRIS',
            'NQ' => 'NOBU QRIS',
            
            // Credit Card
            'VC' => 'CREDIT CARD',
            
            // Retail/COD
            'FT' => 'RETAIL',
            'IR' => 'INDOMARET',
            'DN' => 'INDODANA PAYLATER',
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    // Helper method untuk mengecek apakah sudah dibayar
    public function isPaid()
    {
        return $this->transactionstatus === 'paid';
    }

    // Helper method untuk mengecek apakah masih pending
    public function isPending()
    {
        return $this->transactionstatus === 'pending';
    }

    // Helper method untuk mark sebagai paid
    public function markAsPaid()
    {
        $this->update([
            'transactionstatus' => 'paid',
            'paid_at' => now()
        ]);

        // Increment sold count untuk setiap produk yang dibeli
        foreach ($this->order->cart->cartDetails as $cartDetail) {
            $cartDetail->product->incrementSoldCount($cartDetail->quantity);
        }
    }

    // Helper method untuk mark sebagai failed
    public function markAsFailed()
    {
        $this->update(['transactionstatus' => 'failed']);
    }

    // Scope berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('transactionstatus', $status);
    }

    // Scope untuk transaksi yang sudah dibayar
    public function scopePaid($query)
    {
        return $query->where('transactionstatus', 'paid');
    }

    // Scope untuk transaksi pending
    public function scopePending($query)
    {
        return $query->where('transactionstatus', 'pending');
    }
}