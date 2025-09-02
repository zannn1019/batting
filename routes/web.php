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
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
    Route::get('/team', fn() => 'tes')->name('team');
    Route::put('/batting/{id}/approve', [BattingController::class, 'approve'])->name('batting.approve');
    Route::delete('/batting/{id}/reject', [BattingController::class, 'reject'])->name('batting.reject');
    Route::resource('user', UserController::class)->names('user');
    Route::resource('team', TeamController::class)->names('team');
    Route::resource('batting', BattingController::class)->names('batting');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
