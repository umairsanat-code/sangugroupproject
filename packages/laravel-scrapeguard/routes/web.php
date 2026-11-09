<?php

use Illuminate\Support\Facades\Route;
use Sanat\ScrapeGuard\Http\Controllers\DashboardController;
use Sanat\ScrapeGuard\Http\Middleware\DetectScraping;

if (config('scrapeguard.dashboard.enabled', true)) {
    Route::middleware(DetectScraping::class)->get('/scrapeguard/honeypot/{token?}', fn () => response()->json([
        'ok' => true,
        'message' => 'ScrapeGuard monitored endpoint',
    ]))->name('scrapeguard.honeypot');

    Route::prefix(config('scrapeguard.dashboard.prefix', 'scrapeguard'))
        ->name('scrapeguard.')
        ->middleware(config('scrapeguard.dashboard.middleware', ['web', 'auth']))
        ->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/events', [DashboardController::class, 'events'])->name('events');
            Route::get('/events/{id}', [DashboardController::class, 'show'])->whereNumber('id')->name('event');
            Route::post('/block', [DashboardController::class, 'block'])->name('block');
            Route::delete('/unblock/{ip}', [DashboardController::class, 'unblock'])->name('unblock');
        });
}
