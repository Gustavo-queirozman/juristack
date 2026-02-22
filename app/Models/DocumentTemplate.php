<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Tipos permitidos
    public const TYPES = [
        'power_of_attorney',
        'contract',
        'petition',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}

