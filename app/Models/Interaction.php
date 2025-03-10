<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Interaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['post_platform_id', 'number_of_likes', 'number_of_shares', 'number_of_comments'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid(); // Generate UUID
            }
        });
    }

    public function postPlatform()
    {
        return $this->belongsTo(PostPlatform::class);
    }
}
