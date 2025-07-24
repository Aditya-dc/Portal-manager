<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ServerController;

// Home route
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Excel Import/Export Routes
Route::get('/portals/export', [PortalController::class, 'export'])->name('portals.export');
Route::post('/portals/import', [PortalController::class, 'import'])->name('portals.import');
Route::get('/portals/import-template', [PortalController::class, 'downloadTemplate'])->name('portals.import-template');

    // Portal Routes
    Route::resource('portals', PortalController::class);
    Route::post('/portals/{portal}/check-status', [PortalController::class, 'checkStatus'])->name('portals.check-status');
    Route::post('/portals/check-all-status', [PortalController::class, 'checkAllStatus'])->name('portals.check-all-status');
    
    // Server Routes
    Route::resource('servers', ServerController::class);


});
