<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class DebugActivities extends Command
{
    protected $signature = 'activity:debug';
    protected $description = 'Debug activities collection to see what data exists';

    public function handle()
    {
        $firestore = new FirestoreSimple();

        $this->info("Đang lấy tất cả documents trong collection 'activities'...");

        try {
            // Try to get all documents
            $response = $firestore->listDocuments('activities', 50);
            $documents = $response['documents'] ?? [];

            $this->info("Tìm thấy " . count($documents) . " documents");

            if (empty($documents)) {
                $this->error("Không có document nào trong collection 'activities'!");
                return;
            }

            foreach ($documents as $index => $doc) {
                $id = basename($doc['name'] ?? 'no-name');
                $fields = $doc['fields'] ?? [];

                $docNumber = $index + 1;
                $this->info("Document #{$docNumber} - ID: {$id}");

                // Parse fields
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
                    } else {
                        $data[$key] = json_encode($value);
                    }
                }

                $this->line("  - user_uid: " . ($data['user_uid'] ?? 'missing'));
                $this->line("  - type: " . ($data['type'] ?? 'missing'));
                $this->line("  - message: " . ($data['message'] ?? 'missing'));
                $this->line("  - created_at: " . ($data['created_at'] ?? 'missing'));
                $this->line("  - created_at_unix: " . ($data['created_at_unix'] ?? 'missing'));
                $this->line("  ---");
            }
        } catch (\Exception $e) {
            $this->error("Lỗi khi lấy activities: " . $e->getMessage());
        }

        return 0;
    }
}
