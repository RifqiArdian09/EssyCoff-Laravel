<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'no_order',
        'customer_name',
        'total',
        'uang_dibayar',
        'kembalian',
        'status',
        'payment_method',
        'payment_ref',
        'card_last4',
    ];

    // 🔽 Tambahkan ini: pastikan field numerik jadi float
    protected $casts = [
        'total' => 'float',
        'uang_dibayar' => 'float',
        'kembalian' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(CafeTable::class, 'table_id');
    }
}