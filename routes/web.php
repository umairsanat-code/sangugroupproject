<?php

use Illuminate\Support\Facades\Route;
use Sanat\ScrapeGuard\Http\Middleware\DetectScraping;
use Illuminate\Support\Facades\Artisan;

Route::get('/admin/login', function (\App\Services\SeoResolver $seoResolver) {
    return view('app', ['seo' => $seoResolver->resolve('/admin/login')]);
})->name('login');


Route::get('/admin/reset-storage', function () {

    // Remove old storage link
    Artisan::call('storage:unlink');

    // Create storage link again
    Artisan::call('storage:link');

    return response()->json([
        'success' => true,
        'message' => 'Storage link removed and recreated successfully.',
        'output' => Artisan::output(),
    ]);
});

Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $file = storage_path("app/public/{$folder}/{$filename}");

    if (!is_file($file)) {
        abort(404);
    }

    return response()->file($file);
})->where([
    'folder' => '[A-Za-z0-9_-]+',
    'filename' => '.*',
]);
Route::get('/test-umrah-image', function () {
    $file = storage_path(
        'app/public/umrah_packages/QEx2cgzh2eT7c4zAovGQ2FMW7tvUl2DXcJ2FW5dK.webp'
    );

    return response()->file($file);
});
// Server-rendered blog pages (posts published from the SEO module via webhook).
// Disabled: blog pages now load through the Vue SPA so the full site navbar is used.
// (See BlogList.vue / BlogDetail.vue components.)
// Route::get('/blog', [\App\Http\Controllers\BlogViewController::class, 'index']);
// Route::get('/blog/{slug}', [\App\Http\Controllers\BlogViewController::class, 'show']);
Route::middleware(DetectScraping::class)->get('/scrape-test/{id}', function ($id) {
    return response()->json([
        'id' => $id,
        'name' => 'Test Product ' . $id,
    ]);
});
// Dynamic XML sitemap (must be registered before the catch-all fallback route).
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index']);
Route::get('/{any}', function (\App\Services\SeoResolver $seoResolver) {
    // Determine the current path exactly as it would be matched by the DB.
    $path = '/' . request()->path();
    if ($path === '//') $path = '/';

    // Resolve server-rendered SEO (title/meta/canonical/JSON-LD) for this route,
    // preferring admin-managed rows and falling back to DB-derived / default data.
    $seo = $seoResolver->resolve($path);

    return view('app', compact('seo'));
})->where('any', '^(?!api).*$');



