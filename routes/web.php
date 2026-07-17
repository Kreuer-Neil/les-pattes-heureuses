<?php

use App\Http\Controllers\Client\AdoptionController;
use App\Http\Controllers\Client\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Laravel recommends putting subdomain routes before root routes, to not let URIs get overwritten
// Just in case.
Route::domain('admin.'.'les-pattes-heureuses.test')->group(function () {
    Route::middleware(['guest'])->group(function () {
//        Route::get('/login', function () {
//            return Inertia::render('auth/login', [
//                'canRegister' => Features::enabled(Features::registration()),
//            ]);
//        })->name('login');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        require __DIR__.'/dashboard.php';
        // Put only admin-access auth routes here
    });
});

Route::get('/', [HomeController::class, 'index'])
    ->name('homepage');

Route::post('/contact', [HomeController::class, 'contact'])
    ->name('client.contact');

Route::get('/adoption', [AdoptionController::class, 'index'])
    ->name('client.animals');

Route::get('/adoption/{animal}', [AdoptionController::class, 'show'])
    ->name('client.animal.show');

Route::post('/adoption/{animal}/request', [AdoptionController::class, 'request'])
    ->name('client.adoption.request');

require __DIR__.'/settings.php';
