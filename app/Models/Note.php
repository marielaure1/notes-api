<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
