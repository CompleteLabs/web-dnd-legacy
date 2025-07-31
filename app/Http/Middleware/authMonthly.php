<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class authMonthly
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
        $user = auth()->user();
        if ($user->mn || $user->mr) {
            return $next($request);
        }
        return back();
    }
}
