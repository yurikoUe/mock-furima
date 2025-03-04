<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'payment_method',
        'order_address_id',
        'status',
    ];

    // 支払い方法の選択肢
    const PAYMENT_METHODS = [
        'card' => 'クレジットカード',
        'convenience_store' => 'コンビニ決済',
    ];
    
    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function orderAddress()
    {
        return $this->belongsTo(OrderAddress::class);
    }

}
