<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notification;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    public $fillable = [
        'user_id',
        'enterprise_id',
        'name',
        'cnp',
        'rg',
        'rg_issue_date',
        'cnh',
        'cnh_issue_date',
        'cnh_expiration_date',
        'my_inss_password',
        'birth_date',
        'gender',
        'mobile_phone',
        'phone',
        'phone_2',
        'email',
        'zip_code',
        'state',
        'city',
        'neighborhood',
        'street',
        'number',
        'profession',
        'marital_status',
        'father_name',
        'father_birth_date',
        'mother_name',
        'mother_birth_date',
        'tags',
    ];

    public $timestamps = true;

    protected $casts = [
        'tags' => 'array',
        'birth_date' => 'date',
        'rg_issue_date' => 'date',
        'cnh_issue_date' => 'date',
        'cnh_expiration_date' => 'date',
        'father_birth_date' => 'date',
        'mother_birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CustomerFile::class);
    }

    public function processos(): HasMany
    {
        return $this->hasMany(DatajudProcesso::class);
    }

    public function documentRequests(): HasMany
    {
        return $this->hasMany(CustomerDocumentRequest::class)->latest();
    }

    public static function formatarCpf(string $cpf): string
    {
        $digits = preg_replace('/\D/', '', $cpf);

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
    }

    public static function formatarCnpj(string $cnpj): string
    {
        $digits = preg_replace('/\D/', '', $cnpj);

        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $digits);
    }

    public function routeNotificationForWhatsApp(?Notification $notification = null): ?string
    {
        return $this->mobile_phone ?: $this->phone;
    }
}
