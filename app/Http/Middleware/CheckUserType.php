<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserType
{
    public function handle(Request $request, Closure $next, ...$types)
    {
        $user = $request->user();
        
        if (!$user || !in_array($user->type, $types)) {
            return response()->json([
                'message' => 'غير مصرح لك بالوصول إلى هذا المورد'
            ], 403);
        }
        
        return $next($request);
    }
}