<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PortfolioApiController;
use App\Http\Controllers\Api\CustomOrderApiController;
use App\Http\Controllers\Api\TonewoodApiController;
use App\Http\Controllers\Api\ContentApiController;
use App\Http\Controllers\Api\FaqApiController;
use App\Http\Controllers\Api\ContactApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Lynn's Bass & Guitar API
| Base URL: http://localhost:8000/api
|
| Public endpoints  → no auth required
| Admin endpoints   → require Bearer token from /api/login
|
*/

// =========================================================================
// AUTH (No middleware)
// =========================================================================
Route::post('/login', [AuthController::class, 'login']);

// =========================================================================
// PUBLIC ENDPOINTS (No auth required)
// =========================================================================

// Products / Gallery
Route::get('/products', [PortfolioApiController::class, 'index']);          // ?category=bass|guitar
Route::get('/products/{id}', [PortfolioApiController::class, 'show']);

// Tonewood / Materials
Route::get('/tonewood', [TonewoodApiController::class, 'index']);           // ?type=body|neck|fretboard
Route::get('/tonewood/{id}', [TonewoodApiController::class, 'show']);

// Content (CMS pages)
Route::get('/content', [ContentApiController::class, 'index']);              // ?section=about|shipping|...

// FAQ
Route::get('/faq', [FaqApiController::class, 'index']);

// Contact (submit inquiry)
Route::post('/contact', [ContactApiController::class, 'store']);

// Custom Orders (public: submit + track)
Route::post('/custom-orders', [CustomOrderApiController::class, 'store']);
Route::get('/custom-orders/track', [CustomOrderApiController::class, 'track']); // ?token=uuid

// =========================================================================
// ADMIN ENDPOINTS (Sanctum token required)
// =========================================================================
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- Admin: Products/Portfolio ---
    Route::post('/admin/products', [PortfolioApiController::class, 'store']);
    Route::put('/admin/products/{id}', [PortfolioApiController::class, 'update']);
    Route::delete('/admin/products/{id}', [PortfolioApiController::class, 'destroy']);

    // --- Admin: Custom Orders ---
    Route::get('/admin/custom-orders', [CustomOrderApiController::class, 'index']);
    Route::get('/admin/custom-orders/{id}', [CustomOrderApiController::class, 'show']);
    Route::put('/admin/custom-orders/{id}', [CustomOrderApiController::class, 'update']);
    Route::delete('/admin/custom-orders/{id}', [CustomOrderApiController::class, 'destroy']);

    // --- Admin: Tonewood ---
    Route::post('/admin/tonewood', [TonewoodApiController::class, 'store']);
    Route::put('/admin/tonewood/{id}', [TonewoodApiController::class, 'update']);
    Route::delete('/admin/tonewood/{id}', [TonewoodApiController::class, 'destroy']);

    // --- Admin: Content CMS ---
    Route::get('/admin/content', [ContentApiController::class, 'adminIndex']);
    Route::post('/admin/content', [ContentApiController::class, 'store']);
    Route::get('/admin/content/{id}', [ContentApiController::class, 'show']);
    Route::put('/admin/content/{id}', [ContentApiController::class, 'update']);
    Route::delete('/admin/content/{id}', [ContentApiController::class, 'destroy']);

    // --- Admin: FAQ ---
    Route::get('/admin/faq', [FaqApiController::class, 'adminIndex']);
    Route::post('/admin/faq', [FaqApiController::class, 'store']);
    Route::get('/admin/faq/{id}', [FaqApiController::class, 'show']);
    Route::put('/admin/faq/{id}', [FaqApiController::class, 'update']);
    Route::delete('/admin/faq/{id}', [FaqApiController::class, 'destroy']);

    // --- Admin: Contact Inquiries ---
    Route::get('/admin/contact-inquiries', [ContactApiController::class, 'index']);
    Route::get('/admin/contact-inquiries/{id}', [ContactApiController::class, 'show']);
    Route::put('/admin/contact-inquiries/{id}', [ContactApiController::class, 'update']);
    Route::delete('/admin/contact-inquiries/{id}', [ContactApiController::class, 'destroy']);
});
