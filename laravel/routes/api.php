<?php

use App\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function (): void {
    Route::get('summary', [StateController::class, 'summary'])
        ->middleware('throttle:10,1')
        ->name('summary');

    Route::get('states', [StateController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('states.index');
});
