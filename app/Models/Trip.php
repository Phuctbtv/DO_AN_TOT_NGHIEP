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
        'exported_at'   => 'datetime',
        'started_at'    => 'datetime',
        'completed_at'  => 'datetime',
    ];

    // ===== STATUS HELPERS =====

    public function isPreparing(): bool  { return $this->status === 'preparing'; }
    public function isExporting(): bool  { return $this->status === 'exporting'; }
    public function isShipping(): bool   { return $this->status === 'shipping'; }
    public function isCompleted(): bool  { return $this->status === 'completed'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'preparing'  => 'Chẩn bị',
            'exporting'  => 'Xuất kho',
            'shipping'   => 'Đang giao',
            'completed'  => 'Hoàn thành',
            'cancelled'  => 'Đã huỷ',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'preparing'  => '#f59e0b',
            'exporting'  => '#8b5cf6',
            'shipping'   => '#3b82f6',
            'completed'  => '#10b981',
            'cancelled'  => '#ef4444',
            default      => '#64748b',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match($this->status) {
            'preparing'  => '#fef3c7',
            'exporting'  => '#ede9fe',
            'shipping'   => '#dbeafe',
            'completed'  => '#d1fae5',
            'cancelled'  => '#fee2e2',
            default      => '#f1f5f9',
        };
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->tripDetails->sum('quantity_loaded');
    }

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