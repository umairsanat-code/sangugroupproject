<?php

namespace Sanat\ScrapeGuard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sanat\ScrapeGuard\Services\ScrapeGuardManager;

class DetectScraping
{
    public function __construct(private ScrapeGuardManager $guard) {}

    public function handle(Request $request, Closure $next)
    {
        if (!config('scrapeguard.enabled', true)) return $next($request);

        if ($this->guard->isBlocked($request->ip())) {
            return response()->json([
                'message' => 'Request blocked by ScrapeGuard.',
                'ip' => $request->ip(),
            ], (int) config('scrapeguard.protection.blocked_response_status', 403));
        }

        $analysis = $this->guard->analyze($request);
        $response = $next($request);
        $this->guard->record($request, $analysis);
        return $response;
    }
}
