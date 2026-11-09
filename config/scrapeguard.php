<?php
return [
 'enabled'=>env('SCRAPEGUARD_ENABLED',true),
 'thresholds'=>['requests_per_minute'=>(int)env('SCRAPEGUARD_RPM',120),'sequential_paths'=>(int)env('SCRAPEGUARD_SEQUENTIAL',20),'risk_to_notify'=>(int)env('SCRAPEGUARD_NOTIFY_SCORE',70)],
 'capture'=>['query'=>true,'body'=>env('SCRAPEGUARD_CAPTURE_BODY',false),'headers'=>['user-agent','accept','referer','accept-language'],'redact_keys'=>['password','password_confirmation','token','access_token','refresh_token','authorization','cookie','card_number']],
 'storage'=>['table'=>'scrapeguard_events','retention_days'=>30],
 'notifications'=>['enabled'=>env('SCRAPEGUARD_NOTIFICATIONS',true),'mail_to'=>env('SCRAPEGUARD_MAIL_TO')],
 'exclude'=>['paths'=>['livewire/*','horizon/*','telescope/*'],'ips'=>[],'user_agents'=>['Googlebot','Bingbot']],
 'dashboard'=>['enabled'=>true,'prefix'=>env('SCRAPEGUARD_PREFIX','scrapeguard'),'middleware'=>['web',\App\Http\Middleware\ScrapeGuardAuth::class]]
];
