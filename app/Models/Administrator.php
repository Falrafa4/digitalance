<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Administrator extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = ['name',
        'email',
        'password',
        'status'];

    public function getRole()
    {
        return 'administrator';
    }
}
