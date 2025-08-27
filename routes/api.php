<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\TerminalActivityController;
use App\Http\Controllers\TpsActivityController;



Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});

Route::post('/containers', [ContainerController::class, 'store']);
// Route::apiResource('containers', ContainerController::class);
// Route::apiResource('terminal-activities', TerminalActivityController::class);
// Route::apiResource('tps-activities', TpsActivityController::class);
