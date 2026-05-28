<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int|null $service_id
 * @property int|null $client_id
 * @property int|null $freelancer_id
 * @property int|null $loker_application_id
 * @property string|null $brief
 * @property string|null $status
 * @property float|int|null $agreed_price
 * @property string|null $deadline
 */
class Order extends Model
{
    use HasFactory;

    // ERD: orders punya freelancer_id
    protected $fillable = [
        'service_id',
        'client_id',
        'freelancer_id',
        'loker_application_id',
        'brief',
        'status',
        'agreed_price',
        'deadline',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // ERD: orders.freelancer_id -> freelancers.id
    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function lokerApplication()
    {
        return $this->belongsTo(LokerApplication::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function negotiations()
    {
        return $this->hasMany(Negotiation::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function attachments()
    {
        return $this->hasMany(OrderAttachment::class);
    }
}
