<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerPaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'iduserseller',
        'payment_category_id',
        'method_name',
        'account_name',
        'account_number',
        'instructions',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'iduserseller');
    }

    public function category()
    {
        return $this->belongsTo(PaymentCategory::class, 'payment_category_id');
    }
}