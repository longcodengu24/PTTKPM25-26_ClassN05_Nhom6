<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActivityService;

class TestActivityCreation extends Command
{
    protected $signature = 'activity:test-create';
    protected $description = 'Test basic activity creation';

    public function handle()
    {
        $this->info("Đang test tạo activity...");

        $activityService = new ActivityService();

        try {
            $result = $activityService->createActivity(
                'test_user_999',
                'test',
                'This is a test activity message',
                ['test_data' => 'test_value']
            );

            if ($result) {
                $this->info("✅ Activity được tạo thành công với ID: " . $result);
            } else {
                $this->error("❌ Không thể tạo activity");
            }
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " Line: " . $e->getLine());
        }

        return 0;
    }
}
