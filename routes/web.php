<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ScannerController; // ✅ Perbaiki namespace (Controllers, bukan Controller)

// Route utama: redirect ke dashboard sesuai role
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('dashboard.admin');
        }
        return redirect()->route('dashboard.user');
    }
    return redirect()->route('login');
});

// Route autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route untuk semua user yang login
Route::middleware(['auth'])->group(function () {
    // Dashboard berdasarkan role
    Route::get('/dashboard/user', [DashboardController::class, 'userDashboard'])
        ->middleware('role:user')
        ->name('dashboard.user');

    Route::get('/dashboard/admin', [AdminController::class, 'adminDashboard'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

    // Route measurement (bisa diakses semua role)
    Route::get('/measurement/{id?}', [MeasurementController::class, 'show'])->name('measurement.show');

    // Profile user
    Route::get('/dashboard/user/scanner', [DashboardController::class, 'userScanner'])->name('dashboard.user.scanner');
        Route::delete('/dashboard/user/scanner/{scanner}', [DashboardController::class, 'destroyUserScanner'])->name('dashboard.user.scanner.destroy');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile', [DashboardController::class, 'destroyAccount'])->name('profile.destroy');

    // ✅ Scanner (untuk user biasa / operator)
    Route::prefix('scanner')->group(function () {
        Route::get('/', [ScannerController::class, 'index'])->name('scanner.index');
        Route::post('/', [ScannerController::class, 'store'])->name('scanner.store');   // tambah ini
        Route::delete('/{scanner}', [ScannerController::class, 'destroy'])->name('scanner.destroy');
    });

});

// Route khusus ADMIN (CRUD truk, driver, manajemen user)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Manajemen Truk
    Route::prefix('trucks')->group(function () {
        Route::get('/', [TruckController::class, 'index'])->name('trucks.index');
        Route::get('/create', [TruckController::class, 'create'])->name('trucks.create');
        Route::post('/', [TruckController::class, 'store'])->name('trucks.store');
        Route::get('/{truck}/edit', [TruckController::class, 'edit'])->name('trucks.edit');
        Route::put('/{truck}', [TruckController::class, 'update'])->name('trucks.update');
        Route::delete('/{truck}', [TruckController::class, 'destroy'])->name('trucks.destroy');
    });

    // Manajemen Driver
    Route::prefix('drivers')->group(function () {
        Route::get('/', [DriverController::class, 'index'])->name('drivers.index');
        Route::get('/create', [DriverController::class, 'create'])->name('drivers.create');
        Route::post('/', [DriverController::class, 'store'])->name('drivers.store');
        Route::get('/{driver}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
        Route::put('/{driver}', [DriverController::class, 'update'])->name('drivers.update');
        Route::delete('/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');
    });

    // Manajemen User (CRUD user)
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminController::class, 'usersIndex'])->name('admin.users');
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    });

    Route::prefix('scanner')->group(function() {
        Route::get('/admin/scanners', [AdminController::class, 'scannerIndex'])->name('admin.scanners');
        Route::get('/admin/scanners/{id}/edit', [AdminController::class, 'scannerEdit'])->name('admin.scanners.edit');
        Route::put('/admin/scanners/{id}', [AdminController::class, 'scannerUpdate'])->name('admin.scanners.update');
        Route::delete('/admin/scanners/{id}', [AdminController::class, 'scannerDestroy'])->name('admin.scanners.destroy');
    
    });
});