<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class TestCoinsSafety extends Command
{
    protected $signature = 'test:coins-safety {user_id} {--amount=100}';
    protected $description = 'Test coins update safety - verify user data integrity';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = (int) $this->option('amount');

        $firestoreService = new FirestoreSimple();

        $this->info("=== TESTING COINS UPDATE SAFETY ===");
        $this->line("User ID: $userId");
        $this->line("Test amount: $amount");
        $this->line("");

        try {
            // 1. Lấy dữ liệu trước khi update
            $beforeData = $firestoreService->getDocument('users', $userId);
            if (!$beforeData) {
                $this->error("User not found: $userId");
                return;
            }

            $this->info("📊 BEFORE UPDATE:");
            $this->line("Name: " . ($beforeData['name'] ?? 'N/A'));
            $this->line("Email: " . ($beforeData['email'] ?? 'N/A'));
            $this->line("Role: " . ($beforeData['role'] ?? 'N/A'));
            $this->line("Coins: " . ($beforeData['coins'] ?? 0));
            $this->line("Avatar: " . ($beforeData['avatar'] ?? 'N/A'));
            $this->line("");

            // 2. Update coins
            $newCoins = ($beforeData['coins'] ?? 0) + $amount;
            $this->info("🔄 UPDATING COINS...");
            $this->line("Old coins: " . ($beforeData['coins'] ?? 0));
            $this->line("New coins: $newCoins");

            $firestoreService->updateCoinsOnly($userId, $newCoins);

            // 3. Kiểm tra dữ liệu sau update
            $this->info("✅ UPDATE COMPLETED");
            $this->line("");

            $afterData = $firestoreService->getDocument('users', $userId);

            $this->info("📊 AFTER UPDATE:");
            $this->line("Name: " . ($afterData['name'] ?? 'N/A'));
            $this->line("Email: " . ($afterData['email'] ?? 'N/A'));
            $this->line("Role: " . ($afterData['role'] ?? 'N/A'));
            $this->line("Coins: " . ($afterData['coins'] ?? 0));
            $this->line("Avatar: " . ($afterData['avatar'] ?? 'N/A'));
            $this->line("");

            // 4. Kiểm tra tính toàn vẹn dữ liệu
            $this->info("🔍 DATA INTEGRITY CHECK:");

            $checks = [
                'Name preserved' => ($beforeData['name'] ?? '') === ($afterData['name'] ?? ''),
                'Email preserved' => ($beforeData['email'] ?? '') === ($afterData['email'] ?? ''),
                'Role preserved' => ($beforeData['role'] ?? '') === ($afterData['role'] ?? ''),
                'Avatar preserved' => ($beforeData['avatar'] ?? '') === ($afterData['avatar'] ?? ''),
                'Coins updated correctly' => ($afterData['coins'] ?? 0) === $newCoins
            ];

            $allPassed = true;
            foreach ($checks as $check => $passed) {
                $status = $passed ? '✅' : '❌';
                $this->line("$status $check");
                if (!$passed) $allPassed = false;
            }

            $this->line("");
            if ($allPassed) {
                $this->info("🎉 ALL CHECKS PASSED - COINS UPDATE IS SAFE!");
            } else {
                $this->error("⚠️  DATA INTEGRITY ISSUES DETECTED!");
            }
        } catch (\Exception $e) {
            $this->error("ERROR: " . $e->getMessage());
        }
    }
}
