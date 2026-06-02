<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Trip;
use App\Models\TripDetail;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $now      = now();
        $weekAgo  = $now->copy()->subDays(7);
        $twoWeeks = $now->copy()->subDays(14);

        // ── 4 CARD THỐNG KÊ ──────────────────────────────────────
        $activeHH      = Household::where('status', 'active')->count();
        $activeHHPrev  = Household::where('status', 'active')
            ->where('updated_at', '<', $weekAgo)->count();

        $totalTrips     = Trip::count();
        $totalTripsPrev = Trip::where('created_at', '<', $weekAgo)->count();

        $pendingCount = Household::where('status', 'pending')->count();

        $totalTon     = $this->calcTotalTon();
        $tonPrev      = $this->calcTotalTonBefore($weekAgo);

        $stats = [
            'households'        => $activeHH,
            'households_change' => $this->pct($activeHH, $activeHHPrev),
            'total_trips'       => $totalTrips,
            'trips_change'      => $this->pct($totalTrips, $totalTripsPrev),
            'total_ton'         => $totalTon,
            'ton_change'        => $this->pct($totalTon, $tonPrev),
            'pending'           => $pendingCount,
        ];

        // ── BIỂU ĐỒ 1: Chuyến xe 7 ngày ────────────────────────
        $tripsByDay = Trip::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $weekAgo->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $chartData[]   = (int) ($tripsByDay[$d] ?? 0);
        }

        // ── BIỂU ĐỒ 2: Trạng thái hộ dân ────────────────────────
        $hhStatus = Household::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusChart = [
            'active'   => (int) ($hhStatus['active']   ?? 0),
            'pending'  => (int) ($hhStatus['pending']  ?? 0),
            'rejected' => (int) ($hhStatus['rejected'] ?? 0),
        ];

        // ── BẢN ĐỒ: Xe đang giao (shipping) ─────────────────────
        $shippingTrips = Trip::with(['driver', 'deliveries' => function ($q) {
            $q->whereNotNull('actual_lat')->whereNotNull('actual_lng')
              ->orderByDesc('delivered_at');
        }])
        ->where('status', 'shipping')
        ->get()
        ->map(function ($trip) {
            // Tọa độ: lấy từ delivery có actual_lat gần nhất
            $lastDelivery = $trip->deliveries->first();
            if (!$lastDelivery) return null;
            return [
                'trip_id'   => $trip->id,
                'trip_code' => $trip->trip_code,
                'driver'    => $trip->driver?->name ?? 'N/A',
                'lat'       => (float) $lastDelivery->actual_lat,
                'lng'       => (float) $lastDelivery->actual_lng,
                'done'      => $trip->deliveries->whereIn('status', ['success','warning'])->count(),
                'total'     => $trip->deliveries->count(),
            ];
        })
        ->filter()
        ->values();

        // ── BẢNG: 5 Chuyến xe gần nhất ──────────────────────────
        $recentTrips = Trip::with(['driver', 'warehouse', 'deliveries.household', 'tripDetails'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($trip) {
                $firstHH = $trip->deliveries->first()?->household;
                $totalQty = $trip->tripDetails->sum('quantity_loaded');
                return [
                    'id'           => $trip->id,
                    'trip_code'    => $trip->trip_code,
                    'driver'       => $trip->driver?->name ?? '—',
                    'warehouse'    => $trip->warehouse?->name ?? '—',
                    'first_addr'   => $firstHH?->address ?? '—',
                    'total_qty'    => $totalQty,
                    'status'       => $trip->status,
                    'status_label' => $trip->status_label,
                    'status_color' => $trip->status_color,
                    'status_bg'    => $trip->status_bg,
                    'created_at'   => $trip->created_at->format('H:i d/m'),
                ];
            });

        return view('dashboard.admin', compact(
            'stats',
            'chartLabels',
            'chartData',
            'statusChart',
            'shippingTrips',
            'recentTrips',
        ));
    }

    // ── HELPERS ──────────────────────────────────────────────────

    private function calcTotalTon(): float
    {
        $kg = TripDetail::whereHas('trip', fn($q) => $q->where('status', 'completed'))
            ->sum('quantity_delivered');
        return round($kg / 1000, 2);
    }

    private function calcTotalTonBefore($date): float
    {
        $kg = TripDetail::whereHas('trip', fn($q) =>
            $q->where('status', 'completed')->where('completed_at', '<', $date)
        )->sum('quantity_delivered');
        return round($kg / 1000, 2);
    }

    private function pct($current, $previous): array
    {
        if ($previous == 0) {
            return ['value' => null, 'up' => true];
        }
        $diff = round((($current - $previous) / $previous) * 100, 1);
        return ['value' => abs($diff), 'up' => $diff >= 0];
    }
}
