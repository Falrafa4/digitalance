<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'file_url', 'note', 'version'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
