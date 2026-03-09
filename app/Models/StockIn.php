<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'supply_id',
        'quantity',
        'donor_info',
        'received_date',
        'created_by'
    ];

    protected $casts = [
        'received_date' => 'datetime'
    ];

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