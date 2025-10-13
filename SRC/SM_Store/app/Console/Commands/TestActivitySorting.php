<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActivityService;

class TestActivitySorting extends Command
{
    protected $signature = 'activity:test-sorting {user_uid}';
    protected $description = 'Test activity sorting by creating multiple activities and checking order';

    public function handle()
    {
        $userUid = $this->argument('user_uid');
        $activityService = new ActivityService();

        $this->info("Tạo các activity test cho user: {$userUid}");

        // Tạo nhiều activity với delay nhỏ để test sorting
        $activities = [
            ['type' => 'upload', 'message' => 'Test upload activity 1'],
            ['type' => 'purchase', 'message' => 'Test purchase activity 1'],
            ['type' => 'sale', 'message' => 'Test sale activity 1'],
            ['type' => 'upload', 'message' => 'Test upload activity 2'],
            ['type' => 'purchase', 'message' => 'Test purchase activity 2'],
        ];

        foreach ($activities as $index => $activity) {
            $activityNumber = $index + 1;
            $this->info("Tạo activity {$activityNumber}: {$activity['message']}");

            $activityService->createActivity(
                $userUid,
                $activity['type'],
                $activity['message'],
                ['test_index' => $activityNumber]
            );

            // Delay nhỏ để đảm bảo timestamp khác nhau
            usleep(10000); // 10ms
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("Lấy và hiển thị activities theo thứ tự thời gian:");
        $this->info(str_repeat('=', 50));

        // Lấy activities và hiển thị
        $userActivities = $activityService->getUserActivities($userUid);

        if (empty($userActivities)) {
            $this->error("Không tìm thấy activities cho user này!");
            return;
        }

        foreach ($userActivities as $index => $activity) {
            $timestamp = isset($activity['created_at_unix'])
                ? date('H:i:s.u', $activity['created_at_unix'])
                : (isset($activity['created_at']) ? $activity['created_at'] : 'N/A');

            $unixTime = isset($activity['created_at_unix']) ? $activity['created_at_unix'] : 'N/A';
            $activityNumber = $index + 1;

            $this->line(sprintf(
                "%d. [%s] %s (%s) - Unix: %s",
                $activityNumber,
                $activity['type'] ?? 'unknown',
                $activity['message'] ?? 'No message',
                $timestamp,
                $unixTime
            ));
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("Kiểm tra thứ tự thời gian:");

        $isCorrectOrder = true;
        for ($i = 0; $i < count($userActivities) - 1; $i++) {
            $current = $userActivities[$i]['created_at_unix'] ?? 0;
            $next = $userActivities[$i + 1]['created_at_unix'] ?? 0;

            if ($current < $next) {
                $this->error("❌ Sai thứ tự tại vị trí {$i}: {$current} < {$next}");
                $isCorrectOrder = false;
            }
        }

        if ($isCorrectOrder) {
            $this->info("✅ Activities được sắp xếp đúng theo thứ tự thời gian (mới nhất trước)");
        }

        return 0;
    }
}
