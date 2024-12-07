<?php

use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserProfileController;
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
    // Profile Routes
    Route::get('/profile', [UserProfileController::class, 'show']);
    Route::put('/profile', [UserProfileController::class, 'update']);
    Route::post('/profile', [UserProfileController::class, 'updatePicture']);
    Route::delete('/profile', [UserProfileController::class, 'deletePicture']);
});
