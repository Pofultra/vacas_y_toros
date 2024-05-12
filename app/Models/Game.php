<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name',
        'user_age',
        'secret_code',
        'attempts',
        'remaining_time',
        'status',
        'token',
    ];

    protected $casts = [
        'attempts' => 'array',
    ];
}
