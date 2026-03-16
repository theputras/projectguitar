<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\CustomOrderController;
use App\Http\Controllers\TonewoodController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\AdminPortfolioController;
use App\Http\Controllers\AdminFaqController;
use App\Http\Controllers\AdminTonewoodController;
use App\Http\Controllers\AdminContentController;
use App\Http\Controllers\AdminCustomOrderController;
use App\Http\Controllers\AdminContactController;
use App\Http\Controllers\AdminCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Lynn's Bass & Guitar - Boutique Handcrafted Instruments
| Brand showcase & portfolio website (not a marketplace)
|
*/

// =========================================================================
// PUBLIC ROUTES
// =========================================================================

// Homepage
Route::get('/', LandingController::class)->name('landing');

// About
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Portfolio / Gallery
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/portfolio/{portfolio}', [PortfolioController::class, 'show'])->name('portfolio.show');

// Custom Order
Route::get('/custom-order', [CustomOrderController::class, 'index'])->name('custom-order.index');
Route::get('/custom-order/form', [CustomOrderController::class, 'create'])->name('custom-order.create');
Route::post('/custom-order/form', [CustomOrderController::class, 'store'])->name('custom-order.store');
Route::get('/custom-order/track', [CustomOrderController::class, 'track'])->name('custom-order.track');

// Tonewoods / Materials
Route::get('/tonewoods', [TonewoodController::class, 'index'])->name('tonewoods');

// International Shipping
Route::get('/shipping', [ShippingController::class, 'index'])->name('shipping');

// FAQ
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Contact / Order Form
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');


// =========================================================================
// ADMIN ROUTES
// =========================================================================

Route::prefix('admin')->group(function () {

    // --- Auth (No middleware) ---
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    // --- Protected Routes ---
    Route::middleware('auth')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminHomeController::class, 'edit'])->name('admin.dashboard');

        // Home Content Settings
        Route::get('/home', [AdminHomeController::class, 'edit'])->name('admin.home.edit');
        Route::post('/home', [AdminHomeController::class, 'update'])->name('admin.home.update');

        // Portfolio CRUD
        Route::resource('portfolio', AdminPortfolioController::class)->names('admin.portfolio');

        // FAQ CRUD
        Route::resource('faqs', AdminFaqController::class)->names('admin.faqs');

        // Tonewood CRUD
        Route::resource('tonewoods', AdminTonewoodController::class)->names('admin.tonewoods');

        // Content CMS CRUD
        Route::resource('contents', AdminContentController::class)->names('admin.contents');

        // Custom Orders Management
        Route::resource('custom-orders', AdminCustomOrderController::class)->names('admin.custom-orders')
            ->except(['create', 'store']); // Orders are created by customers, not admin

        // Contact Inquiries
        Route::get('/contacts', [AdminContactController::class, 'index'])->name('admin.contacts.index');
        Route::get('/contacts/{contact}', [AdminContactController::class, 'show'])->name('admin.contacts.show');
        Route::put('/contacts/{contact}', [AdminContactController::class, 'update'])->name('admin.contacts.update');
        Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('admin.contacts.destroy');

        // Categories (keep existing)
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});