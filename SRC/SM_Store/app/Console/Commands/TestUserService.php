<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserService;

class TestUserService extends Command
{
    protected $signature = 'test:user-service';
    protected $description = 'Test UserService duplicate prevention';

    public function handle()
    {
        $this->info('🧪 Testing UserService duplicate prevention...');
        $this->info(str_repeat('=', 80));

        $userService = new UserService();

        try {
            // Test 1: Check existing email
            $this->info('Test 1: Checking if existing email is detected');
            $exists = $userService->emailExists('seller1@demo.com');
            $this->line("  Email seller1@demo.com exists: " . ($exists ? 'YES' : 'NO'));

            // Test 2: Try to create user with existing email
            $this->info('Test 2: Attempting to create user with existing email');
            try {
                $userService->createUser([
                    'email' => 'seller1@demo.com',
                    'name' => 'Test Duplicate',
                    'role' => 'customer'
                ]);
                $this->error('  ❌ Duplicate was allowed - this should not happen!');
            } catch (\Exception $e) {
                $this->line("  ✅ Duplicate prevented: " . $e->getMessage());
            }

            // Test 3: Create new user with unique email
            $this->info('Test 3: Creating user with unique email');
            $testEmail = 'test_user_' . time() . '@example.com';
            try {
                $documentId = $userService->createUser([
                    'email' => $testEmail,
                    'name' => 'Test User',
                    'role' => 'customer',
                    'coins' => 1000
                ]);
                $this->line("  ✅ User created successfully: {$documentId}");

                // Test 4: Try to create same user again
                $this->info('Test 4: Attempting to create same user again');
                try {
                    $userService->createUser([
                        'email' => $testEmail,
                        'name' => 'Test User 2',
                        'role' => 'customer'
                    ]);
                    $this->error('  ❌ Duplicate was allowed - this should not happen!');
                } catch (\Exception $e) {
                    $this->line("  ✅ Duplicate prevented: " . $e->getMessage());
                }

                // Test 5: Get user by email
                $this->info('Test 5: Getting user by email');
                $userData = $userService->getUserByEmail($testEmail);
                if ($userData) {
                    $this->line("  ✅ User found: {$userData['name']} ({$userData['email']})");
                } else {
                    $this->error("  ❌ User not found");
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Error creating user: " . $e->getMessage());
            }

            $this->line("");
            $this->info("🎉 UserService tests completed!");
        } catch (\Exception $e) {
            $this->error("Test failed: " . $e->getMessage());
        }

        return 0;
    }
}
