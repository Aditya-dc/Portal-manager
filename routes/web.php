<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\UserManagementController;

// Home route: Redirect to dashboard if authenticated, else to login
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // --- Portal Routes ---
    // Static routes must come before dynamic routes for correct matching
    Route::get('/portals/export', [PortalController::class, 'export'])->name('portals.export');
    Route::get('/portals/import-template', [PortalController::class, 'downloadTemplate'])->name('portals.import-template');
    
    // Portal modification routes (Admin/SuperAdmin only)
    Route::middleware('check.role:admin')->group(function () {
        Route::get('/portals/create', [PortalController::class, 'create'])->name('portals.create');
        Route::post('/portals', [PortalController::class, 'store'])->name('portals.store');
        Route::post('/portals/import', [PortalController::class, 'import'])->name('portals.import');
        Route::post('/portals/check-all-status', [PortalController::class, 'checkAllStatus'])->name('portals.check-all-status');
    });
    
    // Portal view routes (all authenticated users)
    Route::get('/portals', [PortalController::class, 'index'])->name('portals.index');
    Route::get('/portals/{portal}', [PortalController::class, 'show'])->name('portals.show');
    
    // Portal individual modification routes (Admin/SuperAdmin only)
    Route::middleware('check.role:admin')->group(function () {
        Route::get('/portals/{portal}/edit', [PortalController::class, 'edit'])->name('portals.edit');
        Route::put('/portals/{portal}', [PortalController::class, 'update'])->name('portals.update');
        Route::delete('/portals/{portal}', [PortalController::class, 'destroy'])->name('portals.destroy');
        Route::post('/portals/{portal}/check-status', [PortalController::class, 'checkStatus'])->name('portals.check-status');
    });

    // --- Server Routes ---
    // Static routes must come before dynamic routes for correct matching

    // Server modification routes (Admin/SuperAdmin only)
    Route::middleware('check.role:admin')->group(function () {
        Route::get('/servers/create', [ServerController::class, 'create'])->name('servers.create');
        Route::post('/servers', [ServerController::class, 'store'])->name('servers.store');
    });

    // Server listing (all authenticated users)
    Route::get('/servers', [ServerController::class, 'index'])->name('servers.index');

    // Server details (SuperAdmin only)
    Route::middleware('check.role:superadmin')->group(function () {
        Route::get('/servers/{server}', [ServerController::class, 'show'])->name('servers.show');
    });

    // Server individual modification routes (Admin/SuperAdmin only)
    Route::middleware('check.role:admin')->group(function () {
        Route::get('/servers/{server}/edit', [ServerController::class, 'edit'])->name('servers.edit');
        Route::put('/servers/{server}', [ServerController::class, 'update'])->name('servers.update');
        Route::delete('/servers/{server}', [ServerController::class, 'destroy'])->name('servers.destroy');
    });
});

// --- Super Admin Only Routes ---
Route::middleware(['auth', 'check.role:superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // User Management (CRUD)
        Route::resource('users', UserManagementController::class);

        // Resource Assignments
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments');
        Route::post('/assign/{user}', [AssignmentController::class, 'assign'])->name('assign');
        Route::get('/user-data/{user}', [AssignmentController::class, 'getUserData'])->name('user.data');

        // Unassign routes (removing assignments)
        Route::delete('/unassign/{user}/server/{server}', [AssignmentController::class, 'removeServer'])->name('unassign.server');
        Route::delete('/unassign/{user}/portal/{portal}', [AssignmentController::class, 'removePortal'])->name('unassign.portal');
        Route::delete('/unassign/{user}/servers/all', [AssignmentController::class, 'removeAllServers'])->name('unassign.servers.all');
        Route::delete('/unassign/{user}/portals/all', [AssignmentController::class, 'removeAllPortals'])->name('unassign.portals.all');
        Route::post('/bulk-unassign/{user}', [AssignmentController::class, 'bulkUnassign'])->name('bulk.unassign');
    });
