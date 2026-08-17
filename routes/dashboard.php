<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShelterReportController;

Route::redirect('/', 'dashboard');

Route::get('dashboard', [DashboardController::class, 'render'])
    ->name('dashboard');

Route::get('dashboard/report', [ShelterReportController::class, 'export'])
    ->name('reports.export');
