<?php

use App\Http\Controllers\AdoptionRequestController;

Route::get('adoption-requests', [AdoptionRequestController::class, 'index'])
    ->name('adoption-requests.index');

Route::patch('adoption-requests/{adoptionRequest}', [AdoptionRequestController::class, 'updateStatus'])
    ->name('adoption-requests.update-status');
