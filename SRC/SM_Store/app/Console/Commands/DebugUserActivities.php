<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActivityService;

class DebugUserActivities extends Command
{
    protected $signature = 'activity:debug-user {user_uid}';
    protected $description = 'Debug activities for a specific user to check sorting';

    public function handle()
    {
        $userUid = $this->argument('user_uid');
        $activityService = new ActivityService();

        $this->info("Debugging activities for user: {$userUid}");
        $this->info(str_repeat('=', 80));

        try {
            $activities = $activityService->getUserActivities($userUid, 50);

            if (empty($activities)) {
                $this->error("No activities found for this user!");
                return;
            }

            $this->info("Found " . count($activities) . " activities");
            $this->info(str_repeat('-', 80));

            foreach ($activities as $index => $activity) {
                $number = $index + 1;
                $type = $activity['type'] ?? 'unknown';
                $message = $activity['description'] ?? $activity['message'] ?? 'No message';
                $title = $activity['title'] ?? '';
                $createdAt = $activity['created_at'] ?? 'N/A';
                $createdAtUnix = $activity['created_at_unix'] ?? 'N/A';

                // Calculate time difference
                $timeDiff = 'N/A';
                if ($createdAtUnix !== 'N/A') {
                    $now = time();
                    $diff = $now - (float)$createdAtUnix;
                    $timeDiff = $this->formatTimeDifference($diff);
                }

                $this->line("{$number}. [{$type}] {$title}");
                $this->line("    Description: {$message}");
                $this->line("    Created: {$createdAt}");
                $this->line("    Unix: {$createdAtUnix}");
                $this->line("    Time ago: {$timeDiff}");
                $this->line("    ---");
            }

            // Check if sorting is correct
            $this->info(str_repeat('=', 80));
            $this->info("Checking sort order (should be newest first):");

            $sortErrors = 0;
            for ($i = 0; $i < count($activities) - 1; $i++) {
                $current = (float)($activities[$i]['created_at_unix'] ?? 0);
                $next = (float)($activities[$i + 1]['created_at_unix'] ?? 0);

                if ($current < $next) {
                    $sortErrors++;
                    $this->error("❌ Sort error at position {$i}: {$current} < {$next}");
                }
            }

            if ($sortErrors === 0) {
                $this->info("✅ Activities are correctly sorted by time (newest first)");
            } else {
                $this->error("❌ Found {$sortErrors} sorting errors!");
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }

    private function formatTimeDifference($seconds)
    {
        if ($seconds < 60) {
            return round($seconds) . " seconds ago";
        } elseif ($seconds < 3600) {
            return round($seconds / 60) . " minutes ago";
        } elseif ($seconds < 86400) {
            return round($seconds / 3600) . " hours ago";
        } else {
            return round($seconds / 86400) . " days ago";
        }
    }
}
