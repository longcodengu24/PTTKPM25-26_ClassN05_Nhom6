<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class CheckUsersActivity extends Command
{
    protected $signature = 'debug:users-activity';
    protected $description = 'Check users_activity collection structure';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $firestore = app(FirestoreSimple::class);

        $this->info('Checking users_activity collection...');

        try {
            $userActivities = $firestore->listDocuments('users_activity', 5);

            if (isset($userActivities['documents'])) {
                foreach ($userActivities['documents'] as $doc) {
                    $this->info('Document ID: ' . basename($doc['name']));
                    $this->info('Data: ' . json_encode($doc['fields'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $this->info('---');
                }
            } else {
                $this->info('No documents found in users_activity');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        $this->info('Checking activities collection...');

        try {
            $activities = $firestore->listDocuments('activities', 5);

            if (isset($activities['documents'])) {
                foreach ($activities['documents'] as $doc) {
                    $this->info('Document ID: ' . basename($doc['name']));
                    $this->info('Data: ' . json_encode($doc['fields'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $this->info('---');
                }
            } else {
                $this->info('No documents found in activities');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
