<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SaasPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_cents',
        'currency',
        'billing_interval',
        'interval_count',
        'trial_days',
        'button_label',
        'features',
        'is_active',
        'is_public',
        'is_featured',
        'contact_only',
        'sort_order',
        'stripe_product_id',
        'stripe_price_id',
        'stripe_price_signature',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'is_featured' => 'boolean',
            'contact_only' => 'boolean',
            'price_cents' => 'integer',
            'interval_count' => 'integer',
            'trial_days' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (SaasPlan $plan): void {
            if (! $plan->slug) {
                $plan->slug = Str::slug($plan->name);
            }

            $plan->currency = strtolower($plan->currency ?: 'brl');
        });
    }

    public function enterprises(): HasMany
    {
        return $this->hasMany(Enterprise::class, 'subscription_plan_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublicActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('is_public', true)
            ->orderBy('sort_order')
            ->orderBy('price_cents');
    }

    public function getDisplayPriceAttribute(): string
    {
        if ($this->contact_only || $this->price_cents === null) {
            return 'Sob consulta';
        }

        return 'R$ '.number_format($this->price_cents / 100, 2, ',', '.');
    }

    public function getDisplayPeriodAttribute(): string
    {
        if ($this->contact_only || $this->price_cents === null) {
            return '';
        }

        if ($this->billing_interval === 'year') {
            return $this->interval_count > 1
                ? '/'.$this->interval_count.' anos'
                : '/ano';
        }

        return $this->interval_count > 1
            ? '/'.$this->interval_count.' meses'
            : '/mes';
    }

    public function getCheckoutButtonLabelAttribute(): string
    {
        return $this->button_label ?: ($this->contact_only ? 'Falar com vendas' : 'Assinar plano');
    }

    public function isCheckoutEnabled(): bool
    {
        return ! $this->contact_only && filled($this->stripe_price_id);
    }

    public function pricingSignature(): string
    {
        return sha1(json_encode([
            'price_cents' => $this->price_cents,
            'currency' => $this->currency,
            'billing_interval' => $this->billing_interval,
            'interval_count' => $this->interval_count,
        ], JSON_THROW_ON_ERROR));
    }
}
