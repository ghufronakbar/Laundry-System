<?php

use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserMachineController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('auth/login', [UserAuthController::class, 'login']);
Route::post('auth/register', [UserAuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserProfileController::class, 'show']);
        Route::put('/', [UserProfileController::class, 'update']);
        Route::post('/', [UserProfileController::class, 'updatePicture']);
        Route::delete('/', [UserProfileController::class, 'deletePicture']);
    });

    Route::apiResource('machines', UserMachineController::class);
    Route::apiResource('reservations', UserReservationController::class);
    Route::get('machines/test/test', [UserMachineController::class, 'testing']);
});
