<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

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
            'bank_transfer' => 'Transfer Bank',
            'credit_card' => 'Kartu Kredit',
            'e_wallet' => 'E-Wallet',
            'cod' => 'Cash on Delivery'
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