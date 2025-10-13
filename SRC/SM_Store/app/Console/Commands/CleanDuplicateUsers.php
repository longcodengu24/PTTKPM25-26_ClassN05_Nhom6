<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class CleanDuplicateUsers extends Command
{
    protected $signature = 'users:clean-duplicates {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up duplicate user records in Firestore';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            $this->warn('⚠️  LIVE MODE - Changes will be made to the database');
            if (!$this->confirm('Are you sure you want to proceed?')) {
                $this->info('Cancelled.');
                return 0;
            }
        }

        $this->info('🧹 Cleaning duplicate users...');
        $this->info(str_repeat('=', 80));

        try {
            $firestore = new FirestoreSimple();
            $response = $firestore->listDocuments('users', 100);

            if (!isset($response['documents']) || empty($response['documents'])) {
                $this->warn('No users found in the collection.');
                return 0;
            }

            $users = [];
            $emailGroups = [];

            // Process all users and group by email
            foreach ($response['documents'] as $doc) {
                $docData = [];
                $docPath = $doc['name'] ?? '';
                $docData['id'] = basename($docPath);
                $docData['document_path'] = $docPath;

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
                $email = $docData['email'] ?? 'NO_EMAIL';

                if (!isset($emailGroups[$email])) {
                    $emailGroups[$email] = [];
                }
                $emailGroups[$email][] = $docData;
            }

            $this->info("📊 Total users: " . count($users));
            $duplicateCount = 0;
            $recordsToDelete = [];

            // Find and process duplicates
            foreach ($emailGroups as $email => $userList) {
                if (count($userList) > 1) {
                    $duplicateCount++;
                    $this->line("");
                    $this->warn("📧 Duplicate email: {$email} (found " . count($userList) . " times)");

                    // Sort by ID to get a consistent "keep" record
                    usort($userList, function ($a, $b) {
                        return strcmp($a['id'], $b['id']);
                    });

                    $keep = $userList[0]; // Keep the first one (oldest ID)
                    $toDelete = array_slice($userList, 1); // Delete the rest

                    $this->line("  ✅ Keeping: ID={$keep['id']}, Name=" . ($keep['name'] ?? 'N/A'));

                    foreach ($toDelete as $user) {
                        $this->line("  ❌ Will delete: ID={$user['id']}, Name=" . ($user['name'] ?? 'N/A'));
                        $recordsToDelete[] = $user;
                    }
                }
            }

            if ($duplicateCount === 0) {
                $this->info("✅ No duplicates found!");
                return 0;
            }

            $this->line("");
            $this->info("📋 Summary:");
            $this->line("  - Duplicate emails found: {$duplicateCount}");
            $this->line("  - Records to delete: " . count($recordsToDelete));

            if (!$dryRun) {
                $this->line("");
                $this->info("🗑️  Deleting duplicate records...");

                $deletedCount = 0;
                foreach ($recordsToDelete as $user) {
                    try {
                        // Delete from Firestore using document path
                        $result = $firestore->deleteDocument('users', $user['id']);

                        if ($result) {
                            $this->line("  ✅ Deleted: {$user['id']} ({$user['email']})");
                            $deletedCount++;
                        } else {
                            $this->error("  ❌ Failed to delete: {$user['id']}");
                        }
                    } catch (\Exception $e) {
                        $this->error("  ❌ Error deleting {$user['id']}: " . $e->getMessage());
                    }
                }

                $this->line("");
                $this->info("🎉 Cleanup completed!");
                $this->line("  - Records deleted: {$deletedCount}");
                $this->line("  - Records kept: " . ($duplicateCount));
            } else {
                $this->line("");
                $this->info("🔍 DRY RUN completed - no changes made");
                $this->line("Run without --dry-run to actually delete duplicates");
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
