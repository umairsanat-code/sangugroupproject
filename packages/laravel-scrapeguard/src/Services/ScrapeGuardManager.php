<?php

namespace Sanat\ScrapeGuard\Services;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScrapeGuardManager
{
    public function __construct(private Application $app) {}

    public function analyze(Request $request): array
    {
        $ip = $request->ip() ?: 'unknown';
        $ua = (string) $request->userAgent();
        $score = 0;
        $reasons = [];
        $table = $this->table();
        $rpm = DB::table($table)->where('ip', $ip)->where('created_at', '>=', now()->subMinute())->count();

        if ($rpm >= (int) config('scrapeguard.thresholds.requests_per_minute', 120)) {
            $score += 50;
            $reasons[] = 'high_request_rate';
        } elseif ($rpm >= 60) {
            $score += 20;
            $reasons[] = 'elevated_request_rate';
        }

        if ($this->automation($ua)) {
            $score += 25;
            $reasons[] = 'automation_user_agent';
        }

        $sequential = $this->sequentialCount($ip);
        if ($sequential >= (int) config('scrapeguard.thresholds.sequential_paths', 8)) {
            $score += 25;
            $reasons[] = 'sequential_crawling';
        }

        if ($request->routeIs('scrapeguard.honeypot')) {
            $score += 40;
            $reasons[] = 'honeypot_triggered';
        }

        return [
            'score' => min(100, $score),
            'reasons' => array_values(array_unique($reasons)),
            'rpm' => $rpm,
            'sequential' => $sequential,
            'ip' => $ip,
            'path' => '/' . ltrim($request->path(), '/'),
            'user_agent' => $ua,
        ];
    }

    public function record(Request $request, array $analysis): void
    {
        if (!config('scrapeguard.enabled', true) || $this->excluded($request)) {
            return;
        }

        DB::table($this->table())->insert([
            'ip' => $request->ip(),
            'method' => $request->method(),
            'path' => '/' . ltrim($request->path(), '/'),
            'query' => config('scrapeguard.capture.query', true) ? json_encode($request->query()) : null,
            'body' => config('scrapeguard.capture.body', false) ? json_encode($this->redact($request->all())) : null,
            'headers' => json_encode($this->headers($request)),
            'user_agent' => $request->userAgent(),
            'risk_score' => $analysis['score'],
            'reasons' => json_encode($analysis['reasons']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($analysis['score'] >= (int) config('scrapeguard.thresholds.risk_to_notify', 70)) {
            Log::warning('ScrapeGuard suspicious activity', $analysis);
        }

        if (config('scrapeguard.protection.auto_block', false)
            && $analysis['score'] >= (int) config('scrapeguard.thresholds.risk_to_block', 95)) {
            $this->block($analysis['ip'], 'Automatic ScrapeGuard protection', (int) config('scrapeguard.protection.block_minutes', 60));
        }
    }

    public function block(string $ip, string $reason = 'Blocked by ScrapeGuard', ?int $minutes = null): void
    {
        $until = $minutes ? now()->addMinutes($minutes) : null;
        DB::table($this->blockedTable())->updateOrInsert(
            ['ip' => $ip],
            [
                'reason' => $reason,
                'blocked_until' => $until,
                'active' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function isBlocked(string $ip): bool
    {
        $row = DB::table($this->blockedTable())->where('ip', $ip)->where('active', true)->first();
        if (!$row) return false;
        if ($row->blocked_until && now()->greaterThan($row->blocked_until)) {
            DB::table($this->blockedTable())->where('id', $row->id)->update(['active' => false, 'updated_at' => now()]);
            return false;
        }
        return true;
    }

    public function score(Request $request): int
    {
        return $this->analyze($request)['score'];
    }

    private function sequentialCount(string $ip): int
    {
        $paths = DB::table($this->table())->where('ip', $ip)->where('created_at', '>=', now()->subMinutes(5))->orderByDesc('id')->limit(30)->pluck('path')->values()->all();
        if (count($paths) < 3) return 0;
        $numeric = [];
        foreach ($paths as $path) {
            if (preg_match('#/(?:[^/]+/)*[^/]*?(\d+)$#', $path, $m)) $numeric[] = (int) $m[1];
        }
        if (count($numeric) < 3) return 0;
        $count = 1;
        $best = 1;
        for ($i = 1; $i < count($numeric); $i++) {
            if (abs($numeric[$i - 1] - $numeric[$i]) === 1) $count++; else $count = 1;
            $best = max($best, $count);
        }
        return $best;
    }

    private function automation(string $ua): bool
    {
        return $ua === '' || (bool) preg_match('/python-requests|scrapy|curl|wget|httpclient|headless|phantom|selenium|playwright|puppeteer|go-http-client/i', $ua);
    }

    private function excluded(Request $request): bool
    {
        if (in_array($request->ip(), config('scrapeguard.exclude.ips', []), true)) return true;
        foreach (config('scrapeguard.exclude.paths', []) as $pattern) if ($request->is($pattern)) return true;
        foreach (config('scrapeguard.exclude.user_agents', []) as $ua) if ($request->isMethod('GET') && str_contains(strtolower((string)$request->userAgent()), strtolower($ua))) return true;
        return false;
    }

    private function headers(Request $request): array
    {
        $out = [];
        foreach (config('scrapeguard.capture.headers', []) as $header) $out[$header] = $request->header($header);
        return $out;
    }

    private function redact(array $data): array
    {
        $keys = array_map('strtolower', config('scrapeguard.capture.redact_keys', []));
        foreach ($data as $key => $value) {
            if (in_array(strtolower((string)$key), $keys, true)) $data[$key] = '[REDACTED]';
            elseif (is_array($value)) $data[$key] = $this->redact($value);
        }
        return $data;
    }

    private function table(): string { return config('scrapeguard.storage.table', 'scrapeguard_events'); }
    private function blockedTable(): string { return config('scrapeguard.storage.blocked_table', 'scrapeguard_blocked_ips'); }
}
