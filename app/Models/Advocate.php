<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advocate extends Model
{
    protected $table = 'advocates';


   public function user()
{
    return $this->belongsTo(User::class);
}
}
