<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect user to the correct dashboard based on their role.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin'             => redirect()->route('admin.dashboard'),
            'warehouse_manager' => redirect()->route('warehouse.dashboard'),
            'driver'            => redirect()->route('driver.dashboard'),
            default             => redirect()->route('resident.dashboard'),
        };
    }
}
