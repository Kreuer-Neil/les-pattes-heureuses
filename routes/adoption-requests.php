<?php

use App\Http\Controllers\AdopterProfileController;
use App\Http\Controllers\AdoptionRequestController;

Route::get('adoption-requests', [AdoptionRequestController::class, 'index'])
    ->name('adoption-requests.index');

Route::patch('adoption-requests/{adoptionRequest}', [AdoptionRequestController::class, 'updateStatus'])
    ->name('adoption-requests.update-status');

Route::post('adoption-requests', [AdoptionRequestController::class, 'storeManual'])
    ->name('adoption-requests.store');

Route::post('adoption-requests/quick-adopt', [AdoptionRequestController::class, 'storeQuickAdopt'])
    ->name('adoption-requests.quick-adopt');

Route::patch('adoption-requests/{adoptionRequest}/content', [AdoptionRequestController::class, 'update'])
    ->name('adoption-requests.update');

Route::patch('adoption-requests/{adoptionRequest}/reply', [AdoptionRequestController::class, 'reply'])
    ->name('adoption-requests.reply');

Route::patch('adopter-profile/{adopterProfile}/update', [AdopterProfileController::class, 'update'])
    ->name('adopter-profile.update');
