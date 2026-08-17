<?php

use App\Http\Controllers\ContactMessageController;

Route::get('contact-messages', [ContactMessageController::class, 'index'])
    ->name('contact-messages.index');

Route::patch('contact-messages/{contactMessage}/reply', [ContactMessageController::class, 'reply'])
    ->name('contact-messages.reply');

Route::patch('contact-messages/{contactMessage}/ignore', [ContactMessageController::class, 'markIgnored'])
    ->name('contact-messages.mark-ignored');
