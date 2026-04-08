<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name', '');
        $this->apiKey    = config('services.cloudinary.api_key', '');
        $this->apiSecret = config('services.cloudinary.api_secret', '');
    }

    /**
     * Upload ảnh lên Cloudinary từ file path hoặc base64.
     * Trả về URL public của ảnh, hoặc null nếu thất bại.
     */
    public function upload($file, string $folder = 'daiphuc'): ?string
    {
        if (empty($this->cloudName) || empty($this->apiKey)) {
            Log::warning('[Cloudinary] Chưa cấu hình API key.');
            return null;
        }

        try {
            $timestamp = time();
            $params = [
                'folder'    => $folder,
                'timestamp' => $timestamp,
            ];

            // Sinh signature
            $signString = "folder={$folder}&timestamp={$timestamp}{$this->apiSecret}";
            $signature  = sha1($signString);

            $uploadUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";

            $response = Http::timeout(30)->attach(
                'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
            )->post($uploadUrl, [
                'api_key'   => $this->apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
                'folder'    => $folder,
            ]);

            if ($response->successful()) {
                return $response->json('secure_url');
            }

            Log::warning('[Cloudinary] Upload thất bại:', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('[Cloudinary] Exception: ' . $e->getMessage());
            return null;
        }
    }
}
