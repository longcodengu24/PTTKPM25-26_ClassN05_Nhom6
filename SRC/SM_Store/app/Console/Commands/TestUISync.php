<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class TestUISync extends Command
{
    protected $signature = 'test:ui-sync {uid}';
    protected $description = 'Test UI synchronization for coins display';

    public function handle()
    {
        $uid = $this->argument('uid');
        $firestore = new FirestoreSimple();

        $this->info("Testing UI synchronization for user: $uid");

        // Get current user data
        $user = $firestore->getDocument('users', $uid);
        if (!$user) {
            $this->error("User not found: $uid");
            return;
        }

        $this->info("Current user data:");
        $this->line("Name: " . ($user['name'] ?? 'N/A'));
        $this->line("Email: " . ($user['email'] ?? 'N/A'));
        $this->line("Coins: " . ($user['coins'] ?? 0));
        $this->line("Role: " . ($user['role'] ?? 'N/A'));

        // Test coin update
        $originalCoins = $user['coins'] ?? 0;
        $testCoins = $originalCoins + 100;

        $this->info("\nTesting coins update...");
        $this->line("Original coins: $originalCoins");
        $this->line("Test coins: $testCoins");

        // Update coins
        $updateResult = $firestore->updateCoinsOnly($uid, $testCoins);
        if ($updateResult) {
            $this->info("✅ Coins updated successfully");

            // Verify update
            $updatedUser = $firestore->getDocument('users', $uid);
            if ($updatedUser) {
                $this->line("Verified coins: " . ($updatedUser['coins'] ?? 0));
                $this->line("Name after update: " . ($updatedUser['name'] ?? 'N/A'));
                $this->line("Email after update: " . ($updatedUser['email'] ?? 'N/A'));

                if (($updatedUser['coins'] ?? 0) == $testCoins &&
                    ($updatedUser['name'] ?? '') != '' &&
                    ($updatedUser['email'] ?? '') != ''
                ) {
                    $this->info("✅ Data integrity maintained");
                } else {
                    $this->error("❌ Data integrity compromised");
                }
            }

            // Restore original coins
            $firestore->updateCoinsOnly($uid, $originalCoins);
            $this->info("Coins restored to original value");
        } else {
            $this->error("❌ Failed to update coins");
        }

        $this->info("\n🎯 UI Sync Test Recommendations:");
        $this->line("1. LoadUserData middleware should fetch fresh data from Firestore");
        $this->line("2. Views should use \$currentUser data instead of session");
        $this->line("3. After transactions, coins should update immediately in navbar and account layout");

        return 0;
    }
}
