<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class ShowActivityStructure extends Command
{
    protected $signature = 'activity:show-structure {limit=5}';
    protected $description = 'Show the structure of existing activities';

    public function handle()
    {
        $limit = (int)$this->argument('limit');
        $firestore = new FirestoreSimple();

        $this->info("Showing structure of existing activities...");
        $this->info(str_repeat('=', 80));

        try {
            $response = $firestore->listDocuments('activities', $limit);
            $documents = $response['documents'] ?? [];

            if (empty($documents)) {
                $this->error("No activities found!");
                return;
            }

            foreach ($documents as $index => $doc) {
                $number = $index + 1;
                $id = basename($doc['name'] ?? 'no-name');
                $createTime = $doc['createTime'] ?? 'N/A';

                $this->info("Activity #{$number} - ID: {$id}");
                $this->line("Created: {$createTime}");

                $fields = $doc['fields'] ?? [];

                // Parse all fields
                $data = [];
                foreach ($fields as $key => $value) {
                    if (isset($value['stringValue'])) {
                        $data[$key] = $value['stringValue'];
                    } elseif (isset($value['integerValue'])) {
                        $data[$key] = (int)$value['integerValue'];
                    } elseif (isset($value['doubleValue'])) {
                        $data[$key] = (float)$value['doubleValue'];
                    } elseif (isset($value['timestampValue'])) {
                        $data[$key] = $value['timestampValue'];
                    } elseif (isset($value['booleanValue'])) {
                        $data[$key] = $value['booleanValue'];
                    } else {
                        $data[$key] = json_encode($value);
                    }
                }

                // Display formatted data
                foreach ($data as $key => $value) {
                    $this->line("  {$key}: {$value}");
                }

                $this->line(str_repeat('-', 70));
            }

            // Show unique fields across all documents
            $this->info("All unique fields found:");
            $allFields = [];
            foreach ($documents as $doc) {
                $fields = $doc['fields'] ?? [];
                $allFields = array_merge($allFields, array_keys($fields));
            }
            $uniqueFields = array_unique($allFields);
            $this->line("• " . implode(', ', $uniqueFields));
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }
}
