<?php

use App\Http\Controllers\AnimalChangeController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalNoteController;

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

Route::post('animals/{animal}/notes', [AnimalNoteController::class, 'store'])
    ->name('animal-notes.store');

Route::put('animal-notes/{animalNote}', [AnimalNoteController::class, 'update'])
    ->name('animal-notes.update');

Route::delete('animal-notes/{animalNote}', [AnimalNoteController::class, 'destroy'])
    ->name('animal-notes.destroy');
