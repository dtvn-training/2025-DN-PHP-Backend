<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, HasUuids;

    const USER_ID = "user_id";
    const CONTENT = "content";
    const MEDIA_URLS = "media_urls";
    const SCHEDULED_TIME = "scheduled_time";

    protected $fillale = [
        self::USER_ID,
        self::CONTENT,
        self::MEDIA_URLS,
        self::SCHEDULED_TIME,
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

    protected $casts = [
        'media_urls' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
