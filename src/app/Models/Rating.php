<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'order_id',
        'rater_id',
        'ratee_id',
        'rating',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 評価者（ユーザー）
    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    // 評価される側（ユーザー）
    public function ratee()
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }
}
