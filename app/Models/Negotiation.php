<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'sender', 'message'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getTimeAttribute(): string
    {
        return date(
            "d M Y, H:i:s",
            strtotime($this->attributes['created_at'])
        );
    }
}
