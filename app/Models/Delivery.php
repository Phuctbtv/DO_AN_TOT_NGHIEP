<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_code',
        'trip_id',
        'household_id',
        'proof_image_url',
        'actual_lat',
        'actual_lng',
        'distance_deviation',
        'recipient_name',
        'recipient_cccd',
        'status',
        'notes',
        'sync_status',
        'delivered_at'
    ];

    protected $casts = [
        'delivered_at' => 'datetime'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }
}