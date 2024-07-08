<?php

namespace oval\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetUniqueSessionVideoId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $videoId = $request->header('v-session-id');
        if ($videoId) {
            session(['v-session-id' => $videoId]);
        }

        return $next($request);
    }
}
