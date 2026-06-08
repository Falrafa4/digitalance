<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['name', 'email', 'password', 'phone', 'profile_photo'];

    protected $hidden = ['password'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function lokers()
    {
        return $this->hasMany(Loker::class);
    }

    public function getRole()
    {
        return 'client';
    }
}
