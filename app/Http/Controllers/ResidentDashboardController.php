<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Household;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResidentDashboardController extends Controller
{
    public function __construct(
        private QrCodeService $qrCode,
    ) {}

    // ============================================================
    //  RESIDENT DASHBOARD – Trang chủ
    // ============================================================

    public function index()
    {
        $user = Auth::user();

        // Load household (không eager deliveries — sẽ phân trang riêng)
        $household = Household::where('resident_id', $user->id)->first();

        // QR image URL
        $qrImageUrl = null;
        if ($household?->qr_code) {
            $qrImageUrl = $this->qrCode->generateImageUrl($household->qr_code, 220);
        }

        // Lịch sử giao hàng — phân trang 5 mục/trang
        $deliveries = collect();
        if ($household) {
            $deliveries = $household->deliveries()
                ->with('trip.tripDetails.supply')
                ->whereIn('status', ['success', 'warning'])
                ->orderByDesc('delivered_at')
                ->paginate(2);
        }

        // ── TIMELINE (4 bước) ──────────────────────────────
        // Dùng tổng count — không bị ảnh hưởng bởi phân trang
        $allDeliveries  = $household ? $household->deliveries()->get() : collect();
        $timelineSteps  = $this->buildTimeline($household, $allDeliveries);

        return view('dashboard.resident', compact(
            'user',
            'household',
            'qrImageUrl',
            'deliveries',
            'timelineSteps',
        ));
    }

    // ============================================================
    //  RESIDENT – Cập nhật thông tin (phone, member_count)
    // ============================================================

    public function updateInfo(Request $request)
    {
        $user = Auth::user();
        $household = Household::where('resident_id', $user->id)->first();

        if (!$household) {
            return back()->with('error', 'Không tìm thấy thông tin hộ dân.');
        }

        $validated = $request->validate([
            'phone'        => ['required', 'string', 'max:20'],
            'member_count' => ['required', 'integer', 'min:1', 'max:20'],
        ], [
            'phone.required'        => 'Vui lòng nhập số điện thoại.',
            'member_count.required' => 'Vui lòng nhập số thành viên.',
            'member_count.min'      => 'Số thành viên tối thiểu là 1.',
            'member_count.max'      => 'Số thành viên tối đa là 20.',
        ]);

        DB::beginTransaction();
        try {
            // Cập nhật bảng households
            $household->update([
                'phone'        => $validated['phone'],
                'member_count' => $validated['member_count'],
            ]);

            // Đồng bộ phone lên bảng users
            $user->update(['phone' => $validated['phone']]);

            DB::commit();

            return back()->with('success', '✅ Cập nhật thông tin thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[ResidentDashboard@updateInfo] ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi. Vui lòng thử lại.');
        }
    }

    // ============================================================
    //  RESIDENT – Gửi phản hồi (không có ảnh)
    // ============================================================

    public function submitFeedback(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'content.required' => 'Vui lòng nhập nội dung phản hồi.',
            'content.min'      => 'Nội dung phải có ít nhất 10 ký tự.',
        ]);

        Feedback::create([
            'user_id'       => $user->id,
            'identity_card' => $user->identity_card,
            'name'          => $user->name,
            'phone'         => $user->phone,
            'type'          => 'general',
            'content'       => $validated['content'],
            'status'        => 'pending',
        ]);

        return back()->with('success', '✅ Cảm ơn! Phản hồi của bạn đã được gửi thành công.');
    }

    // ============================================================
    //  RESIDENT – Tải QR Code
    // ============================================================

    public function downloadQr()
    {
        $user      = Auth::user();
        $household = Household::where('resident_id', $user->id)->first();

        if (!$household?->qr_code) {
            return back()->with('error', 'Chưa có mã QR để tải xuống.');
        }

        $url = $this->qrCode->generateImageUrl($household->qr_code, 400);

        // Stream ảnh về trình duyệt với header tải xuống
        $imageContent = file_get_contents($url);
        if ($imageContent === false) {
            return back()->with('error', 'Không thể tải QR. Vui lòng thử lại.');
        }

        $filename = 'QR_' . $household->qr_code . '.png';

        return response($imageContent, 200)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache');
    }

    // ============================================================
    //  PRIVATE HELPER – Tính timeline 4 bước
    // ============================================================

    private function buildTimeline(?Household $household, $deliveries): array
    {
        if (!$household) {
            return [];
        }

        $hasDelivery = $deliveries->isNotEmpty();
        $hasSuccess  = $deliveries->whereIn('status', ['success', 'warning'])->isNotEmpty();
        $isActive    = $household->isActive();

        return [
            // Bước 1: Đăng ký
            [
                'icon'     => '✅',
                'title'    => 'Đã đăng ký',
                'desc'     => 'Đơn đăng ký đã gửi lúc ' . $household->created_at->format('H:i d/m/Y'),
                'status'   => 'done', // luôn done
                'color'    => '#10b981',
                'bg'       => '#d1fae5',
            ],

            // Bước 2: Admin phê duyệt
            [
                'icon'     => $isActive ? '✅' : '⏳',
                'title'    => $isActive ? 'Admin đã phê duyệt' : 'Chờ Admin phê duyệt',
                'desc'     => $isActive
                    ? 'Được duyệt vào ' . $household->updated_at->format('H:i d/m/Y') . ' — QR code đã cấp'
                    : 'Admin đang xem xét thông tin của bạn',
                'status'   => $isActive ? 'done' : 'active',
                'color'    => $isActive ? '#10b981' : '#f59e0b',
                'bg'       => $isActive ? '#d1fae5' : '#fef3c7',
            ],

            // Bước 3: Chờ giao hàng
            [
                'icon'     => $hasDelivery ? '✅' : '🚛',
                'title'    => $hasDelivery ? 'Chuyến xe đã đến' : 'Chờ giao hàng',
                'desc'     => $hasDelivery
                    ? 'Chuyến xe cứu trợ đã đến khu vực của bạn'
                    : 'Chờ chuyến xe cứu trợ đến khu vực của bạn',
                'status'   => $hasDelivery ? 'done' : ($isActive ? 'active' : 'pending'),
                'color'    => $hasDelivery ? '#10b981' : ($isActive ? '#3b82f6' : '#cbd5e1'),
                'bg'       => $hasDelivery ? '#d1fae5' : ($isActive ? '#dbeafe' : '#f1f5f9'),
            ],

            // Bước 4: Nhận hàng
            [
                'icon'     => $hasSuccess ? '📦' : '📦',
                'title'    => $hasSuccess ? 'Đã nhận hàng' : 'Nhận hàng',
                'desc'     => $hasSuccess
                    ? 'Đã nhận hàng cứu trợ thành công ' . $deliveries->whereIn('status', ['success', 'warning'])->count() . ' lần'
                    : 'Xuất trình QR code khi tài xế đến',
                'status'   => $hasSuccess ? 'done' : 'pending',
                'color'    => $hasSuccess ? '#10b981' : '#cbd5e1',
                'bg'       => $hasSuccess ? '#d1fae5' : '#f1f5f9',
            ],
        ];
    }
}
