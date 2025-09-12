<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MessageController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OfficialProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// User Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.show');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::post('/profile/remove-image', [UserController::class, 'removeProfileImage'])->name('profile.image.remove');
    Route::get('/profile/change-password', [UserController::class, 'changePasswordForm'])->name('profile.password');
    Route::put('/profile/change-password', [UserController::class, 'changePassword']);
});

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/api/search/suggestions', [ProductController::class, 'searchSuggestions'])->name('products.search.suggestions');
Route::get('/api/search/popular', [ProductController::class, 'popularSearches'])->name('products.search.popular');

// Important: Place the create route before the show route to prevent routing conflicts
Route::middleware(['auth'])->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
});

// Show product detail - this needs to come after the create route
Route::get('/products/{productId}', [ProductController::class, 'show'])->name('products.show');

// Product edit/update/delete routes - these can be after the show route
Route::middleware(['auth'])->group(function () {
    Route::get('/products/{productId}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{productId}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{productId}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{productId}/review', [ProductController::class, 'addReview'])->name('products.review');
});

// Cart Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
    // Cart checkout routes
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/checkout/process', [CartController::class, 'processCheckout'])->name('cart.processCheckout');
});

// Orders
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{orderId}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{orderId}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Messaging system routes
Route::middleware(['auth'])->group(function () {
    // Message start route must come BEFORE the parameterized routes
    Route::post('/start-message', [MessageController::class, 'startConversation'])->name('messages.start');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::get('/messages/{productId}/{userId}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{productId}/{userId}', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/unread/count', [MessageController::class, 'getUnreadCount'])->name('messages.unread.count');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    // Dashboard
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Admin Profile (separate from user profile)
    Route::get('/profile', [\App\Http\Controllers\AdminController::class, 'adminProfile'])->name('admin.profile.show');
    Route::put('/profile', [\App\Http\Controllers\AdminController::class, 'updateAdminProfile'])->name('admin.profile.update');

    // User Management
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users.index');
    Route::get('/users/create', [\App\Http\Controllers\AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [\App\Http\Controllers\AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{userId}/edit', [\App\Http\Controllers\AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{userId}', [\App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{userId}', [\App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.users.destroy');
    Route::get('/users/{userId}', [\App\Http\Controllers\AdminController::class, 'showUser'])->name('admin.users.show');

    // Product Management
    Route::get('/products', [\App\Http\Controllers\AdminController::class, 'products'])->name('admin.products.index');
    Route::delete('/products/{productId}', [\App\Http\Controllers\AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

    // Order Management
    Route::get('/orders', [\App\Http\Controllers\AdminController::class, 'orders'])->name('admin.orders.index');
    Route::get('/orders/{orderId}', [\App\Http\Controllers\AdminController::class, 'viewOrder'])->name('admin.orders.show');
    Route::put('/orders/{orderId}/status', [\App\Http\Controllers\AdminController::class, 'updateOrderStatus'])->name('admin.orders.update-status');

    // Category Management
    Route::get('/categories', [\App\Http\Controllers\AdminController::class, 'categories'])->name('admin.categories.index');
    Route::get('/categories/create', [\App\Http\Controllers\AdminController::class, 'createCategory'])->name('admin.categories.create');
    Route::post('/categories', [\App\Http\Controllers\AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/categories/{categoryId}/edit', [\App\Http\Controllers\AdminController::class, 'editCategory'])->name('admin.categories.edit');
    Route::put('/categories/{categoryId}', [\App\Http\Controllers\AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{categoryId}', [\App\Http\Controllers\AdminController::class, 'deleteCategory'])->name('admin.categories.destroy');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports.index');

    // Official Products
    Route::get('/official-products', [\App\Http\Controllers\AdminController::class, 'officialProducts'])->name('admin.official-products.index');
    Route::get('/official-products/create', [\App\Http\Controllers\AdminController::class, 'createOfficialProduct'])->name('admin.official-products.create');
    Route::post('/official-products', [\App\Http\Controllers\AdminController::class, 'storeOfficialProduct'])->name('admin.official-products.store');
    Route::get('/official-products/{productId}/edit', [\App\Http\Controllers\AdminController::class, 'editOfficialProduct'])->name('admin.official-products.edit');
    Route::put('/official-products/{productId}', [\App\Http\Controllers\AdminController::class, 'updateOfficialProduct'])->name('admin.official-products.update');
    Route::delete('/official-products/{productId}', [\App\Http\Controllers\AdminController::class, 'destroyOfficialProduct'])->name('admin.official-products.destroy');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
});