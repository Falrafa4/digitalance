<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAttachment extends Model
{
    protected $fillable = [
        'order_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isImage()
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']);
    }

    public function getUrl()
    {
        return asset('storage/'.$this->file_path);
    }
}
