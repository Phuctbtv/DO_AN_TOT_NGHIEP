<?php

namespace App\Services;

class QrCodeService
{
    /**
     * Sinh mã QR string dạng HD-{YEAR}-{ID_PADDED}
     * Ví dụ: HD-2026-00042
     */
    public function generate(int $householdId): string
    {
        $year   = date('Y');
        $padded = str_pad($householdId, 5, '0', STR_PAD_LEFT);

        return "HD-{$year}-{$padded}";
    }

    /**
     * Sinh QR image URL dùng Google Charts API (không cần thư viện ngoài)
     * Trả về URL hình ảnh QR code có thể nhúng thẳng vào <img>
     */
    public function generateImageUrl(string $qrCode, int $size = 200): string
    {
        $encoded = urlencode($qrCode);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encoded}&margin=10";
    }
}
