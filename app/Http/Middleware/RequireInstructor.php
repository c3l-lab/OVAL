<?php

namespace oval\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireInstructor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = \Auth::user();
        if (!$user->isAnInstructor()) {
            return response()->view('pages.not-instructor', [], 401);
        }
        return $next($request);
    }
}
