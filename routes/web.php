<?php

use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\AdoptionRequestController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\EnsurePasswordHasBeenChanged;
use Illuminate\Support\Facades\Route;

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

    Route::middleware(['auth', EnsurePasswordHasBeenChanged::class])->group(function () {
        require __DIR__.'/dashboard.php';
        require __DIR__.'/animals.php';
        require __DIR__.'/adoption-requests.php';
        require __DIR__.'/contact-messages.php';
        require __DIR__.'/notifications.php';
        require __DIR__.'/users.php';
        // Put only admin-access auth routes here
    });
});

Route::get('/', [HomeController::class, 'index'])
    ->name('homepage');

Route::post('/contact', [ContactMessageController::class, 'store'])
    ->name('client.contact');

Route::get('/adoption', [AdoptionController::class, 'index'])
    ->name('client.animals');

Route::get('/adoption/{animal}', [AdoptionController::class, 'show'])
    ->name('client.animal.show');

Route::post('/adoption/{animal}/request', [AdoptionRequestController::class, 'store'])
    ->name('client.adoption.request');

require __DIR__.'/settings.php';
