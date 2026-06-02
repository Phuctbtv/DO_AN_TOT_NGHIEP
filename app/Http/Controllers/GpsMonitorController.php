<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Delivery;
use App\Models\Household;
use Illuminate\Http\Request;

class GpsMonitorController extends Controller
{
    /**
     * Trang giám sát GPS – hiển thị bản đồ realtime các chuyến đang giao
     * và lịch sử các giao hàng GPS lệch vượt ngưỡng.
     */
    public function index()
    {
        // ── Chuyến đang shipping ──────────────────────────────────
        $shippingTrips = Trip::where('status', 'shipping')
            ->with(['driver', 'warehouse', 'deliveries.household'])
            ->get()
            ->map(function ($trip) {
                // Lấy delivery cuối cùng có tọa độ thực tế
                $lastDelivery = $trip->deliveries
                    ->whereNotNull('actual_lat')
                    ->whereNotNull('actual_lng')
                    ->sortByDesc('delivered_at')
                    ->first();

                $done  = $trip->deliveries->where('status', 'success')->count();
                $total = $trip->deliveries->count();

                return [
                    'trip_id'   => $trip->id,
                    'trip_code' => $trip->trip_code,
                    'driver'    => $trip->driver?->name ?? 'N/A',
                    'warehouse' => $trip->warehouse?->name ?? 'N/A',
                    'done'      => $done,
                    'total'     => $total,
                    'progress'  => $total > 0 ? round($done / $total * 100) : 0,
                    'lat'       => $lastDelivery?->actual_lat,
                    'lng'       => $lastDelivery?->actual_lng,
                    'started_at'=> $trip->started_at?->format('H:i d/m'),
                    // Danh sách điểm giao (cho sidebar chi tiết)
                    'stops'     => $trip->deliveries->map(fn($d) => [
                        'code'      => $d->delivery_code,
                        'address'   => $d->household?->address ?? '—',
                        'status'    => $d->status,
                        'lat'       => $d->household?->lat,
                        'lng'       => $d->household?->lng,
                        'actual_lat'=> $d->actual_lat,
                        'actual_lng'=> $d->actual_lng,
                        'deviation' => $d->distance_deviation,
                        'delivered_at' => $d->delivered_at?->format('H:i'),
                    ])->values()->toArray(),
                ];
            })
            ->values();

        // ── Thống kê GPS lệch ─────────────────────────────────────
        $gpsTolerance = (int) config('app.gps_tolerance', 200);

        $warningDeliveries = Delivery::with(['trip', 'household'])
            ->whereNotNull('distance_deviation')
            ->where('distance_deviation', '>', $gpsTolerance)
            ->orderByDesc('delivered_at')
            ->take(50)
            ->get()
            ->map(fn($d) => [
                'delivery_code' => $d->delivery_code,
                'trip_code'     => $d->trip?->trip_code ?? '—',
                'address'       => $d->household?->address ?? '—',
                'deviation'     => $d->distance_deviation,
                'delivered_at'  => $d->delivered_at?->format('H:i d/m/Y'),
                'hh_lat'        => $d->household?->lat,
                'hh_lng'        => $d->household?->lng,
                'actual_lat'    => $d->actual_lat,
                'actual_lng'    => $d->actual_lng,
                'notes'         => $d->notes,
            ]);

        // ── Tổng hộ dân có tọa độ (để vẽ marker) ─────────────────
        $householdsWithCoords = Household::whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('status', 'active')
            ->select('id', 'household_name', 'address', 'lat', 'lng', 'priority_level')
            ->get();

        return view('gps.index', compact(
            'shippingTrips',
            'warningDeliveries',
            'householdsWithCoords',
            'gpsTolerance',
        ));
    }

    /**
     * API: Lấy vị trí mới nhất của tất cả chuyến đang shipping (polling/Ajax).
     */
    public function livePositions()
    {
        $trips = Trip::where('status', 'shipping')
            ->with(['driver', 'deliveries' => fn($q) => $q->whereNotNull('actual_lat')->orderByDesc('delivered_at')])
            ->get()
            ->map(function ($trip) {
                $last = $trip->deliveries->first();
                $done = $trip->deliveries->where('status', 'success')->count();
                return [
                    'trip_id'   => $trip->id,
                    'trip_code' => $trip->trip_code,
                    'driver'    => $trip->driver?->name,
                    'lat'       => $last?->actual_lat,
                    'lng'       => $last?->actual_lng,
                    'done'      => $done,
                    'total'     => $trip->deliveries->count(),
                ];
            })
            ->filter(fn($t) => $t['lat'] && $t['lng'])
            ->values();

        return response()->json($trips);
    }
}
