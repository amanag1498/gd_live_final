<?php

namespace App\Http\Middleware;

use App\Services\AppSettingsService;
use Closure;
use Illuminate\Http\Request;

class EnforceAndroidClientVersion
{
    public function __construct(private AppSettingsService $settings)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$request->is('api/*') || $this->shouldBypass($request)) {
            return $next($request);
        }

        if (!(bool) config('app_features.force_app_upgrade_enabled', false)) {
            return $next($request);
        }

        $platform = strtolower(trim((string) $request->header('X-Client-Platform', '')));
        $versionCode = (int) $request->header('X-App-Version-Code', 0);
        if (!$this->settings->isSupportedClientPlatform($platform)) {
            return $this->reject(
                'UNSUPPORTED_CLIENT_PLATFORM',
                'A supported client platform is required while a mandatory upgrade is active.',
                1,
                $platform,
            );
        }

        $minimumVersionCode = $this->settings->minimumVersionCode($platform);
        if ($versionCode < $minimumVersionCode) {
            return $this->reject(
                'APP_UPGRADE_REQUIRED',
                $this->settings->updateMessage($platform),
                $minimumVersionCode,
                $platform,
            );
        }

        return $next($request);
    }

    private function shouldBypass(Request $request): bool
    {
        return $this->isTrustedRealtimeServerRequest($request)
            || $request->is('api/ping')
            || $request->is('api/health/*')
            || $request->is('api/metrics')
            || $request->is('api/app-config')
            || $request->is('api/app/settings');
    }

    private function isTrustedRealtimeServerRequest(Request $request): bool
    {
        $expected = trim((string) env('WS_INTERNAL_KEY', ''));
        $provided = trim((string) $request->header('X-WS-Internal-Key', ''));

        return $expected !== ''
            && $provided !== ''
            && hash_equals($expected, $provided);
    }

    private function reject(
        string $error,
        string $message,
        int $minimumVersionCode,
        string $platform,
    )
    {
        $platform = strtolower(trim($platform));

        return response()->json([
            'ok' => false,
            'error' => $error,
            'message' => $message,
            'platform' => $platform,
            'minimum_version_code' => $minimumVersionCode,
            ...($platform === 'android'
                ? ['minimum_android_version_code' => $minimumVersionCode]
                : []),
            ...($platform === 'ios'
                ? ['minimum_ios_version_code' => $minimumVersionCode]
                : []),
        ], 426);
    }
}
