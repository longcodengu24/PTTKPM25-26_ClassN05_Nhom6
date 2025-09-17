<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Contract\Storage as FirebaseStorage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Factory::class, function () {
            $config = config('services.firebase');

            $factory = new Factory();

            if (!empty($config['credentials'])) {
                // Accept either JSON string or path to JSON
                $credentials = $config['credentials'];
                if (is_string($credentials) && str_starts_with(trim($credentials), '{')) {
                    $factory = $factory->withServiceAccount(json_decode($credentials, true));
                } else {
                    $factory = $factory->withServiceAccount($credentials);
                }
            }

            if (!empty($config['project_id'])) {
                $factory = $factory->withProjectId($config['project_id']);
            }

            if (!empty($config['database_url'])) {
                $factory = $factory->withDatabaseUri($config['database_url']);
            }

            if (!empty($config['storage_bucket'])) {
                $factory = $factory->withDefaultStorageBucket($config['storage_bucket']);
            }

            return $factory;
        });

        $this->app->bind(FirebaseAuth::class, function ($app) {
            return $app->make(Factory::class)->createAuth();
        });

        $this->app->bind(FirebaseStorage::class, function ($app) {
            return $app->make(Factory::class)->createStorage();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
