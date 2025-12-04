<?php

use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

Route::get('dashboard', [DashboardController::class, 'render'])
    ->name('dashboard');
