<?php

use App\Http\Controllers\Client\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;


Route::get('/', [HomeController::class, 'index'])
    ->name('homepage');

Route::post('/contact', [HomeController::class, 'contact'])
    ->name('client.contact');

/*Route::get('/animals', [Client\AnimalController::class, 'index'])
->name('client.animals');*/

/*Route::get('/welcome', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');*/


Route::middleware(['guest'])->group(function() {
    Route::get('/login', function () {
        return Inertia::render('auth/login', [
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    })->name('login');

});

require __DIR__ . '/settings.php';
require __DIR__ . '/dashboard.php';
