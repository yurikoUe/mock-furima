<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_address',
        'order_postal_code',
        'order_building',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
