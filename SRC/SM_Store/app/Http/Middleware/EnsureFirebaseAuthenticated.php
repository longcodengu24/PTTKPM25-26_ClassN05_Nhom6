<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureFirebaseAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('firebase_uid')) {
            // Nếu là AJAX request, trả về JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để tiếp tục',
                    'redirect' => route('login')
                ], 401);
            }
            
            // Nếu là request thường, redirect về login
            return redirect()->route('login')->withErrors(['error' => 'Vui lòng đăng nhập trước.']);
        }
        return $next($request);
    }
}
