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
        'customer_id',
    ];

    // Tipos permitidos (mesmos do template)
    public const TYPES = [
        'power_of_attorney' => 'Procuração',
        'contract' => 'Contrato',
        'petition' => 'Petição',
        'contestation' => 'Contestação',
        'declaration' => 'Declaração',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

