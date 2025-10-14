<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Auth::class, function ($app) {
            $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
            return $factory->createAuth();
        });

        $this->app->singleton(Database::class, function ($app) {
            $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
            
            // Set the database URL explicitly using project ID from .env
            $projectId = env('FIREBASE_PROJECT_ID', 'kchip-8865d');
            $databaseUrl = "https://{$projectId}-default-rtdb.firebaseio.com/";
            $factory = $factory->withDatabaseUri($databaseUrl);
            
            return $factory->createDatabase();
        });

        $this->app->singleton(Firestore::class, function ($app) {
            $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
            return $factory->createFirestore();
        });

        $this->app->alias(Auth::class, 'firebase.auth');
        $this->app->alias(Database::class, 'firebase.database');
        $this->app->alias(Firestore::class, 'firebase.firestore');
    }

    public function boot()
    {
        //
    }
}
