<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class LoadUserData
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $uid = session('firebase_uid');

        if ($uid) {
            try {
                // Lấy thông tin user từ Firebase
                $user = $this->auth->getUser($uid);

                // Cập nhật session với thông tin mới nhất từ Firebase
                $userData = [
                    'name' => $user->displayName ?? session('name', ''),
                    'email' => $user->email ?? session('email', ''),
                    'avatar' => $user->photoUrl ?? '/img/default-avatar.png'
                ];

                // Cập nhật session
                session($userData);

                // Share data với tất cả views
                View::share('currentUser', $userData);

                Log::info('User data loaded in middleware', [
                    'uid' => $uid,
                    'name' => $userData['name'],
                    'avatar' => $userData['avatar'],
                    'firebase_photoUrl' => $user->photoUrl
                ]);
            } catch (\Exception $e) {
                Log::error('Error loading user data in middleware: ' . $e->getMessage());

                // Fallback to session data
                $userData = [
                    'name' => session('name', ''),
                    'email' => session('email', ''),
                    'avatar' => session('avatar', '/img/default-avatar.png')
                ];
                View::share('currentUser', $userData);
            }
        }

        return $next($request);
    }
}
