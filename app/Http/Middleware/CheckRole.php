<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'غير مصرح - يجب تسجيل الدخول',
                    'timestamp' => now()->toISOString(),
                ]
            ], 401);
        }

        if ($request->user()->role !== $role) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'غير مصرح - لا تملك الصلاحيات المطلوبة',
                    'timestamp' => now()->toISOString(),
                ]
            ], 403);
        }

        return $next($request);
    }
}
