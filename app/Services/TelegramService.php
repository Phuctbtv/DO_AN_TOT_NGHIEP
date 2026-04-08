<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $botToken;
    private string $adminChatId;    // Chat cá nhân Admin
    private string $groupChatId;    // Nhóm Telegram
    private string $apiBase;

    public function __construct()
    {
        $this->botToken    = config('services.telegram.bot_token', '');
        $this->adminChatId = config('services.telegram.admin_chat_id', '');
        $this->groupChatId = config('services.telegram.group_chat_id', '');
        $this->apiBase     = "https://api.telegram.org/bot{$this->botToken}";
    }

    // ============================================================
    //  CÀI ĐẶT GỐC
    // ============================================================

    public function sendMessage(string $chatId, string $text): bool
    {
        if (empty($this->botToken) || empty($chatId)) {
            Log::warning("[Telegram] Bot token hoặc chat_id trống (chat_id={$chatId}).");
            return false;
        }
        try {
            $response = Http::timeout(10)->post("{$this->apiBase}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'HTML',
            ]);
            if (!$response->successful()) {
                Log::warning('[Telegram] Gửi thất bại:', ['chat_id' => $chatId, 'status' => $response->status(), 'body' => $response->body()]);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('[Telegram] Exception: ' . $e->getMessage());
            return false;
        }
    }

    // ============================================================
    //  GỬI ĐẾN TỪNG ĐỐI TƯỢNG
    // ============================================================

    /** Gửi riêng cho Admin cá nhân */
    public function sendToAdmin(string $text): bool
    {
        return $this->sendMessage($this->adminChatId, $text);
    }

    /** Gửi vào Nhóm Telegram */
    public function sendToGroup(string $text): bool
    {
        if (empty($this->groupChatId)) {
            Log::info('[Telegram] Chưa cấu hình GROUP_CHAT_ID.');
            return false;
        }
        return $this->sendMessage($this->groupChatId, $text);
    }

    /** Gửi cho cả Admin + Nhóm */
    public function sendToAll(string $text): void
    {
        $this->sendToAdmin($text);
        $this->sendToGroup($text);
    }

    /** Gửi cho Hộ dân cá nhân (nếu có chat_id) */
    public function notifyResident(?string $chatId, string $text): bool
    {
        if (empty($chatId)) {
            Log::info('[Telegram] Hộ dân không có telegram_chat_id.');
            return false;
        }
        return $this->sendMessage($chatId, $text);
    }

    // ============================================================
    //  ĐỐI VỚI ĐĂNG KÝ CỨU TRỢ → GỬI ADMIN (CHAT CÁ NHÂN)
    // ============================================================

    /** Đơn đăng ký mới → notify Admin cá nhân */
    public function notifyNewRegistration(string $name, string $cccd, string $address): void
    {
        $text = "🔔 <b>Đăng ký cứu trợ mới!</b>\n\n"
              . "👤 Họ tên: <b>{$name}</b>\n"
              . "🪪 CCCD: <code>{$cccd}</code>\n"
              . "📍 Địa chỉ: {$address}\n\n"
              . "➡️ Vào Admin Dashboard để xem và duyệt.";

        $this->sendToAdmin($text); // Chỉ gửi riêng Admin
    }

    /** Phê duyệt → notify Admin + Hộ dân riêng */
    public function notifyApproved(string $name, string $qrCode, ?string $residentChatId): void
    {
        $adminText = "✅ <b>Đã phê duyệt đăng ký cứu trợ!</b>\n\n"
                   . "👤 Hộ dân: <b>{$name}</b>\n"
                   . "🎟️ Mã QR: <code>{$qrCode}</code>";
        $this->sendToAdmin($adminText);

        $residentText = "✅ <b>Đăng ký cứu trợ được phê duyệt!</b>\n\n"
                      . "Xin chào <b>{$name}</b>,\n"
                      . "Đăng ký của bạn đã được Admin phê duyệt.\n\n"
                      . "🎟️ Mã hộ dân: <code>{$qrCode}</code>\n\n"
                      . "Đăng nhập vào hệ thống ĐẠI PHÚC để xem QR code nhận hàng.\n"
                      . "📞 Hotline: 1900.636.838";
        $this->notifyResident($residentChatId, $residentText);
    }

    /** Từ chối → notify Admin + Hộ dân riêng */
    public function notifyRejected(string $name, string $reason, ?string $residentChatId): void
    {
        $adminText = "❌ <b>Từ chối đăng ký cứu trợ!</b>\n\n"
                   . "👤 Hộ dân: <b>{$name}</b>\n"
                   . "📝 Lý do: <i>{$reason}</i>";
        $this->sendToAdmin($adminText);

        $residentText = "❌ <b>Đăng ký cứu trợ bị từ chối</b>\n\n"
                      . "Xin chào <b>{$name}</b>,\n"
                      . "Rất tiếc, đăng ký của bạn chưa được phê duyệt.\n\n"
                      . "📝 Lý do: <i>{$reason}</i>\n\n"
                      . "Liên hệ hỗ trợ: 📞 1900.636.838";
        $this->notifyResident($residentChatId, $residentText);
    }

    // ============================================================
    //  ĐỐI VỚI CHUYẾN XE → GỬI NHÓM (GROUP_CHAT_ID)
    // ============================================================

    /** Tạo chuyến xe mới → gửi vào NHÓM */
    public function notifyTripAssigned(string $tripCode, string $driverName, string $warehouseName, string $vehicleInfo, int $itemCount): void
    {
        $text = "🚛 <b>Phân công chuyến xe mới!</b>\n\n"
              . "📋 Mã chuyến: <code>{$tripCode}</code>\n"
              . "👨‍✈️ Tài xế: <b>{$driverName}</b>\n"
              . "🏭 Từ kho: <b>{$warehouseName}</b>\n"
              . "🚗 Phương tiện: {$vehicleInfo}\n"
              . "📦 Số mặt hàng: {$itemCount} loại\n\n"
              . "➡️ Tài xế vui lòng chuẩn bị và xác nhận xuất kho.";

        $this->sendToGroup($text); // Chỉ gửi NHÓM
    }

    /** Cập nhật trạng thái chuyến → gửi vào NHÓM */
    public function notifyTripStatusChanged(string $tripCode, string $driverName, string $newStatus): void
    {
        $icons = [
            'exporting'  => '📤',
            'shipping'   => '🚛',
            'completed'  => '✅',
            'cancelled'  => '❌',
        ];
        $labels = [
            'exporting'  => 'Đang xuất kho',
            'shipping'   => 'Đang vận chuyển',
            'completed'  => 'Hoàn thành',
            'cancelled'  => 'Đã huỷ',
        ];
        $icon  = $icons[$newStatus]  ?? '🔄';
        $label = $labels[$newStatus] ?? $newStatus;

        $text = "{$icon} <b>Trạng thái chuyến xe thay đổi</b>\n\n"
              . "📋 Mã chuyến: <code>{$tripCode}</code>\n"
              . "👨‍✈️ Tài xế: {$driverName}\n"
              . "🔄 Trạng thái: <b>{$label}</b>";

        $this->sendToGroup($text);
    }
}
