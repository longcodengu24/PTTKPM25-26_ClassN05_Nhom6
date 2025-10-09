<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackfillUsersToFirestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-users-to-firestore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lấy danh sách user từ Firebase Auth
        $auth = app(\Kreait\Firebase\Contract\Auth::class);
        $firestore = new \App\Services\FirestoreSimple();

        $users = $auth->listUsers();
        $count = 0;
        foreach ($users as $user) {
            $uid = $user->uid;
            // Luôn ghi đè document với đúng UID
            $firestore->updateDocument('users', $uid, [
                'name'   => $user->displayName ?? '',
                'email'  => $user->email ?? '',
                'avatar' => $user->photoUrl ?? '',
                'coins'  => $userDoc['coins'] ?? 0 // Giữ nguyên coins nếu đã có, hoặc 0 nếu chưa
            ]);
            $count++;
            $this->info("Synced Firestore user: $uid");
        }
        $this->info("Backfill completed! Created $count new user documents.");
    }
}
