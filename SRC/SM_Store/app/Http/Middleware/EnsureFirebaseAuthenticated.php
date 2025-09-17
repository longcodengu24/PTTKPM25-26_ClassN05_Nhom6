<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureFirebaseAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('firebase_uid')) {
            return redirect()->route('login')->withErrors(['error' => 'Vui lòng đăng nhập trước.']);
        }
        return $next($request);
    }
}
