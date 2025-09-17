<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Auth::class, function ($app) {
            $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
            return $factory->createAuth();
        });

       
        $this->app->alias(Auth::class, 'firebase.auth');
    }

    public function boot()
    {
        //
    }
}
