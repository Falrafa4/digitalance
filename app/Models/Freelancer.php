<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Freelancer extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $fillable = ['student_id', 'bio', 'email', 'password', 'status'];

    public function skomda_student()
    {
        return $this->belongsTo(SkomdaStudent::class, 'student_id');
    }

    public function portofolios()
    {
        return $this->hasMany(Portofolio::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
    
    public function getRole()
    {
        return 'freelancer';
    }
}
