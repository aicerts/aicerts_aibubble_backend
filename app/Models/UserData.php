<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    protected $table = 'user_datas';

    protected $fillable = [
        'user_id',
        'data'
    ];

    protected $hidden = [
        'created_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
