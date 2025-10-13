<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class DebugRecentActivities extends Command
{
    protected $signature = 'activity:debug-recent {limit=10}';
    protected $description = 'Debug recent activities to see detailed field structure';

    public function handle()
    {
        $limit = (int)$this->argument('limit');
        $firestore = new FirestoreSimple();

        $this->info("Getting {$limit} most recent activities...");
        $this->info(str_repeat('=', 80));

        try {
            $response = $firestore->listDocuments('activities', $limit);
            $documents = $response['documents'] ?? [];

            if (empty($documents)) {
                $this->error("No activities found!");
                return;
            }

            // Sort by creation time (based on document name timestamp)
            usort($documents, function ($a, $b) {
                $timeA = isset($a['createTime']) ? strtotime($a['createTime']) : 0;
                $timeB = isset($b['createTime']) ? strtotime($b['createTime']) : 0;
                return $timeB - $timeA; // Newest first
            });

            foreach (array_slice($documents, 0, $limit) as $index => $doc) {
                $docNumber = $index + 1;
                $id = basename($doc['name'] ?? 'no-name');
                $createTime = $doc['createTime'] ?? 'N/A';
                $updateTime = $doc['updateTime'] ?? 'N/A';

                $this->info("Document #{$docNumber} - ID: {$id}");
                $this->line("  Created: {$createTime}");
                $this->line("  Updated: {$updateTime}");

                $fields = $doc['fields'] ?? [];
                $this->line("  Fields:");

                foreach ($fields as $fieldName => $fieldData) {
                    $value = 'unknown';
                    $type = 'unknown';

                    if (isset($fieldData['stringValue'])) {
                        $value = $fieldData['stringValue'];
                        $type = 'string';
                    } elseif (isset($fieldData['integerValue'])) {
                        $value = $fieldData['integerValue'];
                        $type = 'integer';
                    } elseif (isset($fieldData['doubleValue'])) {
                        $value = $fieldData['doubleValue'];
                        $type = 'double';
                    } elseif (isset($fieldData['booleanValue'])) {
                        $value = $fieldData['booleanValue'] ? 'true' : 'false';
                        $type = 'boolean';
                    } elseif (isset($fieldData['timestampValue'])) {
                        $value = $fieldData['timestampValue'];
                        $type = 'timestamp';
                    } elseif (isset($fieldData['arrayValue'])) {
                        $value = json_encode($fieldData['arrayValue']);
                        $type = 'array';
                    } elseif (isset($fieldData['mapValue'])) {
                        $value = json_encode($fieldData['mapValue']);
                        $type = 'map';
                    }

                    $this->line("    {$fieldName} ({$type}): {$value}");
                }

                $this->line("  " . str_repeat('-', 70));
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }
}
