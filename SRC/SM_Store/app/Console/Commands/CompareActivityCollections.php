<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class CompareActivityCollections extends Command
{
    protected $signature = 'activity:compare-collections';
    protected $description = 'Compare activities and users_activity collections';

    public function handle()
    {
        $firestore = new FirestoreSimple();

        $this->info("Comparing 'activities' and 'users_activity' collections...");
        $this->info(str_repeat('=', 80));

        try {
            // Check activities collection
            $this->info("📋 ACTIVITIES Collection:");
            $activitiesResponse = $firestore->listDocuments('activities', 10);
            $activitiesDocuments = $activitiesResponse['documents'] ?? [];
            $this->line("  Total documents: " . count($activitiesDocuments));

            if (!empty($activitiesDocuments)) {
                $doc = $activitiesDocuments[0];
                $fields = $doc['fields'] ?? [];
                $this->line("  Sample fields: " . implode(', ', array_keys($fields)));
            }

            $this->line("");

            // Check users_activity collection
            $this->info("📋 USERS_ACTIVITY Collection:");
            $usersActivityResponse = $firestore->listDocuments('users_activity', 10);
            $usersActivityDocuments = $usersActivityResponse['documents'] ?? [];
            $this->line("  Total documents: " . count($usersActivityDocuments));

            if (!empty($usersActivityDocuments)) {
                $doc = $usersActivityDocuments[0];
                $fields = $doc['fields'] ?? [];
                $this->line("  Sample fields: " . implode(', ', array_keys($fields)));

                // Show sample data
                $this->line("  Sample data:");
                foreach ($fields as $key => $value) {
                    if (isset($value['stringValue'])) {
                        $val = $value['stringValue'];
                    } elseif (isset($value['integerValue'])) {
                        $val = $value['integerValue'];
                    } elseif (isset($value['doubleValue'])) {
                        $val = $value['doubleValue'];
                    } elseif (isset($value['timestampValue'])) {
                        $val = $value['timestampValue'];
                    } else {
                        $val = json_encode($value);
                    }
                    $this->line("    {$key}: {$val}");
                }
            }

            $this->line("");
            $this->info("🔍 Recommendations:");

            if (count($usersActivityDocuments) > count($activitiesDocuments)) {
                $this->line("✅ users_activity has more data - should use this collection");
            } elseif (count($activitiesDocuments) > count($usersActivityDocuments)) {
                $this->line("✅ activities has more data - should use this collection");
            } else {
                $this->line("⚠️  Both collections have similar amounts of data - need to check which is used by UI");
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }
}
