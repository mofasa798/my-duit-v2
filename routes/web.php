<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

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

    Route::get('/reports/{report}/export/{format}', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    Route::resource('reports', App\Http\Controllers\ReportController::class)
        ->only(['index', 'store', 'destroy']);
});

require __DIR__.'/auth.php';
