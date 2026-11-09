<?php

namespace Sanat\ScrapeGuard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Sanat\ScrapeGuard\Services\ScrapeGuardManager;

class DashboardController extends Controller
{
    public function __construct(private ScrapeGuardManager $guard) {}

    private function table(): string { return config('scrapeguard.storage.table', 'scrapeguard_events'); }
    private function blockedTable(): string { return config('scrapeguard.storage.blocked_table', 'scrapeguard_blocked_ips'); }

    public function index(Request $request)
    {
        $days = max(1, min(90, (int) $request->integer('days', 7)));
        $from = now()->subDays($days);
        $events = DB::table($this->table())->where('created_at', '>=', $from);
        $stats = [
            'total' => (clone $events)->count(),
            'unique_ips' => (clone $events)->distinct('ip')->count('ip'),
            'suspicious' => (clone $events)->where('risk_score', '>=', 50)->count(),
            'high_risk' => (clone $events)->where('risk_score', '>=', 70)->count(),
            'blocked' => DB::table($this->blockedTable())->where('active', true)->count(),
        ];
        $daily = (clone $events)->selectRaw('DATE(created_at) day, COUNT(*) total, SUM(CASE WHEN risk_score >= 50 THEN 1 ELSE 0 END) suspicious')->groupBy('day')->orderBy('day')->get();
        $topIps = (clone $events)->select('ip')->selectRaw('COUNT(*) requests')->selectRaw('MAX(risk_score) max_score')->groupBy('ip')->orderByDesc('requests')->limit(10)->get();
        $topPaths = (clone $events)->select('path')->selectRaw('COUNT(*) requests')->selectRaw('MAX(risk_score) max_score')->groupBy('path')->orderByDesc('requests')->limit(10)->get();
        $recent = DB::table($this->table())->orderByDesc('id')->limit(20)->get();
        return view('scrapeguard::dashboard', compact('stats','daily','topIps','topPaths','recent','days'));
    }

    public function events(Request $request)
    {
        $query = DB::table($this->table());
        if ($request->filled('ip')) $query->where('ip', $request->string('ip'));
        if ($request->filled('min_score')) $query->where('risk_score', '>=', (int) $request->min_score);
        if ($request->filled('path')) $query->where('path', 'like', '%'.$request->string('path').'%');
        if ($request->filled('method')) $query->where('method', strtoupper($request->string('method')));
        $events = $query->orderByDesc('id')->paginate(50)->withQueryString();
        return view('scrapeguard::events', compact('events'));
    }

    public function show(int $id)
    {
        $event = DB::table($this->table())->where('id', $id)->firstOrFail();
        return view('scrapeguard::event', compact('event'));
    }

    public function block(Request $request)
    {
        $data = $request->validate(['ip' => ['required','ip'], 'reason' => ['nullable','string','max:500'], 'minutes' => ['nullable','integer','min:1','max:43200']]);
        $this->guard->block($data['ip'], $data['reason'] ?? 'Blocked from ScrapeGuard dashboard', isset($data['minutes']) ? (int)$data['minutes'] : null);
        return back()->with('success', 'IP blocked successfully.');
    }

    public function unblock(string $ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) abort(422);
        DB::table($this->blockedTable())->where('ip', $ip)->update(['active' => false, 'updated_at' => now()]);
        return back()->with('success', 'IP unblocked successfully.');
    }
}
