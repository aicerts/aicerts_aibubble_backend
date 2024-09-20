<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        $customer = Auth::guard('customer')->user();

        if ($customer->status == false) {
            
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 401);
        }
        return $next($request);
    }
}
