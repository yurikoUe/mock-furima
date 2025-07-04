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
        'finished'
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
    // この注文に紐づくチャットメッセージ
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function rating()
    {
        return $this->hasMany(Rating::class);
    }

    // 決済完了で自分が購入者か出品者の取引
    public function scopeActiveForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId) // 購入者が自分
                ->orWhereHas('product', function ($q2) use ($userId) { // 出品者が自分
                    $q2->where('user_id', $userId);
                });
            })
            ->where('status', '決済完了'); //決済完了
    }

    // まだ評価していなければtrueを返す
    public function isNotRatedByUser($userId)
    {
        return !$this->rating()->where('rater_id', $userId)->exists();
    }

    // 未読メッセージ数のカウント
    public function unreadMessages()
    {
        return $this->hasMany(ChatMessage::class)
                    ->where('sender_id', '!=', auth()->id()) // or use passed in $userId
                    ->where('is_read', false);
    }

}
