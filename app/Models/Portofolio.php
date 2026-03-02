<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portofolio extends Model
{
    use HasFactory;
    protected $fillable = ['service_id', 'title', 'description', 'media_url'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
