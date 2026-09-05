<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        SiteVisit::create([
            'user_id' => $request->user()?->id,
            'session_id' => $request->session()->getId(),
            'ip_hash' => $this->hashIp($request->ip()),
            'path' => '/'.ltrim($request->path(), '/'),
            'route_name' => $request->route()?->getName(),
            'referrer' => Str::limit((string) $request->headers->get('referer'), 500, ''),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            'visited_at' => now(),
        ]);

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! Schema::hasTable('site_visits')) {
            return false;
        }

        if (! $request->isMethod('GET') || ! $response->isSuccessful()) {
            return false;
        }

        if ($request->expectsJson() || $request->is('admin*', 'api*', 'up', 'storage*', 'build*')) {
            return false;
        }

        return ! str_contains($request->path(), '.');
    }

    private function hashIp(?string $ip): ?string
    {
        if (! $ip) {
            return null;
        }

        return hash_hmac('sha256', $ip, (string) config('app.key'));
    }
}
