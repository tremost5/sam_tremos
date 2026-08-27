<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutopilotSetting extends Model
{
    protected $fillable = [
        'user_id',
        'enabled',
        'mode',
        'posts_per_day',
        'timezone',
        'start_time',
        'end_time',
        'language',
        'tone',
        'image_enabled',
        'auto_publish',
        'require_approval',
        'minimum_quality_score',
        'minimum_inventory',
        'target_inventory',
        'categories',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'image_enabled' => 'boolean',
        'auto_publish' => 'boolean',
        'require_approval' => 'boolean',
        'categories' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
