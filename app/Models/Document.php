<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'document_link',
        'form_link',
        'document_template_id',
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

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }
}

