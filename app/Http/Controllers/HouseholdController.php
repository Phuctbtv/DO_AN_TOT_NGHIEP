<?php

namespace App\Http\Controllers;

use App\Http\Requests\HouseholdRegisterRequest;
use App\Models\Household;
use App\Models\User;
use App\Services\CloudinaryService;
use App\Services\QrCodeService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class HouseholdController extends Controller
{
    public function __construct(
        private CloudinaryService $cloudinary,
        private QrCodeService     $qrCode,
        private TelegramService   $telegram,
    ) {}

    // ============================================================
    //  PUBLIC – Đăng ký cứu trợ (không cần đăng nhập)
    // ============================================================

    public function publicRegister(HouseholdRegisterRequest $request)
    {
        $validated = $request->validated();

        // Kiểm tra CCCD đã tồn tại chưa
        $existingHousehold = Household::whereHas('resident', function ($q) use ($validated) {
            $q->where('identity_card', $validated['identity_card']);
        })->first();

        if ($existingHousehold) {
            // Đã có rồi – báo lỗi rõ ràng
            $status = $existingHousehold->status_label;
            return response()->json([
                'success' => false,
                'message' => "Số CCCD này đã đăng ký cứu trợ (trạng thái: {$status}). Vui lòng đăng nhập để kiểm tra.",
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Tìm hoặc tạo User
            $user = User::where('identity_card', $validated['identity_card'])->first();

            if (!$user) {
                $user = User::create([
                    'name'          => $validated['name'],
                    'email'         => $validated['identity_card'] . '@daiphuc.local',
                    'password'      => Hash::make($validated['identity_card']),
                    'role'          => 'resident',
                    'phone'         => $validated['phone'],
                    'identity_card' => $validated['identity_card'],
                    'address'       => $validated['address'],
                    'latitude'      => $validated['lat'] ?? null,
                    'longitude'     => $validated['lng'] ?? null,
                ]);
            }

            // 2. Upload ảnh hiện trường (nếu có)
            $sceneImageUrl = null;
            if ($request->hasFile('scene_image') && $request->file('scene_image')->isValid()) {
                $sceneImageUrl = $this->cloudinary->upload($request->file('scene_image'), 'daiphuc/scenes');
            }

            // 3. Tạo Household với status = 'pending'
            $household = Household::create([
                'resident_id'    => $user->id,
                'household_name' => $validated['name'],
                'address'        => $validated['address'],
                'lat'            => $validated['lat'] ?? null,
                'lng'            => $validated['lng'] ?? null,
                'phone'          => $validated['phone'],
                'member_count'   => $validated['member_count'] ?? 1,
                'scene_image'    => $sceneImageUrl,
                'status'         => 'pending',
                'qr_code'        => null,
                'priority_level' => 3,
            ]);

            DB::commit();

            // 4. Thông báo Telegram cho Admin
            $this->telegram->notifyNewRegistration(
                $validated['name'],
                $validated['identity_card'],
                $validated['address']
            );

            return response()->json([
                'success' => true,
                'message' => '✅ Yêu cầu của bạn đã được gửi thành công! Vui lòng chờ admin xem xét và phê duyệt.',
                'data'    => [
                    'household_id' => $household->id,
                    'name'         => $validated['name'],
                    'status'       => 'pending',
                ],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[HouseholdController@publicRegister] ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    // ============================================================
    //  ADMIN – Danh sách tất cả hộ dân
    // ============================================================

    public function index(Request $request)
    {
        $query = Household::with('resident')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) =>
                $q->where('household_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('resident', fn ($q2) =>
                      $q2->where('identity_card', 'like', '%' . $request->search . '%')
                         ->orWhere('phone', 'like', '%' . $request->search . '%')
                  )
            )
            ->latest();

        $households   = $query->paginate(15)->withQueryString();
        $pendingCount = Household::pending()->count();

        return view('households.index', compact('households', 'pendingCount'));
    }

    // ============================================================
    //  ADMIN – Danh sách chờ duyệt
    // ============================================================

    public function pending(Request $request)
    {
        $households   = Household::with('resident')->pending()->latest()->paginate(20);
        $pendingCount = $households->total();

        return view('households.pending', compact('households', 'pendingCount'));
    }

    // ============================================================
    //  ADMIN – Chi tiết hộ dân
    // ============================================================

    public function show(Household $household)
    {
        $household->load('resident');
        return view('households.show', compact('household'));
    }

    // ============================================================
    //  ADMIN – PHÊ DUYỆT
    // ============================================================

    public function approve(Request $request, Household $household)
    {
        if (!$household->isPending()) {
            return back()->with('error', 'Chỉ có thể duyệt hộ dân đang ở trạng thái chờ.');
        }

        // Validate mức ưu tiên (nếu được truyền vào)
        $request->validate([
            'priority_level' => ['nullable', 'integer', 'between:1,4'],
        ]);

        // Sinh QR Code
        $qrCode = $this->qrCode->generate($household->id);

        $household->update([
            'status'         => 'active',
            'qr_code'        => $qrCode,
            'priority_level' => $request->input('priority_level', $household->priority_level),
        ]);

        // Gửi Telegram cho hộ dân + nhóm + admin
        $residentChatId = $household->resident->telegram_chat_id ?? null;
        $this->telegram->notifyApproved(
            $household->household_name,
            $qrCode,
            $residentChatId
        );

        return back()->with('success', "Đã phê duyệt hộ dân <strong>{$household->household_name}</strong> và gửi thông báo!");
    }

    // ============================================================
    //  ADMIN – TỪ CHỐI
    // ============================================================

    public function reject(Request $request, Household $household)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'rejection_reason.required' => 'Vui lòng nhập lý do từ chối.',
            'rejection_reason.min'      => 'Lý do phải có ít nhất 10 ký tự.',
        ]);

        if (!$household->isPending()) {
            return back()->with('error', 'Chỉ có thể từ chối hộ dân đang ở trạng thái chờ.');
        }

        $household->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Gửi Telegram cho hộ dân
        $residentChatId = $household->resident->telegram_chat_id ?? null;
        $this->telegram->notifyRejected(
            $household->household_name,
            $request->rejection_reason,
            $residentChatId
        );

        return back()->with('success', "Đã từ chối và thông báo hộ dân <strong>{$household->household_name}</strong>.");
    }
}
