<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShippingController;

// Home route - redirect to products
Route::get('/', [ProductController::class, 'index'])->name('home');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/category/{categoryId}', [ProductController::class, 'byCategory'])->name('products.by-category');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');

// Category routes (public)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');

// Cart routes (can be used by guests)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::get('/count', [CartController::class, 'count'])->name('count');
    
    // Discount routes must come before /{cartItem} routes to avoid conflicts
    Route::post('/discount', [CartController::class, 'applyDiscount'])->name('discount.apply');
    Route::delete('/discount', [CartController::class, 'removeDiscount'])->name('discount.remove');
    
    // Parameterized routes must come last
    Route::put('/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});

// Order routes (require authentication and customer only)
Route::middleware(['auth', 'customer'])->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::get('/{order}/success', [OrderController::class, 'success'])->name('success');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    Route::post('/{order}/payment', [OrderController::class, 'confirmPayment'])->name('payment.confirm');
    Route::get('/{order}/review', [OrderController::class, 'review'])->name('review');
    Route::post('/{order}/review', [OrderController::class, 'storeReview'])->name('review.store');
    Route::get('/{order}/tracking', [OrderController::class, 'tracking'])->name('tracking');
});

// Payment routes (require authentication)
Route::middleware(['auth', 'customer'])->prefix('payment')->name('payment.')->group(function () {
    Route::post('/{order}/create', [PaymentController::class, 'createPayment'])->name('create');
    Route::get('/{order}/success', [PaymentController::class, 'paymentSuccess'])->name('success');
    Route::get('/{order}/failed', [PaymentController::class, 'paymentFailed'])->name('failed');
    Route::get('/{order}/status', [PaymentController::class, 'checkStatus'])->name('status');
});

// Xendit webhook (no authentication required - verified by callback token)
Route::post('/xendit/webhook', [PaymentController::class, 'webhook'])->name('xendit.webhook');

// Shipping routes (for AJAX calls)
Route::prefix('shipping')->name('shipping.')->group(function () {
    Route::get('/provinces', [ShippingController::class, 'getProvinces'])->name('provinces');
    Route::get('/cities', [ShippingController::class, 'getCities'])->name('cities');
    Route::post('/calculate', [ShippingController::class, 'calculateCost'])->name('calculate');
    Route::post('/costs', [ShippingController::class, 'getMultipleCosts'])->name('costs');
    Route::get('/couriers', [ShippingController::class, 'getCouriers'])->name('couriers');
});

// Profile routes (require authentication)
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    Route::put('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
    Route::post('/address', [ProfileController::class, 'storeAddress'])->name('address.store');
    Route::put('/address/{id}', [ProfileController::class, 'updateAddress'])->name('address.update');
    Route::delete('/address/{id}', [ProfileController::class, 'deleteAddress'])->name('address.delete');
});

// Wishlist routes (require authentication and customer only)
Route::middleware(['auth', 'customer'])->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/', [WishlistController::class, 'store'])->name('store');
    Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('destroy');
    Route::get('/check/{productId}', [WishlistController::class, 'check'])->name('check');
    Route::get('/count', [WishlistController::class, 'count'])->name('count');
    Route::post('/move-to-cart', [WishlistController::class, 'moveToCart'])->name('move-to-cart');
});

// Review routes
Route::get('/products/{product}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::middleware('auth')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Admin routes (require authentication and admin role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Product management
    Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.destroy');
    
    // Category management
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    
    // Order management
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    
    // User management
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-admin', [App\Http\Controllers\Admin\UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    
    // Review management
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Test Google OAuth route
Route::get('/test-google', function () {
    return view('test-google');
})->name('test.google');

// Authentication routes will be added by Laravel Breeze/UI
require __DIR__.'/auth.php';
