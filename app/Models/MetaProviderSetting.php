<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MetaProviderSetting extends Model
{
    protected $table = 'meta_provider_settings';

    protected $fillable = ['user_id', 'app_id', 'app_secret_encrypted', 'redirect_uri'];

    protected $hidden = ['app_secret_encrypted'];

    public $casts = [
        'user_id' => 'integer',
    ];

    public function hasAppSecret(): bool
    {
        return ! empty($this->app_secret_encrypted);
    }

    public function getAppSecret(): ?string
    {
        if (empty($this->app_secret_encrypted)) return null;
        try {
            return Crypt::decryptString($this->app_secret_encrypted);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function setAppSecret(string $secret)
    {
        $this->app_secret_encrypted = Crypt::encryptString($secret);
    }
}
