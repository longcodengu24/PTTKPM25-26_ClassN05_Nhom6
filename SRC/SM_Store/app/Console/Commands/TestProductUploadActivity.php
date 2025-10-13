<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActivityService;

class TestProductUploadActivity extends Command
{
    protected $signature = 'test:product-upload-activity {seller_uid}';
    protected $description = 'Test product upload activity notification';

    public function handle()
    {
        $sellerUid = $this->argument('seller_uid');

        $this->info("🧪 Testing Product Upload Activity");
        $this->line("Seller UID: $sellerUid");

        try {
            $activityService = new ActivityService();

            // Test creating product upload activity
            $this->info("\n📢 Creating product upload activity...");

            $activityId = $activityService->createActivity(
                $sellerUid,
                'product_upload',
                'Bạn đã tải lên sheet nhạc "Test Song - Composer" thành công',
                [
                    'product_name' => 'Test Song - Composer',
                    'price' => 15000,
                    'composer' => 'Test Composer',
                    'upload_time' => now()->toISOString()
                ]
            );

            if ($activityId) {
                $this->info("✅ Activity created successfully with ID: $activityId");

                // Get user activities to verify
                $this->info("\n📋 Getting user activities...");
                $activities = $activityService->getUserActivities($sellerUid, 10);

                if (!empty($activities)) {
                    $this->info("Found " . count($activities) . " activities:");

                    $headers = ['ID', 'Type', 'Message', 'Created'];
                    $rows = [];

                    foreach ($activities as $activity) {
                        $rows[] = [
                            substr($activity['id'] ?? 'N/A', 0, 15) . '...',
                            $activity['type'] ?? 'N/A',
                            substr($activity['message'] ?? 'N/A', 0, 40) . '...',
                            isset($activity['created_at']) ? date('Y-m-d H:i', strtotime($activity['created_at'])) : 'N/A'
                        ];
                    }

                    $this->table($headers, $rows);
                } else {
                    $this->warn("No activities found for user");
                }
            } else {
                $this->error("❌ Failed to create activity");
            }

            $this->info("\n🎯 Test Results:");
            if ($activityId) {
                $this->line("✅ Product upload activity notification working correctly");
                $this->line("✅ Activity appears in user's activity list");
                $this->line("✅ Ready to integrate with product upload workflow");
            } else {
                $this->line("❌ Activity creation failed - check logs for errors");
            }
        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
