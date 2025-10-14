<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class UserBalanceService
{
    protected $db;

    public function __construct()
    {
        $credentialsPath = config('firebase.credentials', resource_path('key/firebase.json'));
        $databaseUrl = config('firebase.database_url', 'https://kchip-8865d-default-rtdb.asia-southeast1.firebasedatabase.app');

        $this->db = (new Factory)
            ->withServiceAccount($credentialsPath)
            ->withDatabaseUri($databaseUrl)
            ->createDatabase();
    }

    public function getUserCoins(string $userId): int
    {
        $val = $this->db->getReference("users/{$userId}/coins")->getValue();
        return (int)($val ?? 0);
    }

    /**
     * Alias for getUserCoins() - for backward compatibility
     */
    public function getUserBalance(string $userId): int
    {
        return $this->getUserCoins($userId);
    }

    /**
     * Cộng coins và ghi Realtime DB (path users/{uid}/coins)
     */
    public function addCoins(string $userId, int $amount): array
    {
        try {
            $old = $this->getUserCoins($userId);
            $new = $old + (int)$amount;

            // Update Realtime Database
            $this->db->getReference("users/{$userId}/coins")->set($new);

            // verify
            $verify = (int)($this->db->getReference("users/{$userId}/coins")->getValue() ?? 0);
            $ok = ($verify === $new);

            Log::info("UserBalanceService::addCoins -> uid={$userId}, +{$amount}, old={$old}, new={$new}, verify={$verify}, ok=".($ok?'true':'false'));

            return [
                'success'     => $ok,
                'old_balance' => $old,
                'new_balance' => $verify,
            ];
        } catch (\Throwable $e) {
            Log::error('UserBalanceService::addCoins error: '.$e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * (Tuỳ chọn) ghi lịch sử thay đổi số dư nếu bạn có bảng history
     */
    public function recordBalanceChange(string $userId, int $delta, string $type, string $note = null, $transactionId = null): bool
    {
        try {
            $ref = $this->db->getReference("users/{$userId}/balance_history")->push([
                'delta'          => (int)$delta,
                'type'           => $type,
                'note'           => $note,
                'transaction_id' => $transactionId,
                'timestamp'      => now()->toISOString(),
            ]);
            return !empty($ref->getKey());
        } catch (\Throwable $e) {
            Log::error('recordBalanceChange error: '.$e->getMessage());
            return false;
        }
    }
}
