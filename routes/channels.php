<?php

use App\Models\Trip;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Kênh deliveries.{tripId}
|--------------------------------------------------------------------------
| Tài xế của chuyến xe và Admin đều được xác thực vào kênh này.
*/
Broadcast::channel('deliveries.{tripId}', function ($user, $tripId) {
    // Admin
    if ($user->role === 'admin') {
        return true;
    }
    // Tài xế: chỉ được vào kênh của chuyến xe mình
    if ($user->role === 'driver') {
        return Trip::where('id', $tripId)
            ->where('driver_id', $user->id)
            ->whereIn('status', ['shipping', 'exporting', 'completed'])
            ->exists();
    }
    return false;
});

/*
|--------------------------------------------------------------------------
| Kênh trips.{tripId}
|--------------------------------------------------------------------------
| Admin và tài xế liên quan đều có thể lắng nghe trạng thái chuyến xe.
*/
Broadcast::channel('trips.{tripId}', function ($user, $tripId) {
    // Admin
    if ($user->role === 'admin') {
        return true;
    }
    // Tài xế
    if ($user->role === 'driver') {
        return Trip::where('id', $tripId)
            ->where('driver_id', $user->id)
            ->exists();
    }
    return false;
});
