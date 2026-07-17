<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

Route::redirect('/','dashboard');

Route::get('dashboard', [DashboardController::class, 'render'])
    ->name('dashboard');

Route::get('animals', [AnimalController::class, 'index'])
    ->name('animals.index');
