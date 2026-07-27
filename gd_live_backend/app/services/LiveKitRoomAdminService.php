<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LiveKitRoomAdminService
{
    /**
     * Return whether LiveKit currently has an active host in the room.
     *
     * A null result means LiveKit could not be checked, so callers must fail
     * open and leave the room untouched rather than ending a healthy stream
     * during a control-plane outage.
     */
    public function hasActiveHost(string $roomId): ?bool
    {
        $baseUrl = $this->httpBaseUrl();
        $token = LivekitToken::serverToken(roomId: $roomId);

        if ($baseUrl === '' || $token === '') {
            Log::warning('LIVEKIT_LIST_PARTICIPANTS_SKIPPED', [
                'room_id' => $roomId,
                'reason' => 'admin_credentials_missing',
            ]);

            return null;
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(8)
                ->post(rtrim($baseUrl, '/').'/twirp/livekit.RoomService/ListParticipants', [
                    'room' => $roomId,
                ]);

            if ($response->status() === 404) {
                return false;
            }

            if ($response->failed()) {
                Log::warning('LIVEKIT_LIST_PARTICIPANTS_FAILED', [
                    'room_id' => $roomId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            foreach ((array) $response->json('participants', []) as $participant) {
                if (! is_array($participant) || ! $this->isActiveParticipant($participant)) {
                    continue;
                }

                $identity = (string) ($participant['identity'] ?? '');
                $metadata = $this->participantMetadata($participant['metadata'] ?? null);
                if (
                    str_starts_with($identity, 'host-')
                    || ($metadata['role'] ?? null) === 'host'
                    || ($metadata['is_host'] ?? false) === true
                ) {
                    return true;
                }
            }

            return false;
        } catch (\Throwable $e) {
            Log::warning('LIVEKIT_LIST_PARTICIPANTS_FAILED', [
                'room_id' => $roomId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function setParticipantCanPublish(string $roomId, string $identity, bool $canPublish, ?array $publishSources = null): void
    {
        $baseUrl = $this->httpBaseUrl();
        $token = LivekitToken::serverToken(roomId: $roomId);

        if ($baseUrl === '' || $token === '') {
            throw new \RuntimeException('LiveKit admin credentials are not configured.');
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(8)
            ->post(rtrim($baseUrl, '/').'/twirp/livekit.RoomService/UpdateParticipant', [
                'room' => $roomId,
                'identity' => $identity,
                'permission' => [
                    'canPublish' => $canPublish,
                    'canSubscribe' => true,
                    'canPublishData' => true,
                    'canPublishSources' => $canPublish ? array_values($publishSources ?? []) : [],
                ],
            ]);

        if ($response->failed()) {
            $message = sprintf(
                'LiveKit UpdateParticipant failed (%d): %s',
                $response->status(),
                trim($response->body())
            );
            Log::error('LIVEKIT_UPDATE_PARTICIPANT_FAILED', [
                'room_id' => $roomId,
                'identity' => $identity,
                'can_publish' => $canPublish,
                'publish_sources' => $publishSources,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException($message);
        }
    }

    private function httpBaseUrl(): string
    {
        $url = (string) config('services.livekit.http_url', '');
        if ($url !== '') {
            return $url;
        }

        $wsUrl = (string) config('services.livekit.ws_url', '');
        if ($wsUrl === '') {
            return '';
        }

        return preg_replace('/^ws/i', 'http', $wsUrl) ?? '';
    }

    private function isActiveParticipant(array $participant): bool
    {
        $state = strtoupper((string) ($participant['state'] ?? 'ACTIVE'));

        return ! in_array($state, ['DISCONNECTED', 'UNKNOWN'], true);
    }

    private function participantMetadata(mixed $metadata): array
    {
        if (is_array($metadata)) {
            return $metadata;
        }

        if (! is_string($metadata) || trim($metadata) === '') {
            return [];
        }

        $decoded = json_decode($metadata, true);

        return is_array($decoded) ? $decoded : [];
    }
}
