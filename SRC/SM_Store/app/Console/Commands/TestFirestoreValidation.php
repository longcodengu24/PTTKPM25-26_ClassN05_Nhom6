<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class TestFirestoreValidation extends Command
{
    protected $signature = 'app:test-firestore-validation';
    protected $description = 'Test validation của FirestoreSimple service';

    public function handle()
    {
        $this->info("🧪 TEST VALIDATION CỦA FIRESTORE SERVICE");
        $this->line(str_repeat("=", 60));

        $firestore = new FirestoreSimple();
        $testCases = [
            [
                'name' => 'Valid user data',
                'data' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'coins' => 100,
                    'role' => 'user',
                    'avatar' => 'https://example.com/avatar.jpg'
                ],
                'should_pass' => true
            ],
            [
                'name' => 'Invalid email',
                'data' => [
                    'name' => 'Test User',
                    'email' => 'invalid-email',
                    'coins' => 100,
                    'role' => 'user'
                ],
                'should_pass' => false
            ],
            [
                'name' => 'Invalid role',
                'data' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'coins' => 100,
                    'role' => 'invalid_role'
                ],
                'should_pass' => false
            ],
            [
                'name' => 'Invalid coins (non-numeric)',
                'data' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'coins' => 'abc',
                    'role' => 'user'
                ],
                'should_pass' => false
            ],
            [
                'name' => 'Invalid field',
                'data' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'coins' => 100,
                    'role' => 'user',
                    'invalid_field' => 'should not be allowed'
                ],
                'should_pass' => false
            ],
            [
                'name' => 'Empty data',
                'data' => [],
                'should_pass' => false
            ]
        ];

        $passed = 0;
        $failed = 0;

        foreach ($testCases as $index => $testCase) {
            $this->line("\n🧪 Test #{" . ($index + 1) . "}: {$testCase['name']}");

            try {
                // Test bằng cách gọi reflection để test private method
                $reflection = new \ReflectionClass($firestore);
                $method = $reflection->getMethod('validateUpdateData');
                $method->setAccessible(true);

                $method->invoke($firestore, 'users', $testCase['data']);

                if ($testCase['should_pass']) {
                    $this->info("   ✅ PASS - Validation passed as expected");
                    $passed++;
                } else {
                    $this->error("   ❌ FAIL - Validation should have failed but passed");
                    $failed++;
                }
            } catch (\Exception $e) {
                if (!$testCase['should_pass']) {
                    $this->info("   ✅ PASS - Validation failed as expected: " . $e->getMessage());
                    $passed++;
                } else {
                    $this->error("   ❌ FAIL - Validation failed unexpectedly: " . $e->getMessage());
                    $failed++;
                }
            }
        }

        $this->line("\n📊 KẾT QUẢ TEST:");
        $this->line(str_repeat("-", 60));
        $this->info("✅ Passed: {$passed}");
        if ($failed > 0) {
            $this->error("❌ Failed: {$failed}");
        } else {
            $this->line("❌ Failed: {$failed}");
        }

        $total = count($testCases);
        $this->line("📋 Total: {$total}");

        if ($failed === 0) {
            $this->info("\n🎉 TẤT CẢ TEST VALIDATION ĐỀU PASSED!");
            $this->line("✅ FirestoreSimple service có validation mạnh mẽ");
            $this->line("🛡️  Dữ liệu users được bảo vệ khỏi input không hợp lệ");
        } else {
            $this->error("\n⚠️  CÓ {$failed} TEST FAILED!");
            $this->line("🔧 Cần kiểm tra lại logic validation");
        }
    }
}
