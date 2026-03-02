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

    public function freelancer()
    {
        return $this->hasOne(Freelancer::class, 'student_id');
    }
}
