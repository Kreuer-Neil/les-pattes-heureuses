<?php

use App\Http\Controllers\AnimalChangeController;
use App\Http\Controllers\AnimalController;

Route::get('animals', [AnimalController::class, 'index'])
    ->name('animals.index');

Route::post('animals', [AnimalController::class, 'store'])
    ->name('animals.store');

Route::get('animals/{animal}', [AnimalController::class, 'show'])
    ->name('animals.show');

Route::put('animals/{animal}', [AnimalController::class, 'update'])
    ->name('animals.update');

Route::patch('animals/{animal}/deceased', [AnimalController::class, 'markDeceased'])
    ->name('animals.mark-deceased');

Route::patch('animals/{animal}/recover', [AnimalController::class, 'recoverAnimal'])
    ->name('animals.recover-animal');

Route::patch('animal-changes/{pendingAnimalChange}/accept', [AnimalChangeController::class, 'acceptChange'])
    ->name('animal-changes.accept');

Route::patch('animal-changes/{pendingAnimalChange}/deny', [AnimalChangeController::class, 'denyChange'])
    ->name('animal-changes.deny');
