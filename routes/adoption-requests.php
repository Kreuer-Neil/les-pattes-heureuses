<?php

use App\Http\Controllers\AdopterProfileController;
use App\Http\Controllers\AdoptionRequestController;

Route::get('adoption-requests', [AdoptionRequestController::class, 'index'])
    ->name('adoption-requests.index');

Route::patch('adoption-requests/{adoptionRequest}', [AdoptionRequestController::class, 'updateStatus'])
    ->name('adoption-requests.update-status');

Route::patch('adopter-profile/{adopterProfile}/update', [AdopterProfileController::class, 'update'])
    ->name('adopter-profile.update');
