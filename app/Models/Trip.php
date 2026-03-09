<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_code',
        'driver_id',
        'warehouse_id',
        'vehicle_info',
        'status',
        'weather_temp',
        'weather_desc',
        'weather_alert',
        'notes',
        'exported_at',
        'started_at',
        'completed_at',
        'created_by'
    ];

    protected $casts = [
        'weather_alert' => 'boolean',
        'exported_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tripDetails()
    {
        return $this->hasMany(TripDetail::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}