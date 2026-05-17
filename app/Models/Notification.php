<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'role',
        'user_id',
        'link',
        'is_read',
        'is_kept',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_kept' => 'boolean',
    ];
}
