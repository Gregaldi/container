<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\ContainerController;
// use App\Http\Controllers\TerminalActivityController;
// use App\Http\Controllers\TpsActivityController;

use App\Http\Controllers\ContainerController;
use App\Http\Controllers\MovementController;



Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});

// Route::post('/containers', [ContainerController::class, 'store']);
// Route::apiResource('containers', ContainerController::class);
// Route::apiResource('terminal-activities', TerminalActivityController::class);
// Route::post('/terminal-activities/update/{no_plat}', [TerminalActivityController::class, 'updateByPlat']);
// Route::post('/tps-activities/update/{no_plat}', [TpsActivityController::class, 'updateByPlat']);
// Route::apiResource('tps-activities', TpsActivityController::class);

Route::post('/containers', [ContainerController::class, 'store']);
Route::get('/containers/{container_number}', [ContainerController::class, 'show']);

Route::post('/movements/in', [MovementController::class, 'storeIn']);
Route::post('/movements/out', [MovementController::class, 'storeOut']);
Route::get('/movements/{container_number}', [MovementController::class, 'index']);
