<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AppActivityService
{
    public function recordLogin(User $user, ?string $referralCode = null): void
    {
        $this->updateDailyStreak($user);

        $updates = [];
        $user->refresh();

        if (!$user->referral_code) {
            $updates['referral_code'] = $this->generateReferralCode();
        }

        if (!$user->referred_by_user_id && $referralCode) {
            $referrer = User::query()
                ->where('referral_code', strtoupper(trim($referralCode)))
                ->whereKeyNot($user->id)
                ->first();
            if ($referrer) {
                $updates['referred_by_user_id'] = $referrer->id;
            }
        }

        if ($updates !== []) {
            $user->forceFill($updates)->save();
        }
    }

    public function recordDailyActivity(User $user): array
    {
        return $this->updateDailyStreak($user);
    }

    private function updateDailyStreak(User $user): array
    {
        if (
            !Schema::hasColumn('users', 'last_login_at')
            || !Schema::hasColumn('users', 'last_login_date')
            || !Schema::hasColumn('users', 'current_login_streak_days')
            || !Schema::hasColumn('users', 'max_login_streak_days')
        ) {
            return [
                'updated' => false,
                'current_login_streak_days' => (int) ($user->current_login_streak_days ?? 0),
                'max_login_streak_days' => (int) ($user->max_login_streak_days ?? 0),
            ];
        }

        $today = now()->toDateString();
        $lastDate = optional($user->last_login_date)?->toDateString();
        $current = (int) ($user->current_login_streak_days ?? 0);
        $updates = [];
        $updated = false;

        if ($lastDate === $today) {
            $updates = ['last_login_at' => now()];
            $updated = false;
        } elseif ($lastDate === now()->subDay()->toDateString()) {
            $current++;
            $updates = [
                'last_login_at' => now(),
                'last_login_date' => $today,
                'current_login_streak_days' => max(1, $current),
                'max_login_streak_days' => max((int) $user->max_login_streak_days, max(1, $current)),
            ];
            $updated = true;
        } else {
            $updates = [
                'last_login_at' => now(),
                'last_login_date' => $today,
                'current_login_streak_days' => 1,
                'max_login_streak_days' => max((int) $user->max_login_streak_days, 1),
            ];
            $updated = true;
        }

        $user->forceFill($updates)->save();
        $user->refresh();

        return [
            'updated' => $updated,
            'current_login_streak_days' => (int) ($user->current_login_streak_days ?? 0),
            'max_login_streak_days' => (int) ($user->max_login_streak_days ?? 0),
        ];
    }

    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::query()->where('referral_code', $code)->exists());

        return $code;
    }
}
