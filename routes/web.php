<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminPortfolioController;
use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\AdminShopController;
use App\Http\Controllers\AdminContactController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (){
    return view('welcome');
});

// Rute Landing Page (Menggunakan __invoke)
Route::get('/', LandingController::class)->name('landing');

// Rute Shop Page (Menggunakan __invoke)
// Route Shop Utama
Route::get('/shop', [ShopController::class, 'index'])->name('shop');

// Route untuk mengambil detail produk (Ajax)
Route::get('/product/{id}', [ShopController::class, 'show'])->name('product.show');
// {product?} berarti parameter ini opsional (untuk halaman awal shop)
Route::get('/shop/{product?}', [ShopController::class, 'index'])->name('shop');
// ADMIN ROUTES (Pastikan ini sudah benar)
Route::prefix('admin')->middleware('auth')->group(function () {
    // ... route home, categories, portfolio ...
    
    // Route Resource untuk Shop
    Route::resource('shop', AdminShopController::class)->names('admin.shop');
    
    // ... route contact ...
});
// Rute Contact Page (Menggunakan method create & store)
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Route Portfolio
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');

// Route Admin (Grup dengan Middleware Auth)
Route::prefix('admin')->group(function () {
    
    // Login (Tanpa middleware auth, karena ini untuk masuk)
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    // Routes yang butuh login
    Route::middleware('auth')->group(function () {
        // Dashboard (redirect ke portfolio index saja untuk simplifikasi)
        Route::get('/dashboard', [AdminPortfolioController::class, 'index'])->name('admin.dashboard');
        // CRUD Portfolio
         Route::resource('admin/portfolio', AdminPortfolioController::class)->names('admin.portfolio');


        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});

Route::prefix('admin')->group(function () {
    // Login routes...
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth')->group(function () {
        // Dashboard (Redirect ke home settings)
        Route::get('/dashboard', [AdminHomeController::class, 'edit'])->name('admin.dashboard');
        
        // Home Content Management
        Route::get('/home', [AdminHomeController::class, 'edit'])->name('admin.home.edit');
        Route::post('/home', [AdminHomeController::class, 'update'])->name('admin.home.update');
        // Shop Management (Resource)
        Route::resource('shop', AdminShopController::class)->names('admin.shop');

        // Portfolio Management (Resource) - Pastikan nama route konsisten
        Route::resource('portfolio', AdminPortfolioController::class)->names('admin.portfolio');

        // Contact Messages
        Route::get('/contact', [AdminContactController::class, 'index'])->name('admin.contact.index');
        Route::delete('/contact/{contact}', [AdminContactController::class, 'destroy'])->name('admin.contact.destroy');
        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});

// ... di dalam group admin middleware

// Route Categories
Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

Route::prefix('admin')->middleware('auth')->group(function () {
    
    // ... route home, categories, portfolio ...
    
    // Route Resource untuk Shop (Ini akan membuat otomatis route index, create, store, edit, update, destroy)
    Route::resource('shop', AdminShopController::class)->names('admin.shop');
    
    // ... route contact ...
});

// Group Middleware Auth (Hanya bisa akses jika login)
Route::middleware('auth')->group(function () {
    // Cart
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    
    // Success
    Route::get('/order/success', [CheckoutController::class, 'success'])->name('order.success');
});