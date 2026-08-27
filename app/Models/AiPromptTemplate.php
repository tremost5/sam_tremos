<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPromptTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'prompt',
        'version',
        'is_active',
    ];
}
