<?php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    protected $table = 'enterprises';

    protected $fillable = [
        'name',
        'cnp',
    ];

    public $timestamps = true;
}

