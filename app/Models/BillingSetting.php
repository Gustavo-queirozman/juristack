<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingSetting extends Model
{
    protected $fillable = [
        'stripe_publishable_key',
        'stripe_secret_key',
        'stripe_webhook_secret',
        'default_currency',
        'is_stripe_enabled',
    ];

    protected function casts(): array
    {
        return [
            'stripe_secret_key' => 'encrypted',
            'stripe_webhook_secret' => 'encrypted',
            'is_stripe_enabled' => 'boolean',
        ];
    }

    public function isConfigured(): bool
    {
        return $this->is_stripe_enabled
            && filled($this->stripe_publishable_key)
            && filled($this->stripe_secret_key);
    }
}
