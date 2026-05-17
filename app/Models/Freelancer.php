<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Freelancer extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['student_id', 'bio', 'password', 'status', 'reject_reason'];

    protected $hidden = ['password'];

    public function skomda_student()
    {
        return $this->belongsTo(SkomdaStudent::class, 'student_id');
    }

    public function getNameAttribute()
    {
        return optional($this->skomda_student)->name;
    }

    public function portofolios()
    {
        return $this->hasManyThrough(Portofolio::class, Service::class);
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
