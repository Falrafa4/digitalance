<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['service_id', 'client_id', 'brief', 'status', 'agreed_price'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
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
}
