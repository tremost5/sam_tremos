<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookPage extends Model
{
    protected $fillable = [
        'user_id',
        'facebook_id',
        'name',
        'category',
        'access_token',
        'permissions',
        'status',
        'selected',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
