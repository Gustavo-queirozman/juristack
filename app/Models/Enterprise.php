<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Enterprise extends Model
{
    use HasFactory;

    protected $table = 'enterprises';

    protected $fillable = [
        'name',
        'slug',
        'cnp',
        'email',
        'phone',
        'address',
        'evolution_instance',
        'whatsapp_connection_status',
        'whatsapp_qr_code',
        'whatsapp_connected_at',
        'whatsapp_disconnected_at',
    ];

    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'whatsapp_connected_at' => 'datetime',
            'whatsapp_disconnected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Enterprise $enterprise): void {
            if (! $enterprise->slug) {
                $enterprise->slug = static::generateUniqueSlug($enterprise->name);
            }
        });

        static::updating(function (Enterprise $enterprise): void {
            if (! $enterprise->slug) {
                $enterprise->slug = static::generateUniqueSlug($enterprise->name, $enterprise->id);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function financialEntries(): HasMany
    {
        return $this->hasMany(FinancialEntry::class);
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'escritorio';
        $slug = $baseSlug;
        $suffix = 2;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
