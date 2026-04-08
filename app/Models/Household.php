<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'household_name',
        'address',
        'lat',
        'lng',
        'qr_code',
        'priority_level',
        'status',
        'scene_image',
        'member_count',
        'rejection_reason',
        'phone',
    ];

    // ===== SCOPES =====

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ===== HELPERS =====

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'  => 'Chờ duyệt',
            'active'   => 'Đã duyệt',
            'rejected' => 'Từ chối',
            default    => 'Không xác định',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'  => '#f59e0b',
            'active'   => '#10b981',
            'rejected' => '#ef4444',
            default    => '#64748b',
        };
    }

    // ===== QUAN HỆ =====

    public function resident()
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}