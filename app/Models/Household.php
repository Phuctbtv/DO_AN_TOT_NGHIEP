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
        'status'
    ];

    public function resident()
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}