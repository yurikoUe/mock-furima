<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'user_id','order_address', 'order_postal_code', 'order_building',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // User とのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
