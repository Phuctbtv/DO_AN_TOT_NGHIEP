<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Feedback;
use App\Models\Household;
use App\Models\TripDetail;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WelcomeController extends Controller
{
    public function __construct(private CloudinaryService $cloudinary) {}

    // ============================================================
    //  TRANG CHỦ – Truyền thống kê thật lên view
    // ============================================================

    public function index()
    {
        // 4 thống kê chính
        $stats = [
            'households'   => Household::where('status', 'active')->count(),
            'active_trips' => \App\Models\Trip::where('status', 'shipping')->count(),
            'drivers'      => User::whereIn('role', ['admin', 'driver', 'warehouse_manager'])->count(),
            'total_kg'     => $this->calcTotalKg(),
        ];

        // Markers bản đồ: hộ dân active có tọa độ
        $mapHouseholds = Household::where('status', 'active')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select('id', 'household_name', 'address', 'phone', 'lat', 'lng', 'priority_level')
            ->get()
            ->map(fn($h) => [
                'id'       => $h->id,
                'name'     => $h->household_name,
                'address'  => $h->address,
                'phone'    => $h->phone ?? '',
                'lat'      => (float) $h->lat,
                'lng'      => (float) $h->lng,
                'priority' => (int) $h->priority_level,
            ]);

        return view('welcome', compact('stats', 'mapHouseholds'));
    }

    // ============================================================
    //  API – Bảng tin minh bạch (polling 30s)
    // ============================================================

    public function activityFeed()
    {
        $deliveries = Delivery::with(['trip', 'household', 'trip.tripDetails.supply'])
            ->where('status', 'success')
            ->orderByDesc('delivered_at')
            ->limit(10)
            ->get();

        $items = $deliveries->map(function ($d) {
            $trip     = $d->trip;
            $hh       = $d->household;
            $details  = $trip?->tripDetails ?? collect();

            // Tóm tắt hàng hóa (lấy mặt hàng đầu tiên)
            $supplyText = '—';
            if ($details->isNotEmpty() && $details->first()->supply) {
                $s   = $details->first()->supply;
                $qty = $details->count() > 0
                    ? floor($details->first()->quantity_loaded / max($trip->deliveries()->count(), 1))
                    : 0;
                $supplyText = "{$qty} {$s->unit} {$s->name}";
            }

            return [
                'time'        => $d->delivered_at ? $d->delivered_at->diffForHumans() : 'Vừa xong',
                'trip_code'   => $trip?->trip_code ?? 'N/A',
                'supply_text' => $supplyText,
                'address'     => $hh?->address ?? '—',
            ];
        });

        return response()->json($items);
    }

    // ============================================================
    //  API – Tra cứu CCCD
    // ============================================================

    public function lookupCccd(Request $request)
    {
        $request->validate(['cccd' => ['required', 'string', 'size:12']]);

        $user = User::where('identity_card', $request->cccd)->first();

        if (!$user) {
            return response()->json(['found' => false, 'message' => 'Không tìm thấy thông tin với số CCCD này.'], 404);
        }

        $household = $user->household;

        if (!$household) {
            return response()->json(['found' => false, 'message' => 'Số CCCD này chưa đăng ký cứu trợ.'], 404);
        }

        // Lần nhận gần nhất
        $lastDelivery = Delivery::where('household_id', $household->id)
            ->whereIn('status', ['success', 'warning'])
            ->orderByDesc('delivered_at')
            ->first();

        return response()->json([
            'found'           => true,
            'name'            => $user->name,
            'cccd_masked'     => substr($request->cccd, 0, 3) . '****' . substr($request->cccd, -3),
            'address'         => $household->address,
            'status'          => $household->status,
            'status_label'    => $household->status_label,
            'status_color'    => $household->status_color,
            'member_count'    => $household->member_count,
            'last_delivery'   => $lastDelivery
                ? $lastDelivery->delivered_at->format('H:i d/m/Y')
                : null,
        ]);
    }

    // ============================================================
    //  PHẢN HỒI – Gửi từ trang chủ (có ảnh Cloudinary)
    // ============================================================

    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'identity_card' => ['required', 'string', 'size:12'],
            'type'          => ['required', 'string', 'max:100'],
            'content'       => ['required', 'string', 'min:10', 'max:2000'],
            'image'         => ['nullable', 'image', 'max:8192'],
        ], [
            'identity_card.required' => 'Vui lòng nhập số CCCD.',
            'identity_card.size'     => 'Số CCCD phải đúng 12 ký tự.',
            'content.min'            => 'Nội dung phải có ít nhất 10 ký tự.',
        ]);

        DB::beginTransaction();
        try {
            $imageUrl = null;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imageUrl = $this->cloudinary->upload($request->file('image'), 'daiphuc/feedbacks');
            }

            // Tìm user theo CCCD (nếu có)
            $user = User::where('identity_card', $validated['identity_card'])->first();

            Feedback::create([
                'user_id'       => $user?->id,
                'identity_card' => $validated['identity_card'],
                'name'          => $validated['name'],
                'phone'         => $validated['phone'] ?? null,
                'type'          => $validated['type'],
                'content'       => $validated['content'],
                'image_url'     => $imageUrl,
                'status'        => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Cảm ơn! Phản hồi của bạn đã được gửi thành công. Chúng tôi sẽ xem xét sớm nhất có thể.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[WelcomeController@submitFeedback] ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi. Vui lòng thử lại.'], 500);
        }
    }

    // ============================================================
    //  PRIVATE – Tính tổng kg hàng đã phát
    // ============================================================

    private function calcTotalKg(): float
    {
        // Tổng quantity_delivered từ trip_details của các chuyến completed
        $totalKg = TripDetail::whereHas('trip', fn($q) => $q->where('status', 'completed'))
            ->sum('quantity_delivered');

        return round($totalKg / 1000, 2); // quy đổi ra tấn
    }
}
