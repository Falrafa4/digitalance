<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $fillable = ['name', 'email', 'password', 'phone'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function getRole()
    {
        return 'client';
    }
}
