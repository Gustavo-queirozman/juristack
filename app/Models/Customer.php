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
    'birth_date' => 'date',
    'rg_issue_date' => 'date',
    'cnh_issue_date' => 'date',
    'cnh_expiration_date' => 'date',
    'father_birth_date' => 'date',
    'mother_birth_date' => 'date',
];

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

}
