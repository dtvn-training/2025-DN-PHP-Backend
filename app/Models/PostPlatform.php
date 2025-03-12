<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class PostPlatform extends Model
{
    use HasFactory, HasUuids;

    const POST_ID = 'post_id';
    const SOCIAL_ACCOUNT_ID = 'social_account_id';
    const PLATFORM = 'platform';
    const STATUS = 'platform';
    const POST_AT = 'posted_at';

    protected $fillable = [
        self::POST_ID,
        self::SOCIAL_ACCOUNT_ID,
        self::PLATFORM,
        self::STATUS,
        self::POST_AT,
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

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }
}
