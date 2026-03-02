<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Administrator extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = ['email', 'password'];

    public function getRole()
    {
        return 'administrator';
    }
}
