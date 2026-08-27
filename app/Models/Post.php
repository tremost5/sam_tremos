<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'facebook_page_id',
        'category_id',
        'title',
        'idea',
        'caption',
        'hashtags',
        'engagement_question',
        'image_path',
        'image_prompt',
        'quality_score',
        'ai_generated',
        'status',
        'scheduled_at',
        'published_at',
        'facebook_post_id',
        'error_message',
    ];

    protected $casts = [
        'ai_generated' => 'boolean',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
