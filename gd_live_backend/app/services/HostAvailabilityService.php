<?php

namespace App\Services;

use App\Models\HostAvailability;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HostAvailabilityService
{
    public function __construct(
        private HostNotificationService $notifications,
    ) {
    }

    public function ensureForUser(User $user): HostAvailability
    {
        return HostAvailability::firstOrCreate(
            ['user_id' => $user->id],
            ['manual_status' => 'offline', 'socket_status' => 'offline', 'call_status' => 'available']
        );
    }

    public function updateSocketStatus(int $userId, string $socketStatus): HostAvailability
    {
        return DB::transaction(function () use ($userId, $socketStatus) {
            $user = User::query()->findOrFail($userId);
            $availability = $this->ensureForUser($user);
            $before = $availability->replicate();

            $availability->update([
                'socket_status' => $socketStatus,
                'call_status' => $socketStatus === 'offline' && $availability->call_status !== 'busy'
                    ? 'available'
                    : $availability->call_status,
                'last_seen_at' => now(),
            ]);

            Log::info('HOST_AVAILABILITY_SOCKET_STATUS', [
                'user_id' => $userId,
                'socket_status' => $socketStatus,
                'current_call_session_id' => $availability->current_call_session_id,
            ]);

            if ($socketStatus === 'offline') {
                app(CallSessionService::class)->handleDisconnect($userId);
                $availability->refresh();
            }

            $fresh = $availability->fresh();
            $this->publishAvailability($fresh);
            $this->notifications->handleAvailabilityTransition($user, $before, $fresh);
            return $fresh;
        });
    }

    public function setCallStatus(int $userId, string $callStatus, ?int $callSessionId = null): HostAvailability
    {
        return DB::transaction(function () use ($userId, $callStatus, $callSessionId) {
            $user = User::query()->findOrFail($userId);
            $availability = $this->ensureForUser($user);
            $before = $availability->replicate();

            $availability->update([
                'call_status' => $callStatus,
                'current_call_session_id' => $callSessionId,
                'last_seen_at' => now(),
            ]);

            Log::info('HOST_AVAILABILITY_CALL_STATUS', [
                'user_id' => $userId,
                'call_status' => $callStatus,
                'call_session_id' => $callSessionId,
            ]);
            $fresh = $availability->fresh();
            $this->publishAvailability($fresh);
            $this->notifications->handleAvailabilityTransition($user, $before, $fresh);
            return $fresh;
        });
    }

    public function cleanupStaleSocketStatuses(int $seconds = 120): int
    {
        $cutoff = now()->subSeconds($seconds);
        $rows = HostAvailability::query()
            ->where('socket_status', 'online')
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_seen_at')->orWhere('last_seen_at', '<', $cutoff);
            })
            ->get();

        foreach ($rows as $availability) {
            $this->updateSocketStatus($availability->user_id, 'offline');
        }

        return $rows->count();
    }

    private function availabilityReason(HostAvailability $availability): string
    {
        if ($availability->manual_status !== 'online') {
            return 'manually_unavailable';
        }
        if ($availability->socket_status !== 'online') {
            return 'offline';
        }
        if ($availability->call_status !== 'available') {
            return $availability->current_call_session_id ? 'in_another_call' : 'busy';
        }

        return 'available';
    }

    public function publishAvailability(HostAvailability $availability): void
    {
        CallRealtimePublisher::publish('users:availability', [
            'event' => 'user_availability_updated',
            'user_id' => (int) $availability->user_id,
            'manual_status' => $availability->manual_status,
            'socket_status' => $availability->socket_status,
            'call_status' => $availability->call_status,
            'current_call_session_id' => $availability->current_call_session_id,
            'reason' => $this->availabilityReason($availability),
        ]);
    }
}
