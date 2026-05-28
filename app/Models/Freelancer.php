<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string|null $bio
 * @property string|null $status
 * @property int|null $student_id
 * @property string|null $password
 * @property string|null $email
 * @property string|null $name
 */
class Freelancer extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['student_id', 'bio', 'profile_photo', 'password', 'status', 'reject_reason'];

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
