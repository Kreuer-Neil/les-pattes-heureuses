<?php

use App\Http\Controllers\AnimalController;

Route::get('animals', [AnimalController::class, 'index'])
->name('animals.index');

Route::post('animals', [AnimalController::class, 'store'])
->name('animals.store');
