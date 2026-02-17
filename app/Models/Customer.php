<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;
    protected $table = 'customers';

    public $fillable = [
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
    'tags'];


   public $timestamp =true;

   public function files()
   {
       return $this->hasMany(\App\Models\CustomerFile::class);
   }

protected $casts = [
    'tags' => 'array',
];

}
