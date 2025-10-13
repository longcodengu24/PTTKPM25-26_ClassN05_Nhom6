<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class FullSystemTest extends Command
{
    protected $signature = 'test:full-system';
    protected $description = 'Comprehensive test of coins system and data integrity';

    public function handle()
    {
        $this->info("🔍 COMPREHENSIVE SYSTEM TEST");
        $this->info("============================");

        $firestoreService = new FirestoreSimple();

        // 1. Kiểm tra tất cả users có đầy đủ dữ liệu
        $this->info("1. Checking all users data integrity...");
        $usersResponse = $firestoreService->listDocuments('users');

        $totalUsers = 0;
        $validUsers = 0;
        $incompleteUsers = [];

        if (isset($usersResponse['documents'])) {
            foreach ($usersResponse['documents'] as $doc) {
                $docPath = $doc['name'] ?? '';
                $docId = basename($docPath);
                $totalUsers++;

                $userData = [];
                if (isset($doc['fields'])) {
                    foreach ($doc['fields'] as $field => $value) {
                        if (isset($value['stringValue'])) {
                            $userData[$field] = $value['stringValue'];
                        } elseif (isset($value['integerValue'])) {
                            $userData[$field] = (int)$value['integerValue'];
                        } elseif (isset($value['booleanValue'])) {
                            $userData[$field] = $value['booleanValue'];
                        }
                    }
                }

                $requiredFields = ['name', 'email', 'role', 'coins'];
                $hasAllFields = true;
                foreach ($requiredFields as $field) {
                    if ($field === 'coins') {
                        // Coins có thể là 0, chỉ kiểm tra isset
                        if (!isset($userData[$field])) {
                            $hasAllFields = false;
                            break;
                        }
                    } else {
                        if (!isset($userData[$field]) || empty($userData[$field])) {
                            $hasAllFields = false;
                            break;
                        }
                    }
                }

                if ($hasAllFields) {
                    $validUsers++;
                } else {
                    $missing = [];
                    foreach ($requiredFields as $field) {
                        if ($field === 'coins') {
                            if (!isset($userData[$field])) {
                                $missing[] = $field;
                            }
                        } else {
                            if (!isset($userData[$field]) || empty($userData[$field])) {
                                $missing[] = $field;
                            }
                        }
                    }
                    $incompleteUsers[] = [
                        'id' => $docId,
                        'missing' => $missing,
                        'data' => $userData
                    ];
                }
            }
        }

        $this->line("   Total users: $totalUsers");
        $this->line("   Valid users: $validUsers");
        $this->line("   Incomplete users: " . count($incompleteUsers));

        if (count($incompleteUsers) > 0) {
            $this->error("❌ Found incomplete users:");
            foreach ($incompleteUsers as $user) {
                $this->line("   - {$user['id']}: missing " . implode(', ', $user['missing']));
            }
        } else {
            $this->info("✅ All users have complete data");
        }

        // 2. Test updateCoinsOnly safety
        $this->line("");
        $this->info("2. Testing updateCoinsOnly safety...");

        // Pick first valid user for testing
        if ($validUsers > 0) {
            $testUserId = null;
            foreach ($usersResponse['documents'] as $doc) {
                $docPath = $doc['name'] ?? '';
                $docId = basename($docPath);

                $userData = [];
                if (isset($doc['fields'])) {
                    foreach ($doc['fields'] as $field => $value) {
                        if (isset($value['stringValue'])) {
                            $userData[$field] = $value['stringValue'];
                        } elseif (isset($value['integerValue'])) {
                            $userData[$field] = (int)$value['integerValue'];
                        }
                    }
                }

                if (isset($userData['name']) && isset($userData['email']) && isset($userData['role']) && isset($userData['coins'])) {
                    $testUserId = $docId;
                    break;
                }
            }

            if ($testUserId) {
                // Backup original data
                $originalData = $firestoreService->getDocument('users', $testUserId);
                $originalCoins = $originalData['coins'] ?? 0;

                // Test update
                $testCoins = $originalCoins + 1;
                $firestoreService->updateCoinsOnly($testUserId, $testCoins);

                // Verify
                $updatedData = $firestoreService->getDocument('users', $testUserId);

                $safe = true;
                $issues = [];

                foreach (['name', 'email', 'role', 'avatar'] as $field) {
                    if (($originalData[$field] ?? '') !== ($updatedData[$field] ?? '')) {
                        $safe = false;
                        $issues[] = "Field '$field' changed";
                    }
                }

                if (($updatedData['coins'] ?? 0) !== $testCoins) {
                    $safe = false;
                    $issues[] = "Coins not updated correctly";
                }

                // Restore original coins
                $firestoreService->updateCoinsOnly($testUserId, $originalCoins);

                if ($safe) {
                    $this->info("✅ updateCoinsOnly is safe");
                } else {
                    $this->error("❌ updateCoinsOnly has issues: " . implode(', ', $issues));
                }
            }
        }

        // 3. Summary
        $this->line("");
        $this->info("📊 SYSTEM HEALTH SUMMARY");
        $this->info("========================");

        $overallStatus = (count($incompleteUsers) === 0) ? "✅ HEALTHY" : "⚠️  NEEDS ATTENTION";
        $this->line("Overall Status: $overallStatus");
        $this->line("Users Status: $validUsers/$totalUsers complete");
        $this->line("Coins System: " . ($safe ?? true ? "✅ Safe" : "❌ Unsafe"));

        if ($overallStatus === "✅ HEALTHY") {
            $this->info("");
            $this->info("🎉 SYSTEM IS FULLY OPERATIONAL AND SAFE!");
        }
    }
}
