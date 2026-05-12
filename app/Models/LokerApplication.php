<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LokerApplication extends Model
{
    protected $fillable = [
        'loker_id',
        'freelancer_id',
        'proposal',
        'proposed_price',
        'status',
    ];

    protected $casts = [
        'proposed_price' => 'decimal:2',
    ];

    public function loker(): BelongsTo
    {
        return $this->belongsTo(Loker::class);
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }
}
