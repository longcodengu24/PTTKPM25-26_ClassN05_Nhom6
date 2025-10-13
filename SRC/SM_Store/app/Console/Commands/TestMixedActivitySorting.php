<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActivityService;

class TestMixedActivitySorting extends Command
{
    protected $signature = 'activity:test-mixed-sorting {user_id}';
    protected $description = 'Test sorting with mixed activity types (upload, sale, purchase)';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $activityService = new ActivityService();

        $this->info("Testing mixed activity sorting for user: {$userId}");
        $this->info(str_repeat('=', 70));

        // Create activities with different types and small delays
        $activities = [
            ['type' => 'sale', 'message' => 'Đã bán "Song A" cho khách hàng', 'delay' => 0],
            ['type' => 'upload', 'message' => 'Đã tải lên "Song B"', 'delay' => 1],
            ['type' => 'sale', 'message' => 'Đã bán "Song C" cho khách hàng', 'delay' => 2],
            ['type' => 'upload', 'message' => 'Đã tải lên "Song D"', 'delay' => 3],
            ['type' => 'purchase', 'message' => 'Đã mua "Song E"', 'delay' => 4],
        ];

        $this->info("Creating mixed activities with timestamps...");

        $createdIds = [];
        foreach ($activities as $index => $activity) {
            if ($activity['delay'] > 0) {
                sleep($activity['delay']);
            }

            $activityId = $activityService->createActivity(
                $userId,
                $activity['type'],
                $activity['message'],
                [
                    'test_order' => $index + 1,
                    'amount' => $activity['type'] === 'sale' ? '+25,000 xu' : ($activity['type'] === 'purchase' ? '-15,000 xu' : '+0 xu')
                ]
            );

            if ($activityId) {
                $createdIds[] = $activityId;
                $this->line("✅ Created {$activity['type']} activity: {$activity['message']}");
            } else {
                $this->error("❌ Failed to create {$activity['type']} activity");
            }
        }

        $this->info(str_repeat('-', 70));
        $this->info("Retrieving and checking sort order...");

        // Get all activities for this user
        $userActivities = $activityService->getUserActivities($userId, 20);

        if (empty($userActivities)) {
            $this->error("No activities found!");
            return;
        }

        $this->info("Found " . count($userActivities) . " activities:");
        $this->info(str_repeat('-', 70));

        $previousTimestamp = null;
        $sortingErrors = 0;

        foreach ($userActivities as $index => $activity) {
            $number = $index + 1;
            $type = $activity['type'] ?? 'unknown';
            $title = $activity['title'] ?? '';
            $description = $activity['description'] ?? '';
            $createdAt = $activity['created_at'] ?? 'N/A';
            $testOrder = $activity['test_order'] ?? 'N/A';

            // Check if this is one of our test activities
            $isTestActivity = in_array($activity['id'] ?? '', $createdIds);
            $marker = $isTestActivity ? "🆕 " : "   ";

            // Calculate timestamp for comparison
            $currentTimestamp = strtotime($createdAt);

            // Check chronological order
            if ($previousTimestamp !== null && $currentTimestamp > $previousTimestamp) {
                $sortingErrors++;
                $this->error("❌ SORT ERROR at position {$number}");
            }

            // Calculate time ago
            $timeAgo = 'N/A';
            if ($currentTimestamp) {
                $diff = time() - $currentTimestamp;
                if ($diff < 60) {
                    $timeAgo = $diff . "s ago";
                } elseif ($diff < 3600) {
                    $timeAgo = round($diff / 60) . "m ago";
                } else {
                    $timeAgo = round($diff / 3600) . "h ago";
                }
            }

            $this->line("{$marker}{$number}. [{$type}] {$title}");
            $this->line("    {$description}");
            $this->line("    Created: {$createdAt} ({$timeAgo})");
            if ($testOrder !== 'N/A') {
                $this->line("    Test Order: {$testOrder}");
            }
            $this->line("    " . str_repeat('-', 60));

            $previousTimestamp = $currentTimestamp;
        }

        $this->info(str_repeat('=', 70));
        if ($sortingErrors === 0) {
            $this->info("✅ All activities are correctly sorted by time (newest first)");
        } else {
            $this->error("❌ Found {$sortingErrors} sorting errors!");
        }

        // Check if mixed types are properly interleaved
        $testActivities = array_filter($userActivities, function ($activity) use ($createdIds) {
            return in_array($activity['id'] ?? '', $createdIds);
        });

        if (count($testActivities) >= 2) {
            $this->info("🔍 Checking type mixing...");
            $types = array_map(function ($activity) {
                return $activity['type'] ?? 'unknown';
            }, array_slice($testActivities, 0, 5));

            $uniqueTypes = array_unique($types);
            if (count($uniqueTypes) > 1) {
                $this->info("✅ Different activity types are properly mixed: " . implode(', ', $types));
            } else {
                $this->error("❌ Activities are grouped by type instead of chronological order");
            }
        }

        return 0;
    }
}
