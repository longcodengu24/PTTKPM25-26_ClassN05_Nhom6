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
        // ✅ Kích hoạt API routes
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Đăng ký alias để dùng trong routes: ['firebase.auth', 'role:admin']
        $middleware->alias([
            'firebase.auth' => EnsureFirebaseAuthenticated::class,
            'role'          => RoleMiddleware::class,
            'load.user'     => LoadUserData::class,
        ]);

        // ✅ Loại bỏ các route API khỏi kiểm tra CSRF
        $middleware->validateCsrfTokens(except: [
            'api/sepay/webhook',
            'api/*'
        ]);

        // (Tuỳ chọn) nếu muốn gắn middleware vào group 'web':
        // $middleware->appendToGroup('web', EnsureFirebaseAuthenticated::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
