<?php

use App\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

Route::get('states', [StateController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('states.index');
