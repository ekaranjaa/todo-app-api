<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\TodosController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:6,1'])->prefix('email')->group(function () {
    Route::get('verify', [EmailVerificationController::class, 'index'])->name('verification.notice');
    Route::post('verify/{id}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('resend-verification-notification', [EmailVerificationController::class, 'resend'])
        ->name('verification.resend');
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::get('google', [GoogleAuthController::class, 'authenticate']);
    Route::get('google-callback', [GoogleAuthController::class, 'callback']);
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::get('user', [AuthController::class, 'index']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('/', [AuthController::class, 'update']);
    Route::put('update-password', [AuthController::class, 'updatePassword']);
    Route::delete('delete', [AuthController::class, 'delete']);
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('users')->group(function () {
    Route::get('/', [UsersController::class, 'index']);
    Route::post('/', [UsersController::class, 'create']);
    Route::put('/{id}', [UsersController::class, 'update']);
    Route::put('/update-password/{id}', [UsersController::class, 'updatePassword']);
    Route::delete('/{id}', [UsersController::class, 'delete']);
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('todos')->group(function () {
    Route::get('/', [TodosController::class, 'index']);
    Route::get('daily/{date}', [TodosController::class, 'getDaily']);
    Route::get('weekly/{from}/{to}', [TodosController::class, 'getWeekly']);
    Route::post('/', [TodosController::class, 'create']);
    Route::put('/{id}', [TodosController::class, 'update']);
    Route::put('/pinned-status/{id}', [TodosController::class, 'updatePinnedStatus']);
    Route::put('/completed-status/{id}', [TodosController::class, 'updateCompletedStatus']);
    Route::delete('/{id}', [TodosController::class, 'delete']);
});
