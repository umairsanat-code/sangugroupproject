<?php
namespace Sanat\ScrapeGuard\Console;
use Illuminate\Console\Command;use Illuminate\Support\Facades\DB;
class StatusCommand extends Command{protected $signature='scrapeguard:status {--minutes=60}';protected $description='Show recent ScrapeGuard activity';public function handle():int{$rows=DB::table(config('scrapeguard.storage.table','scrapeguard_events'))->where('created_at','>=',now()->subMinutes((int)$this->option('minutes')))->orderByDesc('risk_score')->limit(20)->get();$this->table(['IP','Method','Path','Score','Created'],$rows->map(fn($r)=>[$r->ip,$r->method,$r->path,$r->risk_score,$r->created_at]));return self::SUCCESS;}}
