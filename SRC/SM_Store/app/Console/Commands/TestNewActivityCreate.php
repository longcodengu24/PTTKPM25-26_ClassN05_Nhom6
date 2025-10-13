<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActivityService;

class TestNewActivityCreate extends Command
{
    protected $signature = 'activity:test-new-create {user_id}';
    protected $description = 'Test creating a new activity with updated structure';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $activityService = new ActivityService();

        $this->info("Testing activity creation for user: {$userId}");
        $this->info(str_repeat('=', 60));

        // Test upload activity
        $this->info("Creating upload activity...");
        $uploadId = $activityService->createActivity(
            $userId,
            'upload',
            'Đã tải lên sheet nhạc "Test Song"',
            [
                'product_id' => 'test_product_123',
                'amount' => '+0 xu'
            ]
        );

        if ($uploadId) {
            $this->info("✅ Upload activity created with ID: {$uploadId}");
        } else {
            $this->error("❌ Failed to create upload activity");
        }

        // Wait a moment for different timestamp
        sleep(1);

        // Test sale activity
        $this->info("Creating sale activity...");
        $saleId = $activityService->createActivity(
            $userId,
            'sale',
            'Đã bán "Test Song" cho khách hàng',
            [
                'product_id' => 'test_product_123',
                'amount' => '+25,000 xu',
                'related_user' => 'buyer_123',
                'transaction_id' => 'txn_test_123'
            ]
        );

        if ($saleId) {
            $this->info("✅ Sale activity created with ID: {$saleId}");
        } else {
            $this->error("❌ Failed to create sale activity");
        }

        $this->info(str_repeat('=', 60));
        $this->info("Now checking updated activities for this user...");

        // Get updated activities
        $activities = $activityService->getUserActivities($userId, 10);

        $this->info("Found " . count($activities) . " total activities:");
        foreach ($activities as $index => $activity) {
            $number = $index + 1;
            $type = $activity['type'] ?? 'unknown';
            $title = $activity['title'] ?? '';
            $description = $activity['description'] ?? '';
            $createdAt = $activity['created_at'] ?? 'N/A';

            // Calculate time difference
            $timeDiff = 'N/A';
            if ($createdAt !== 'N/A') {
                $timestamp = strtotime($createdAt);
                $diff = time() - $timestamp;
                if ($diff < 60) {
                    $timeDiff = $diff . " seconds ago";
                } else {
                    $timeDiff = round($diff / 60) . " minutes ago";
                }
            }

            $this->line("{$number}. [{$type}] {$title}");
            $this->line("    {$description}");
            $this->line("    {$timeDiff}");
            $this->line("    ---");
        }

        return 0;
    }
}
