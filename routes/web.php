<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BattingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/team', fn() => 'tes')
        ->name('team')
        ->middleware('role:admin,panitia');

    Route::put('/batting/{id}/approve', [BattingController::class, 'approve'])
        ->name('batting.approve')
        ->middleware('role:admin,panitia');

    Route::delete('/batting/{id}/reject', [BattingController::class, 'reject'])
        ->name('batting.reject')
        ->middleware('role:admin,panitia');

    Route::resource('user', UserController::class)
        ->names('user')
        ->middleware('role:admin,panitia');

    Route::resource('team', TeamController::class)
        ->names('team')
        ->middleware('role:admin,panitia');

    Route::resource('batting', BattingController::class)
        ->names('batting')
        ->middleware('role:admin,panitia');


    Route::middleware(['auth', 'role:manager'])->group(function () {
        Route::post('/batting', [BattingController::class, 'store'])->name('batting.store');
        Route::put('/batting/{batting}', [BattingController::class, 'update'])->name('batting.update');
        Route::delete('/batting/{batting}', [BattingController::class, 'destroy'])->name('batting.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
