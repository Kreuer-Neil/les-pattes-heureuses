<?php

use App\Http\Controllers\AnimalController;

Route::get('animals', [AnimalController::class, 'index'])
    ->name('animals.index');

Route::post('animals', [AnimalController::class, 'store'])
    ->name('animals.store');

Route::get('animals/{animal}', [AnimalController::class, 'show'])
    ->name('animals.show');

Route::put('animals/{animal}', [AnimalController::class, 'update'])
    ->name('animals.update');

Route::patch('animal-changes/{pendingAnimalChange}/accept', [AnimalController::class, 'acceptChange'])
    ->name('animal-changes.accept');

Route::patch('animal-changes/{pendingAnimalChange}/deny', [AnimalController::class, 'denyChange'])
    ->name('animal-changes.deny');
