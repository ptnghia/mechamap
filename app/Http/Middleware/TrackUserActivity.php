<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();
            
            // Cập nhật thời gian hoạt động cuối cùng
            $user->last_seen_at = now();
            
            // Lưu hoạt động hiện tại
            $routeName = $request->route() ? $request->route()->getName() : 'unknown';
            $user->last_activity = $routeName;
            
            $user->save();
        }

        return $response;
    }
}
