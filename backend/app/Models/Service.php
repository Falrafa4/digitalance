<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'freelancer_id', 'title', 'description', 'price_min', 'price_max', 'delivery_time', 'status'];

    public function service_category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }
}
