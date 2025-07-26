<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * Model untuk alamat pengguna
     *
     * @var array
     */
    protected $fillable = [
        'iduser',
        'address',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Relasi ke User (Many to One)
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser');
    }

    // Helper method untuk set alamat sebagai default
    public function setAsDefault()
    {
        // Set semua alamat user lain menjadi false
        self::where('iduser', $this->iduser)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        // Set alamat ini sebagai default
        $this->update(['is_default' => true]);
    }
}