<?php

use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'render'])
        ->name('dashboard');
});
