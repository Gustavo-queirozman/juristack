<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFile extends Model
{
     protected $fillable = [
        'customer_id', 'path', 'original_name', 'mime', 'size'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
