<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'freelancer_id',
        'title',
        'description',
        'price_min',
        'price_max',
        'delivery_time',
        'status'
    ];

    // ERD: services.category_id -> service_categories.id
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    // Backward-compat (boleh dihapus nanti kalau semua kode sudah pindah)
    public function service_category()
    {
        return $this->category();
    }

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }
}