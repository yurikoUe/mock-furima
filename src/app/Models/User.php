<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Fortify\TwoFactorAuthenticatable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'postal_code',
        'building',
        'profile_image',
    ];

    // リレーション
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites', 'user_id', 'product_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function orderAddresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    // ユーザーが送信したメッセージ
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    // 自分がつけた評価（購入者or出品者として）
    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    // 自分が受けた評価（購入者or出品者として）
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
