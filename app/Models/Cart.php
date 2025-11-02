<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getTotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeForUserOrSession($query, $userId = null, $sessionId = null)
    {
        return $query->when($userId, function ($q) use ($userId) {
            return $q->where('user_id', $userId);
        })->when(!$userId && $sessionId, function ($q) use ($sessionId) {
            return $q->where('session_id', $sessionId);
        });
    }
}
