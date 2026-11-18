<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'dining_table_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'guest_count',
        'reservation_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reservation_at' => 'datetime',
        ];
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class, 'dining_table_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}