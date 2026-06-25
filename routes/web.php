<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::patch('/dashboard/transactions/{transaction}/inline', [DashboardController::class, 'updateInline'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.transactions.inline');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('transactions', TransactionController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::resource('reports', App\Http\Controllers\ReportController::class)
        ->only(['index', 'store', 'destroy']);
});

require __DIR__.'/auth.php';
