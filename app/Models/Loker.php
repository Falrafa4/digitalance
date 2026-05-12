<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loker extends Model
{
    protected $table = 'lokkers';

    protected $fillable = [
        'client_id',
        'category_id',
        'title',
        'description',
        'budget_min',
        'budget_max',
        'deadline',
        'status',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(LokerApplication::class);
    }

    public function approvedApplication()
    {
        return $this->applications()->where('status', 'Approved')->first();
    }
}
