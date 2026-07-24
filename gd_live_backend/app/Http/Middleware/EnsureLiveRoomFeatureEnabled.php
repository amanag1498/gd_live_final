<?php

namespace App\Http\Middleware;

use App\Models\LiveRoom;
use App\Services\AppSettingsService;
use Closure;
use Illuminate\Http\Request;

class EnsureLiveRoomFeatureEnabled
{
    public function __construct(private AppSettingsService $settings)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $roomType = $this->resolveRoomType($request);
        $platform = $request->header('X-Client-Platform');

        if ($roomType === null) {
            $videoEnabled = $this->settings->featureEnabled(
                'video_rooms_enabled',
                $platform,
            );

            if ($videoEnabled) {
                return $next($request);
            }

            return $this->reject('live_rooms_disabled', 'Live rooms are currently unavailable.');
        }

        if (
            $roomType === 'video'
            && !$this->settings->featureEnabled('video_rooms_enabled', $platform)
        ) {
            return $this->reject('video_rooms_enabled', 'Video rooms are currently unavailable.');
        }

        return $next($request);
    }

    private function resolveRoomType(Request $request): ?string
    {
        if ($request->route('live_room') instanceof LiveRoom) {
            return (string) ($request->route('live_room')->room_type ?? 'video');
        }

        $roomId = $request->route('room_id');
        if (is_string($roomId) && $roomId !== '') {
            $resolved = LiveRoom::query()
                ->where('room_id', $roomId)
                ->value('room_type');

            if (is_string($resolved) && $resolved !== '') {
                return $resolved;
            }
        }

        $requested = strtolower(trim((string) $request->input('room_type', '')));
        if ($requested === 'video') {
            return $requested;
        }

        if ($request->is('api/live/rooms') && $request->isMethod('post')) {
            return 'video';
        }

        return null;
    }

    private function reject(string $featureKey, string $message)
    {
        return response()->json([
            'ok' => false,
            'error' => 'FEATURE_DISABLED',
            'feature' => $featureKey,
            'message' => $message,
        ], 403);
    }
}
