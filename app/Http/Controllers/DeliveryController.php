<?php

namespace App\Http\Controllers;

use App\Events\DeliveryUpdated;
use App\Events\TripStatusUpdated;
use App\Models\Delivery;
use App\Models\Household;
use App\Models\Trip;
use App\Services\CloudinaryService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DeliveryController extends Controller
{
    public function __construct(
        private TelegramService   $telegram,
        private CloudinaryService $cloudinary
    ) {}

    // ============================================================
    //  DRIVER DASHBOARD
    // ============================================================

    public function driverDashboard()
    {
        $driver = auth()->user();

        // Lấy chuyến xe ưu tiên: mới nhất trước, nếu cùng ngày thì ưu tiên shipping > exporting > preparing
        $activeTrip = Trip::with([
            'warehouse',
            'tripDetails.supply.category',
            'deliveries.household',
        ])
        ->where('driver_id', $driver->id)
        ->whereIn('status', ['preparing', 'exporting', 'shipping'])
        ->latest('created_at')   // chuyến được tạo MỚI NHẤT lên đầu
        ->first();

        // Thống kê
        $stats = ['pending' => 0, 'done' => 0, 'total' => 0];
        if ($activeTrip) {
            $stats['total']   = $activeTrip->deliveries->count();
            $stats['done']    = $activeTrip->deliveries->whereIn('status', ['success', 'warning'])->count();
            $stats['pending'] = $stats['total'] - $stats['done'];
        }

        $gpsTolerance = config('services.gps.tolerance_meters', 100);

        // JSON cho bản đồ tuyến (tính trước, không dùng @json với arrow fn)
        $routeMapJson = '[]';
        if ($activeTrip) {
            $mapData = $activeTrip->deliveries
                ->filter(fn($d) => $d->household?->lat && $d->household?->lng)
                ->map(fn($d) => [
                    'id'      => $d->id,
                    'name'    => $d->household?->household_name ?? $d->recipient_name,
                    'lat'     => (float) $d->household->lat,
                    'lng'     => (float) $d->household->lng,
                    'status'  => $d->status,
                    'address' => $d->household?->address ?? '',
                    'phone'   => $d->household?->phone ?? '',
                ])
                ->values();
            $routeMapJson = json_encode($mapData, JSON_UNESCAPED_UNICODE);
        }

        return view('dashboard.driver', compact(
            'driver', 'activeTrip', 'stats', 'gpsTolerance', 'routeMapJson'
        ));
    }

    // ============================================================
    //  API: Lấy stats của trip (realtime)
    // ============================================================

    public function tripStats(Trip $trip)
    {
        // Kiểm tra tài xế chỉ xem trip của mình
        if ($trip->driver_id !== auth()->id()) {
            return response()->json(['error' => 'Không có quyền.'], 403);
        }

        $deliveries   = $trip->deliveries()->get();
        $totalCount   = $deliveries->count();
        $successCount = $deliveries->whereIn('status', ['success', 'warning'])->count();
        $pendingCount = $deliveries->where('status', 'pending')->count();

        return response()->json([
            'pending_count' => $pendingCount,
            'success_count' => $successCount,
            'total_count'   => $totalCount,
        ]);
    }

    // ============================================================
    //  API: Lấy danh sách deliveries của trip (realtime)
    // ============================================================

    public function tripDeliveries(Trip $trip)
    {
        // Kiểm tra tài xế chỉ xem trip của mình
        if ($trip->driver_id !== auth()->id()) {
            return response()->json(['error' => 'Không có quyền.'], 403);
        }

        $deliveries = $trip->deliveries()->with('household')->get()->map(fn($d) => [
            'id'             => $d->id,
            'delivery_code'  => $d->delivery_code,
            'recipient_name' => $d->recipient_name,
            'recipient_cccd' => $d->recipient_cccd,
            'status'         => $d->status,
            'delivered_at'   => $d->delivered_at?->format('H:i d/m/Y'),
            'address'        => $d->household?->address ?? '—',
            'phone'          => $d->household?->phone ?? '',
            'priority_level' => $d->household?->priority_level ?? 3,
            'proof_image_url'=> $d->proof_image_url,
            'distance_deviation' => $d->distance_deviation,
        ]);

        return response()->json($deliveries);
    }

    // ============================================================
    //  DRIVER: Bắt đầu giao hàng (exporting → shipping)
    //          hoặc Kết thúc thủ công chuyến bị stuck (_force_complete)
    // ============================================================

    public function startTrip(Request $request, Trip $trip)
    {
        // Chỉ tài xế của chuyến mới được thao tác
        if ($trip->driver_id !== auth()->id()) {
            return back()->with('error', 'Không có quyền thực hiện.');
        }

        // ── Force complete: đóng chuyến stuck shipping còn pending ──
        if ($request->input('_force_complete') === '1') {
            if ($trip->status !== 'shipping') {
                return back()->with('error', 'Chuyến xe không ở trạng thái đang giao.');
            }
            $trip->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);
            return redirect()->route('driver.dashboard')
                ->with('success', "✅ Đã kết thúc chuyến xe {$trip->trip_code}.");
        }

        // ── Bắt đầu giao: exporting → shipping ──
        if ($trip->status !== 'exporting') {
            return back()->with('error', 'Chuyến xe không ở trạng thái xuất kho.');
        }

        $trip->update([
            'status'     => 'shipping',
            'started_at' => now(),
        ]);

        return redirect()->route('driver.dashboard')
            ->with('success', "🚀 Đã bắt đầu giao hàng! Mã chuyến: {$trip->trip_code}");
    }

    // ============================================================
    //  QR LOOKUP – Tra cứu delivery bằng QR code hộ dân
    // ============================================================

    public function qrLookup(Request $request)
    {
        $request->validate(['qr_code' => ['required', 'string', 'max:200']]);

        return $this->findDeliveryByHousehold(
            Household::where('qr_code', $request->qr_code)->first()
        );
    }

    // ============================================================
    //  CCCD LOOKUP – Tra cứu delivery bằng số CCCD
    // ============================================================

    public function cccdLookup(Request $request)
    {
        $request->validate(['cccd' => ['required', 'string', 'max:20']]);

        // Tìm hộ dân qua delivery.recipient_cccd (không cần qua household)
        $delivery = Delivery::with(['household', 'trip'])
            ->whereHas('trip', fn($q) => $q
                ->where('driver_id', auth()->id())
                ->whereIn('status', ['shipping', 'exporting'])
            )
            ->where('recipient_cccd', $request->cccd)
            ->where('status', 'pending')
            ->first();

        if (!$delivery) {
            // Thử tìm qua bảng households (user nhập CCCD của cư dân)
            $household = Household::whereHas('resident', fn($q) => $q->where('identity_card', $request->cccd))
                ->first();

            return $this->findDeliveryByHousehold($household);
        }

        return $this->buildDeliveryResponse($delivery);
    }

    // ============================================================
    //  CONFIRM – Xác nhận giao hàng (GPS + Cloudinary + WebSocket)
    // ============================================================

    public function confirm(Request $request, Delivery $delivery)
    {
        // Kiểm tra quyền
        $trip = $delivery->trip()->with(['tripDetails', 'deliveries', 'driver'])->first();
        if ($trip->driver_id !== auth()->id()) {
            return response()->json(['error' => 'Không có quyền thực hiện thao tác này.'], 403);
        }

        if (in_array($delivery->status, ['success', 'warning'])) {
            return response()->json(['error' => 'Điểm giao này đã được xác nhận trước đó.'], 422);
        }

        // ── SỬA LỖI 1: KIỂM TRA QR CODE ĐÚNG HỘ DÂN ──────────────
        if ($request->has('qr_household_id') && $request->qr_household_id) {
            $qrHouseholdId = (int) $request->qr_household_id;
            if ($qrHouseholdId !== (int) $delivery->household_id) {
                return response()->json([
                    'error' => '❌ Mã QR không đúng với hộ dân đã chọn. Vui lòng kiểm tra lại.',
                ], 422);
            }
        }

        $request->validate([
            'actual_lat'         => ['nullable', 'numeric', 'between:-90,90'],
            'actual_lng'         => ['nullable', 'numeric', 'between:-180,180'],
            'distance_deviation' => ['nullable', 'numeric', 'min:0'],
            'force_confirm'      => ['boolean'],
            'force_reason'       => ['nullable', 'string', 'max:500'],
            'proof_image'        => ['required', 'image', 'max:8192'],
        ]);

        $tolerance    = config('services.gps.tolerance_meters', 100);
        $distance     = (float) ($request->distance_deviation ?? 0);
        $hasGps       = $request->filled('actual_lat') && $request->filled('actual_lng');
        $isOutOfRange = $hasGps && $distance > $tolerance;
        $forceConfirm = (bool) $request->force_confirm;

        // Nếu GPS lệch quá ngưỡng và không force → từ chối
        if ($isOutOfRange && !$forceConfirm) {
            return response()->json([
                'error'     => "GPS lệch " . round($distance) . " mét, vượt quá ngưỡng {$tolerance} mét.",
                'distance'  => $distance,
                'tolerance' => $tolerance,
                'need_force'=> true,
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Upload ảnh bằng chứng lên Cloudinary
            $proofUrl = $this->cloudinary->upload(
                $request->file('proof_image'),
                'daiphuc/deliveries'
            );

            $status = ($isOutOfRange && $forceConfirm) ? 'warning' : 'success';

            // Cập nhật delivery
            $delivery->update([
                'actual_lat'         => $request->actual_lat,
                'actual_lng'         => $request->actual_lng,
                'distance_deviation' => $hasGps ? $distance : null,
                'proof_image_url'    => $proofUrl,
                'status'             => $status,
                'notes'              => $forceConfirm ? $request->force_reason : null,
                'delivered_at'       => now(),
                'sync_status'        => 'synced',
            ]);

            // Cập nhật quantity_delivered trong trip_details
            $totalHH   = $trip->deliveries->count();
            $doneCount = $trip->deliveries
                ->where('id', '!=', $delivery->id)
                ->whereIn('status', ['success', 'warning'])
                ->count() + 1;

            foreach ($trip->tripDetails as $detail) {
                $perHH        = $totalHH > 0 ? (int) floor($detail->quantity_loaded / $totalHH) : 0;
                $newDelivered = min($doneCount * $perHH, $detail->quantity_loaded);
                $detail->update(['quantity_delivered' => $newDelivered]);
            }

            // Kiểm tra nếu tất cả deliveries đã giao → hoàn thành chuyến xe
            $freshDeliveries = $trip->deliveries()->get();
            $allDone = $freshDeliveries->every(fn($d) =>
                $d->id === $delivery->id
                    ? true
                    : in_array($d->status, ['success', 'warning'])
            );

            if ($allDone && $trip->status !== 'completed') {
                $trip->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);
                // Gửi Telegram thông báo hoàn thành
                $this->telegram->notifyTripStatusChanged(
                    $trip->trip_code,
                    $trip->driver->name,
                    'completed'
                );
            }

            DB::commit();

            // ── SỬA LỖI 2+3: BROADCAST REALTIME ──────────────────────
            // Tính lại stats mới nhất
            $freshStats = $trip->deliveries()->get();
            $stats = [
                'pending_count' => $freshStats->where('status', 'pending')->count(),
                'success_count' => $freshStats->whereIn('status', ['success', 'warning'])->count(),
                'total_count'   => $freshStats->count(),
            ];

            // Broadcast cập nhật delivery stats (kèm delivery_id để show page cập nhật đúng hàng)
            $delivery->refresh(); // Load delivered_at mới nhất
            broadcast(new DeliveryUpdated($trip, $stats, $delivery))->toOthers();

            // Nếu trip hoàn thành → broadcast trip status
            if ($allDone) {
                $trip->refresh();
                broadcast(new TripStatusUpdated($trip))->toOthers();
            }

            // Gửi Telegram cảnh báo GPS nếu lệch
            if ($status === 'warning') {
                $hh = $delivery->household;
                $this->telegram->notifyGpsWarning(
                    driverName:    auth()->user()->name,
                    deliveryCode:  $delivery->delivery_code,
                    householdName: $delivery->recipient_name,
                    address:       $hh?->address ?? '—',
                    distance:      $distance,
                    tolerance:     $tolerance,
                    reason:        $request->force_reason ?? ''
                );
            }

            return response()->json([
                'success'       => true,
                'status'        => $status,
                'trip_done'     => $allDone,
                'message'       => $status === 'success'
                    ? '✅ Xác nhận giao hàng thành công!'
                    : '⚠️ Đã giao với cảnh báo GPS. Admin được thông báo.',
                'delivered_at'  => $delivery->fresh()->delivered_at?->format('H:i d/m/Y'),
                'proof_url'     => $proofUrl,
                'stats'         => $stats,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[DeliveryController@confirm] ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    //  HELPERS
    // ============================================================

    private function findDeliveryByHousehold(?Household $household)
    {
        if (!$household) {
            return response()->json(['error' => 'Không tìm thấy hộ dân với thông tin này.'], 404);
        }

        $delivery = Delivery::with(['household', 'trip'])
            ->whereHas('trip', fn($q) => $q
                ->where('driver_id', auth()->id())
                ->whereIn('status', ['shipping', 'exporting'])
            )
            ->where('household_id', $household->id)
            ->where('status', 'pending')
            ->first();

        if (!$delivery) {
            return response()->json([
                'error' => 'Không tìm thấy đơn giao hàng chờ xử lý cho hộ dân này trong chuyến xe hiện tại.',
            ], 404);
        }

        return $this->buildDeliveryResponse($delivery);
    }

    private function buildDeliveryResponse(Delivery $delivery)
    {
        $hh = $delivery->household;

        return response()->json([
            'found'          => true,
            'delivery_id'    => $delivery->id,
            'delivery_code'  => $delivery->delivery_code,
            'recipient_name' => $delivery->recipient_name,
            'recipient_cccd' => $delivery->recipient_cccd,
            'address'        => $hh?->address ?? '—',
            'phone'          => $hh?->phone ?? '',
            'household_lat'  => $hh?->lat  ? (float) $hh->lat  : null,
            'household_lng'  => $hh?->lng  ? (float) $hh->lng  : null,
            'priority_level' => $hh?->priority_level ?? 3,
            'household_id'   => $hh?->id,
        ]);
    }
}
