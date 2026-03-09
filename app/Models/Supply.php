<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'unit',
        'min_stock_alert'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    public function tripDetails()
    {
        return $this->hasMany(TripDetail::class);
    }
}