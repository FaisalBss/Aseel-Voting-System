<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\PollController;
use App\Http\Controllers\Api\userPollController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::post('/polls', [PollController::class, 'store']);
    Route::put('/polls/{pollId}', [PollController::class, 'update']);
    Route::delete('/polls/{pollId}', [PollController::class, 'destroy']);

});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
         return $request->user();
        });

    Route::get('/polls', [PollController::class, 'index']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/polls/{poll}/vote', [userPollController::class, 'vote']);
});

