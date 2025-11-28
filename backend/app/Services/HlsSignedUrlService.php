<?php

namespace App\Services;

use Carbon\Carbon;

class HlsSignedUrlService
{
    /**
     * Secret key dùng để ký URL (phải giống với nginx secure_link_secret)
     */
    protected string $secretKey;

    /**
     * Thời gian hết hạn mặc định (giây)
     */
    protected int $defaultExpiry;

    public function __construct()
    {
        $this->secretKey = config('hls.secret_key', env('HLS_SECRET_KEY', 'your-secret-key'));
        $this->defaultExpiry = config('hls.default_expiry', env('HLS_DEFAULT_EXPIRY', 3600)); // 1 giờ
    }

    /**
     * Tạo chữ ký cho URL video HLS
     * 
     * Sử dụng cơ chế tương tự nginx ngx_http_secure_link_module
     * 
     * @param string $uri Đường dẫn file (ví dụ: /hls/lesson/123/video.m3u8)
     * @param int|null $expiry Thời gian hết hạn (timestamp)
     * @param string|null $clientIp IP của client (nếu muốn giới hạn theo IP)
     * @return array Chứa signature, expires, và các thông tin cần thiết
     */
    public function generateSignature(string $uri, ?int $expiry = null, ?string $clientIp = null): array
    {
        // Tính thời gian hết hạn
        $expires = $expiry ?? (Carbon::now()->timestamp + $this->defaultExpiry);

        // Chuẩn hóa URI
        $uri = '/' . ltrim($uri, '/');

        // Tạo chuỗi cần ký
        // Format: {expires}{uri}{secret_key} hoặc {expires}{uri}{client_ip}{secret_key}
        if ($clientIp) {
            // $stringToSign = $expires . $uri . $clientIp . $this->secretKey;
            $stringToSign = $expires . $uri . ' ' . $this->secretKey;
        } else {
            $stringToSign = $expires . $uri . ' ' . $this->secretKey;
        }

        // Tạo chữ ký MD5 (base64 URL-safe)
        // Nginx secure_link dùng MD5 binary -> base64
        $md5Binary = md5($stringToSign, true);
        $signature = $this->base64UrlEncode($md5Binary);
        // $signature = $md5Binary;

        return [
            'signature' => $signature,
            'expires' => $expires,
            'expires_at' => Carbon::createFromTimestamp($expires)->toIso8601String(),
        ];
    }

    /**
     * Tạo signed URL hoàn chỉnh
     * 
     * @param string $baseUrl URL gốc của nginx (ví dụ: https://video.example.com)
     * @param string $uri Đường dẫn file HLS
     * @param int|null $expiry Thời gian hết hạn (timestamp)
     * @param string|null $clientIp IP của client
     * @return array
     */
    public function generateSignedUrl(string $baseUrl, string $uri, ?int $expiry = null, ?string $clientIp = null): array
    {
        $signature = $this->generateSignature($uri, $expiry, $clientIp);

        $baseUrl = rtrim($baseUrl, '/');
        $uri = '/' . ltrim($uri, '/');

        // Build query string
        $queryParams = [
            'signature' => $signature['signature'],
            'expires' => $signature['expires'],
        ];

        $signedUrl = $uri . '?' . http_build_query($queryParams);

        return [
            'signed_uri' => $signedUrl,
            'signature' => $signature['signature'],
            'expires' => $signature['expires'],
            'expires_at' => $signature['expires_at'],
        ];
    }

    /**
     * Xác thực chữ ký (dùng cho testing hoặc double-check)
     * 
     * @param string $signature signature từ client
     * @param string $uri Đường dẫn file
     * @param int $expires Timestamp hết hạn
     * @param string|null $clientIp IP của client
     * @return bool
     */
    public function verifySignature(string $signature, string $uri, int $expires, ?string $clientIp = null): bool
    {
        // Kiểm tra hết hạn
        if (Carbon::now()->timestamp > $expires) {
            return false;
        }

        // Tạo lại chữ ký và so sánh
        $expectedSignature = $this->generateSignature($uri, $expires, $clientIp);

        return hash_equals($expectedSignature['signature'], $signature);
    }

    /**
     * Base64 URL-safe encoding
     * 
     * @param string $data
     * @return string
     */
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL-safe decoding
     * 
     * @param string $data
     * @return string
     */
    protected function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
