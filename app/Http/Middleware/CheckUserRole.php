<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeUser = auth()->user();

        if ($activeUser->role === 'admin' || $activeUser->role === 'superadmin') {
            return $next($request);
        } else {
            return response()->json([
                "msg" => "You do not have permission to access this route.",
                "status" => false,
                "data" => []
            ]);
        }
    }
}
