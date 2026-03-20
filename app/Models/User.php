<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'identity_card',
        'address',
        'latitude',
        'longitude',
        'qr_code',
        'telegram_chat_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'         => 'hashed',
        ];
    }

    // ====== Role Helpers ======
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWarehouseManager(): bool
    {
        return $this->role === 'warehouse_manager';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isResident(): bool
    {
        return $this->role === 'resident';
    }

    // Quan hệ
    public function managedWarehouses()
    {
        return $this->hasMany(Warehouse::class, 'manager_id');
    }

    public function tripsAsDriver()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    public function tripsAsCreator()
    {
        return $this->hasMany(Trip::class, 'created_by');
    }

    public function household()
    {
        return $this->hasOne(Household::class, 'resident_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class, 'created_by');
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class, 'created_by');
    }
}