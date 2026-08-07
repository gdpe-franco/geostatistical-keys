<?php

use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function (): void {
    Route::get('summary', [StateController::class, 'summary'])
        ->middleware('throttle:10,1')
        ->name('summary');

    Route::prefix('states')->name('states.')->group(function (): void {
        Route::get('/', [StateController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('index');

        Route::get('{state:state_code}/municipalities', [MunicipalityController::class, 'index'])
            ->where('state', '[0-9]{2}')
            ->middleware('throttle:30,1')
            ->name('municipalities.index');
    });
});
