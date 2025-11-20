<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        // Skip logging for some noise routes (assets, health checks, debugging)
        $path = $request->path();
        if ($this->shouldSkip($path)) {
            return $response;
        }

        $userId = $this->resolveUserIdFromRequest($request);

        // Prepare request data with basic redaction
        $queryParams = $request->query();
        $bodyParams = $this->extractRequestBody($request);

        ActivityLog::create([
            'user_id' => $userId,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1024),
            'method' => $request->method(),
            'path' => '/' . ltrim($path, '/'),
            'route_name' => optional($request->route())->getName(),
            'query' => $queryParams ?: null,
            'body' => $bodyParams ?: null,
            'status_code' => method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null,
            'duration_ms' => (int) round((microtime(true) - $startTime) * 1000),
        ]);

        return $response;
    }

    private function shouldSkip(string $path): bool
    {
        $normalized = '/' . ltrim($path, '/');
        $skipPrefixes = [
            '/_debugbar',
            '/telescope',
            '/horizon',
            '/storage',
            '/vendor',
            '/assets',
            '/build',
        ];

        foreach ($skipPrefixes as $prefix) {
            if (strpos($normalized, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    private function resolveUserIdFromRequest(Request $request): ?int
    {
        // Prefer resolved user from request (if any middleware already authenticated)
        $user = $request->user();
        if ($user) {
            return $user->id ?? null;
        }

        // Try to read access_token cookie and decode via JWT to get user
        $token = $request->cookie('access_token');
        if (!$token) {
            return null;
        }

        try {
            $authenticatedUser = JWTAuth::setToken($token)->authenticate();
            return $authenticatedUser ? ($authenticatedUser->id ?? null) : null;
        } catch (JWTException $e) {
            return null;
        }
    }

    private function extractRequestBody(Request $request): ?array
    {
        // Do not store sensitive payloads for auth endpoints
        $path = '/' . ltrim($request->path(), '/');
        $lowerPath = strtolower($path);
        if (strpos($lowerPath, '/login') !== false || strpos($lowerPath, '/auth') !== false) {
            return null;
        }

        // Only attempt body for non-GET
        if (strtoupper($request->method()) === 'GET') {
            return null;
        }

        // Extract input and redact common sensitive keys
        $input = $request->all();
        if (!is_array($input)) {
            return null;
        }

        $redactKeys = [
            'password', 'password_confirmation', 'current_password', 'token', 'access_token', 'refresh_token',
            'secret', 'client_secret',
        ];
        $redacted = $this->redactArray($input, $redactKeys);

        // Avoid huge payloads
        try {
            $json = json_encode($redacted);
            if ($json !== false && strlen($json) > 5000) {
                return ['_truncated' => true];
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return $redacted;
    }

    private function redactArray(array $data, array $keysToRedact): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (in_array((string) $key, $keysToRedact, true)) {
                $result[$key] = '[REDACTED]';
                continue;
            }
            if (is_array($value)) {
                $result[$key] = $this->redactArray($value, $keysToRedact);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}


