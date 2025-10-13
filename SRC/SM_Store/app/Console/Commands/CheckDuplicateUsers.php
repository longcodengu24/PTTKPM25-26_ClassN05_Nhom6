<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class CheckDuplicateUsers extends Command
{
    protected $signature = 'users:check-duplicates';
    protected $description = 'Check for duplicate email addresses in users collection';

    public function handle()
    {
        $this->info('🔍 Checking for duplicate users in the users collection...');
        $this->info(str_repeat('=', 80));

        try {
            $firestore = new FirestoreSimple();
            $response = $firestore->listDocuments('users', 100); // Get more documents

            if (!isset($response['documents']) || empty($response['documents'])) {
                $this->warn('No users found in the collection.');
                return 0;
            }

            $users = [];
            $emailCount = [];
            $duplicates = [];

            // Process all users
            foreach ($response['documents'] as $doc) {
                $docData = [];
                $docPath = $doc['name'] ?? '';
                $docData['id'] = basename($docPath);

                // Extract fields
                if (isset($doc['fields'])) {
                    foreach ($doc['fields'] as $field => $value) {
                        if (isset($value['stringValue'])) {
                            $docData[$field] = $value['stringValue'];
                        } elseif (isset($value['integerValue'])) {
                            $docData[$field] = (int) $value['integerValue'];
                        } elseif (isset($value['booleanValue'])) {
                            $docData[$field] = $value['booleanValue'];
                        } elseif (isset($value['timestampValue'])) {
                            $docData[$field] = $value['timestampValue'];
                        } elseif (isset($value['doubleValue'])) {
                            $docData[$field] = (float) $value['doubleValue'];
                        }
                    }
                }

                $users[] = $docData;

                // Count emails
                $email = $docData['email'] ?? 'NO_EMAIL';
                if (!isset($emailCount[$email])) {
                    $emailCount[$email] = [];
                }
                $emailCount[$email][] = $docData;
            }

            $this->info("📊 Total users found: " . count($users));
            $this->line("");

            // Find duplicates
            foreach ($emailCount as $email => $userList) {
                if (count($userList) > 1) {
                    $duplicates[$email] = $userList;
                }
            }

            if (empty($duplicates)) {
                $this->info("✅ No duplicate emails found!");
                return 0;
            }

            $this->error("❌ Found " . count($duplicates) . " duplicate email(s):");
            $this->line("");

            foreach ($duplicates as $email => $userList) {
                $this->line("📧 Email: {$email} (found {" . count($userList) . "} times)");

                foreach ($userList as $index => $user) {
                    $this->line("  🔹 Record " . ($index + 1) . ":");
                    $this->line("     ID: " . ($user['id'] ?? 'N/A'));
                    $this->line("     Name: " . ($user['name'] ?? 'N/A'));
                    $this->line("     Role: " . ($user['role'] ?? 'N/A'));
                    $this->line("     Created: " . ($user['created_at'] ?? 'N/A'));
                    $this->line("     UID: " . ($user['uid'] ?? 'N/A'));

                    // Show all fields for debugging
                    $this->line("     All fields: " . implode(', ', array_keys($user)));
                    $this->line("");
                }
                $this->line(str_repeat('-', 60));
            }

            // Analysis and recommendations
            $this->line("");
            $this->info("🔍 Analysis:");

            foreach ($duplicates as $email => $userList) {
                $this->line("📧 {$email}:");

                // Check for differences
                $roles = array_unique(array_column($userList, 'role'));
                $uids = array_unique(array_column($userList, 'uid'));
                $names = array_unique(array_column($userList, 'name'));

                $this->line("  - Roles: " . implode(', ', $roles));
                $this->line("  - UIDs: " . implode(', ', $uids));
                $this->line("  - Names: " . implode(', ', $names));

                if (count($uids) > 1) {
                    $this->warn("  ⚠️  Different UIDs - likely separate Firebase Auth users");
                } else {
                    $this->warn("  ⚠️  Same UID - possible duplicate document creation");
                }
                $this->line("");
            }

            $this->line("");
            $this->info("💡 Recommended Actions:");
            $this->line("1. Check user registration/creation logic");
            $this->line("2. Implement email uniqueness validation");
            $this->line("3. Consider merging or removing duplicate records");
            $this->line("4. Add unique constraints if possible");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
