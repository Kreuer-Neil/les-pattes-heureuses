<?php

use App\Http\Controllers\NotificationsController;

Route::get('notifications', [NotificationsController::class, 'index'])
    ->name('notifications.index');
