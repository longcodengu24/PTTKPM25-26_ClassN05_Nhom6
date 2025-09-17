<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $current = session('role', 'user');

        if ($current !== $role) {
            abort(403, 'Bạn không có quyền truy cập nội dung này.');
        }
        return $next($request);
    }
}
