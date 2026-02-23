<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    // Relacionamento muitos-para-muitos com User via tabela pivot task_user
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user')
            ->withTimestamps();
    }
}