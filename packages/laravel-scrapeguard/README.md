# Laravel ScrapeGuard

ScrapeGuard detects automated scraping behavior in Laravel applications, records request forensics, provides a protected admin dashboard, and can block suspicious IP addresses.

## Features
- Risk scoring for high request rates, automation user-agents, sequential crawling and honeypot hits
- Request forensic logging with IP, method, path, query, selected headers and optional redacted body
- Protected dashboard using Laravel `web` + `auth` middleware by default
- Event search and detailed forensic view
- Manual IP blocking/unblocking
- Optional temporary blocks and automatic blocking for critical scores
- Honeypot endpoint
- Artisan status command
- Configurable retention/capture settings

## Install
```bash
composer require sanat/laravel-scrapeguard
php artisan vendor:publish --tag=scrapeguard-config
php artisan vendor:publish --tag=scrapeguard-migrations
php artisan migrate
php artisan optimize:clear
```

Open `/scrapeguard` after logging in through your Laravel application.

### Existing admin middleware
If your application has its own admin middleware, publish the config and change:
```php
'dashboard' => [
    'middleware' => ['web', 'auth', 'admin'],
],
```

### Protect application routes
Add the middleware to public routes you want monitored:
```php
Route::middleware(\Sanat\ScrapeGuard\Http\Middleware\DetectScraping::class)->group(function () {
    // public routes
});
```

### Test honeypot
`/scrapeguard/honeypot/test` is monitored automatically. For production, keep it out of navigation and use it as a hidden trap.

### Optional auto blocking
```env
SCRAPEGUARD_AUTO_BLOCK=true
SCRAPEGUARD_BLOCK_SCORE=95
SCRAPEGUARD_BLOCK_MINUTES=60
```

Auto-blocking is disabled by default. Test thresholds before enabling it in production.
