<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AiProviderSetting extends Model
{
    protected $table = 'ai_provider_settings';

    protected $fillable = [
        'user_id',
        'provider',
        'text_model',
        'image_model',
        'api_key_encrypted',
    ];

    protected $hidden = [
        'api_key_encrypted',
    ];

    public function hasApiKey(): bool
    {
        return ! empty($this->api_key_encrypted);
    }

    public function setApiKey(string $plain)
    {
        $this->api_key_encrypted = Crypt::encryptString($plain);
    }

    public function getApiKey(): ?string
    {
        if (empty($this->api_key_encrypted)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->api_key_encrypted);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
