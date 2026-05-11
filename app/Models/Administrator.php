<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Administrator extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'phone',
        'bio',
        'avatar'
    ];

    public function getRole()
    {
        return 'administrator';
    }
}
