<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\UmrahGroupController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\SocialLinkController;
use App\Http\Controllers\PaymentSettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/inquiry', [InquiryController::class, 'store']);

// Public payment settings (Revolut account details shown to clients)
Route::get('/payment-settings', [PaymentSettingsController::class, 'index']);

// Public social links (website footer)
Route::get('/social-links', [SocialLinkController::class, 'index']);

// Promotions
Route::get('/promotions', [PromotionController::class, 'index']);
Route::post('/promotions', [PromotionController::class, 'store']);
Route::post('/promotions/{id}', [PromotionController::class, 'update']);
Route::patch('/promotions/{id}/toggle', [PromotionController::class, 'toggleActive']);
Route::delete('/promotions/{id}', [PromotionController::class, 'destroy']);

// Gallery
Route::get('/gallery', [GalleryController::class, 'index']);
Route::post('/gallery', [GalleryController::class, 'store']);
Route::post('/gallery/{id}', [GalleryController::class, 'update']);
Route::patch('/gallery/{id}/toggle', [GalleryController::class, 'toggleActive']);
Route::delete('/gallery/{id}', [GalleryController::class, 'destroy']);

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::get('/flights/search', [\App\Http\Controllers\FlightController::class, 'search']);
Route::get('/destinations', [\App\Http\Controllers\DestinationController::class, 'index']);
Route::get('/destinations/{slug}', [\App\Http\Controllers\DestinationController::class, 'show']);
Route::get('/hotels', [\App\Http\Controllers\HotelController::class, 'index']);
Route::post('/hotels', [\App\Http\Controllers\HotelController::class, 'store']);
Route::get('/hotels/{hotel}', [\App\Http\Controllers\HotelController::class, 'show']);
Route::delete('/hotels/{id}', [\App\Http\Controllers\HotelController::class, 'destroy']);

Route::get('/umrah-packages', [\App\Http\Controllers\UmrahPackageController::class, 'index']);
Route::post('/umrah-packages', [\App\Http\Controllers\UmrahPackageController::class, 'store']);
Route::get('/umrah-packages/{id}', [\App\Http\Controllers\UmrahPackageController::class, 'show']);
Route::post('/umrah-packages/{id}', [\App\Http\Controllers\UmrahPackageController::class, 'update']);
Route::delete('/umrah-packages/{id}', [\App\Http\Controllers\UmrahPackageController::class, 'destroy']);

Route::get('/visas', [\App\Http\Controllers\VisaController::class, 'index']);
Route::post('/visas', [\App\Http\Controllers\VisaController::class, 'store']);
Route::get('/visas/{slug}', [\App\Http\Controllers\VisaController::class, 'show']);
Route::post('/visas/{id}', [\App\Http\Controllers\VisaController::class, 'update']);
Route::delete('/visas/{id}', [\App\Http\Controllers\VisaController::class, 'destroy']);

// Blog
Route::get('/blogs', [BlogController::class, 'index']);
Route::post('/blogs', [BlogController::class, 'store']);
Route::get('/blogs/{slug}', [BlogController::class, 'show']);
Route::post('/blogs/{id}', [BlogController::class, 'update']);
Route::patch('/blogs/{id}/toggle', [BlogController::class, 'toggleActive']);
Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);

Route::get('/umrah-groups', [UmrahGroupController::class, 'index']);
Route::post('/umrah-groups', [UmrahGroupController::class, 'store']);
Route::get('/umrah-groups/{id}', [UmrahGroupController::class, 'show']);
Route::post('/umrah-groups/{id}', [UmrahGroupController::class, 'update']);
use App\Http\Controllers\VisaPaymentController;

Route::delete('/umrah-groups/{id}', [UmrahGroupController::class, 'destroy']);

// Visa Application 2-Step & Stripe Payment Integration
Route::prefix('visa')->group(function () {
    Route::post('/apply', [VisaPaymentController::class, 'submitApplication']);
    Route::post('/checkout-session', [VisaPaymentController::class, 'createCheckoutSession']);
    Route::get('/verify-payment', [VisaPaymentController::class, 'verifyPayment']);
});

