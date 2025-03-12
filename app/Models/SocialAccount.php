<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class SocialAccount extends Model
{
    use HasFactory, HasUuids;

    const USER_ID = "user_id";
    const SOCIAL_USER_ID = "social_user_id";
    const SCREEN_NAME = "screen_name";
    const PLATFORM = "platform";
    const ACCESS_TOKEN = "access_token";
    const REFRESH_TOKEN = "refresh_token";
    const EXPIRED_AT = "expires_at";

    protected $fillable = [
        self::USER_ID,
        self::SOCIAL_USER_ID,
        self::SCREEN_NAME,
        self::PLATFORM,
        self::ACCESS_TOKEN,
        self::REFRESH_TOKEN,
        self::EXPIRED_AT
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
