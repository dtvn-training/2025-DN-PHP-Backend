<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class SocialAccount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'social_user_id', 'screen_name', 'platform', 'access_token', 'refresh_token', 'expires_at'];

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
