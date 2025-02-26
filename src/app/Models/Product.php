<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'condition',
        'brand_id',
        'image',
    ];

    // リレーション
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function order()
    {
        return $this->hasOne(Order::class);
    }
    public function condition()
        {
            return $this->belongsTo(Condition::class);
        }
    public function isFavoritedBy($user)
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
