<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDetail extends Model
{
    use HasFactory;

    protected $table = 'trip_details';

    protected $fillable = [
        'trip_id',
        'supply_id',
        'quantity_loaded',
        'quantity_delivered'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}