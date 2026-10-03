<?php

namespace App\Repositories\User;

use App\Models\User\UserTwoFactorCode;

class UserTwoFactorCodeRepository {
    public const DEFAULT_EXPIRATION_MINUTES = 5;

    public function create(
        int $userId,
        string $codeHash,
        int $expirationMinutes = self::DEFAULT_EXPIRATION_MINUTES,
    ): UserTwoFactorCode {
        return UserTwoFactorCode::create([
            'user_id' => $userId,
            'code_hash' => $codeHash,
            'expires_at' => now()->addMinutes($expirationMinutes),
        ]);
    }

    public function invalidatePreviousCodes(int $userId): void {
        UserTwoFactorCode::query()
            ->where('user_id', $userId)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);
    }

    public function findLatestValidByUserId(int $userId): ?UserTwoFactorCode {
        return UserTwoFactorCode::query()
            ->where('user_id', $userId)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }

    public function incrementAttempts(int $twoFactorCodeId): void {
        UserTwoFactorCode::query()
            ->whereKey($twoFactorCodeId)
            ->increment('attempts');
    }

    public function markAsUsed(int $twoFactorCodeId): void {
        UserTwoFactorCode::query()
            ->whereKey($twoFactorCodeId)
            ->update([
                'used_at' => now(),
            ]);
    }
}
