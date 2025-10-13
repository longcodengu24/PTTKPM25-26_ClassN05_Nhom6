<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class CheckUsersData extends Command
{
    protected $signature = 'users:check';
    protected $description = 'Check users data integrity after coin updates';

    public function handle()
    {
        $firestoreService = new FirestoreSimple();

        $this->info('=== KIỂM TRA DỮ LIỆU USERS ===');

        try {
            // Lấy danh sách tất cả users
            $usersResponse = $firestoreService->listDocuments('users');

            if (isset($usersResponse['documents'])) {
                foreach ($usersResponse['documents'] as $doc) {
                    $docPath = $doc['name'] ?? '';
                    $docId = basename($docPath);

                    // Extract fields
                    $userData = [];
                    if (isset($doc['fields'])) {
                        foreach ($doc['fields'] as $field => $value) {
                            if (isset($value['stringValue'])) {
                                $userData[$field] = $value['stringValue'];
                            } elseif (isset($value['integerValue'])) {
                                $userData[$field] = (int)$value['integerValue'];
                            } elseif (isset($value['booleanValue'])) {
                                $userData[$field] = $value['booleanValue'];
                            } elseif (isset($value['timestampValue'])) {
                                $userData[$field] = $value['timestampValue'];
                            }
                        }
                    }

                    $this->line("USER ID: " . $docId);
                    $this->line("Data: " . json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $this->line("Fields count: " . count($userData));
                    $this->line("Has name: " . (isset($userData['name']) ? 'YES' : 'NO'));
                    $this->line("Has email: " . (isset($userData['email']) ? 'YES' : 'NO'));
                    $this->line("Has coins: " . (isset($userData['coins']) ? 'YES (' . $userData['coins'] . ')' : 'NO'));
                    $this->line("Has role: " . (isset($userData['role']) ? 'YES (' . $userData['role'] . ')' : 'NO'));
                    $this->line("----------------------------------------");
                }
            } else {
                $this->error("No users found or error in response");
                $this->line("Response: " . json_encode($usersResponse, JSON_PRETTY_PRINT));
            }
        } catch (\Exception $e) {
            $this->error("ERROR: " . $e->getMessage());
        }
    }
}
