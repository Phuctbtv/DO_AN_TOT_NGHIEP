<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'warehouse_id',
        'supply_id',
        'quantity',
        'exported_date',
        'created_by'
    ];

    protected $casts = [
        'exported_date' => 'datetime'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}