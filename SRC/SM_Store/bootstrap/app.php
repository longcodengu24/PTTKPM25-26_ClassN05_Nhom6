<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


use App\Http\Middleware\EnsureFirebaseAuthenticated;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\LoadUserData;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
<<<<<<< HEAD
        // Enable API routes
        api: __DIR__.'/../routes/api.php',
=======
        // nếu có api routes thì mở dòng dưới
        // api: __DIR__.'/../routes/api.php',
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Đăng ký alias để dùng trong routes: ['firebase.auth', 'role:admin']
        $middleware->alias([
            'firebase.auth' => EnsureFirebaseAuthenticated::class,
            'role'          => RoleMiddleware::class,
            'load.user'     => LoadUserData::class,
        ]);
<<<<<<< HEAD
        
        // Exclude webhook from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'api/sepay/webhook',
            'api/*'  // Tất cả API routes không cần CSRF
        ]);
=======
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836

        // (tuỳ chọn) nếu muốn gắn middleware vào group 'web' mặc định:
        // $middleware->appendToGroup('web', EnsureFirebaseAuthenticated::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
