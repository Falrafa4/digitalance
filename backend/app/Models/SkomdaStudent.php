<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkomdaStudent extends Model
{
    use HasFactory;
    protected $fillable = ['nis', 'name', 'email', 'class', 'major'];
    
    public function getRouteKeyName()
    {
        return 'nis';
    }

    public function freelancers()
    {
        return $this->hasMany(Freelancer::class);
    }
}