// Stripe Webhook Endpoint
Route::post('/stripe/webhook', [VisaPaymentController::class, 'handleWebhook']);

Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::post('/', [BookingController::class, 'store']);
    Route::get('/export-csv', [VisaPaymentController::class, 'exportCsv']);
    Route::get('/{id}', [BookingController::class, 'show']);
    Route::match(['patch', 'put'], '/{id}/status', [BookingController::class, 'updateStatus']);
    Route::post('/payment-success', [BookingController::class, 'paymentSuccess']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('/user', [\App\Http\Controllers\AuthController::class, 'user']);

    Route::prefix('admin')->group(function() {
        Route::apiResource('customers', \App\Http\Controllers\CustomerController::class);
        Route::apiResource('flights', \App\Http\Controllers\FlightController::class);
        Route::apiResource('umrah-packages', \App\Http\Controllers\UmrahPackageController::class);
        Route::apiResource('vendors', \App\Http\Controllers\VendorController::class);

        Route::get('dashboard-stats', [\App\Http\Controllers\DashboardController::class, 'getStats']);

        // SEO Manager
        Route::post('seo/load', [\App\Http\Controllers\SeoController::class, 'load']);
        Route::post('seo/save', [\App\Http\Controllers\SeoController::class, 'save']);
        Route::post('seo/generate', [\App\Http\Controllers\SeoController::class, 'generate']);

        // Payment / Revolut settings
        Route::get('payment-settings', [PaymentSettingsController::class, 'adminGet']);
        Route::post('payment-settings', [PaymentSettingsController::class, 'adminSave']);

        // Social links (footer)
        Route::get('social-links', [SocialLinkController::class, 'adminGet']);
        Route::post('social-links', [SocialLinkController::class, 'adminSave']);
    });

    // Social Media Posting (Facebook & Instagram)
    Route::prefix('admin/social')->group(function () {
        Route::get('accounts', [SocialMediaController::class, 'accounts']);
        Route::get('oauth-url', [SocialMediaController::class, 'oauthUrl']);
        Route::post('accounts/{id}/connect', [SocialMediaController::class, 'connectAccount']);
        Route::post('accounts/{id}/disconnect', [SocialMediaController::class, 'disconnectAccount']);
        Route::delete('accounts/{id}', [SocialMediaController::class, 'destroyAccount']);

        Route::get('posts', [SocialMediaController::class, 'posts']);
        Route::post('posts', [SocialMediaController::class, 'store']);
        Route::post('posts/{id}/publish-scheduled', [SocialMediaController::class, 'publishScheduled']);
        Route::delete('posts/{id}', [SocialMediaController::class, 'destroyPost']);
    });
});

// Facebook OAuth callback (browser redirect from Facebook — no auth middleware)
Route::get('/social/facebook/callback', [SocialMediaController::class, 'oauthCallback']);

// AI Chatbot — public widget endpoints (no auth, rate limited).
Route::prefix('chat')->middleware('throttle:chat')->group(function () {
    Route::get('/config', [\App\Http\Controllers\ChatController::class, 'config']);
    Route::get('/history', [\App\Http\Controllers\ChatController::class, 'history']);
    Route::post('/message', [\App\Http\Controllers\ChatController::class, 'message']);
});

// AI Chatbot — admin management (auth required).
Route::middleware('auth:sanctum')->prefix('admin/chatbot')->group(function () {
    Route::get('settings', [\App\Http\Controllers\ChatbotAdminController::class, 'getSettings']);
    Route::post('settings', [\App\Http\Controllers\ChatbotAdminController::class, 'saveSettings']);
    Route::get('stats', [\App\Http\Controllers\ChatbotAdminController::class, 'stats']);

    Route::get('conversations', [\App\Http\Controllers\ChatbotAdminController::class, 'conversations']);
    Route::get('conversations/{id}', [\App\Http\Controllers\ChatbotAdminController::class, 'showConversation']);
    Route::delete('conversations/{id}', [\App\Http\Controllers\ChatbotAdminController::class, 'destroyConversation']);

    Route::get('leads', [\App\Http\Controllers\ChatbotAdminController::class, 'leads']);
    Route::patch('leads/{id}', [\App\Http\Controllers\ChatbotAdminController::class, 'updateLead']);
    Route::delete('leads/{id}', [\App\Http\Controllers\ChatbotAdminController::class, 'destroyLead']);
});
