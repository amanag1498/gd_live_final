<?php
// app/Services/PresenceReader.php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\HostAvailability;
use App\Models\User;
use Throwable;

class PresenceReader
{
    public function count(): int
    {
        return count($this->onlineUserIds());
    }

    /**
     * @return array<int, array{user_id:int, ttl_ms:int|null, user?:array}>
     */
    public function list(bool $includeUsers = true): array
    {
        $conn = $this->presenceConnection();
        $redisIds = $this->redisOnlineUserIds($conn);
        $fallbackIds = $this->availabilityOnlineUserIds();
        $ids = array_values(array_unique([...$redisIds, ...$fallbackIds]));

        $out = [];
        foreach ($ids as $uid) {
            $ttlMs = null;
            if ($conn !== null) {
                $ttl = $conn->pttl("presence:hb:$uid"); // -1 no expire, -2 no key
                $ttlMs = is_numeric($ttl) && $ttl > 0 ? (int) $ttl : null;
            }
            $out[] = [
                'user_id' => $uid,
                'ttl_ms'  => $ttlMs,
            ];
        }

        if ($includeUsers && $ids) {
            $users = User::query()
                ->whereIn('id', $ids)
                ->get(['id','name','email'])
                ->keyBy('id');
            foreach ($out as &$row) {
                if (isset($users[$row['user_id']])) {
                    $u = $users[$row['user_id']];
                    $row['user'] = ['id'=>$u->id,'name'=>$u->name,'email'=>$u->email];
                }
            }
        }

        usort($out, fn($a,$b) => ($a['ttl_ms'] ?? PHP_INT_MAX) <=> ($b['ttl_ms'] ?? PHP_INT_MAX));
        return $out;
    }

    /**
     * @return array<int, int>
     */
    private function onlineUserIds(): array
    {
        $redisIds = $this->redisOnlineUserIds($this->presenceConnection());
        $fallbackIds = $this->availabilityOnlineUserIds();

        return array_values(array_unique([...$redisIds, ...$fallbackIds]));
    }

    private function presenceConnection(): mixed
    {
        try {
            return Redis::connection('presence');
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param mixed $conn
     * @return array<int, int>
     */
    private function redisOnlineUserIds(mixed $conn): array
    {
        if ($conn === null) {
            return [];
        }

        try {
            $ids = $conn->smembers('presence:online') ?: [];
        } catch (Throwable) {
            return [];
        }

        return array_values(array_filter(array_map('intval', $ids)));
    }

    /**
     * @return array<int, int>
     */
    private function availabilityOnlineUserIds(): array
    {
        return HostAvailability::query()
            ->where('socket_status', 'online')
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
