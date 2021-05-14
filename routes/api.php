<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodosController;
use App\Http\Controllers\UsersController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('email')->group(function () {
    Route::get('verify', function () {
        return response()->json(['message' => 'Email not verified.']);
    })->name('verification.notice');

    Route::get('verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json(['message' => 'Email verified successfully.']);
    })->middleware('signed')->name('verification.verify');

    Route::post('verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification email resent.']);
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])
        ->withoutMiddleware('auth:sanctum');
    Route::post('register', [AuthController::class, 'register'])
        ->withoutMiddleware('auth:sanctum');
    Route::get('user', [AuthController::class, 'index']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('/', [AuthController::class, 'update']);
    Route::put('update-email', [AuthController::class, 'updateEmail']);
    Route::put('update-password', [AuthController::class, 'updatePassword']);
    Route::delete('delete', [AuthController::class, 'delete']);
});

Route::middleware('auth:sanctum')->prefix('users')->group(function () {
    Route::get('/', [UsersController::class, 'index']);
    Route::post('/', [UsersController::class, 'create']);
    Route::put('/{id}', [UsersController::class, 'update']);
    Route::put('/update-email/{id}', [UsersController::class, 'updateEmail']);
    Route::put('/update-password/{id}', [UsersController::class, 'updatePassword']);
    Route::delete('/{id}', [UsersController::class, 'delete']);
});

Route::middleware('auth:sanctum')->prefix('todos')->group(function () {
    Route::get('/', [TodosController::class, 'index']);
    Route::post('/', [TodosController::class, 'create']);
    Route::put('/{id}', [TodosController::class, 'update']);
    Route::put('/pinned-status/{id}', [TodosController::class, 'updatePinnedStatus']);
    Route::put('/completed-status/{id}', [TodosController::class, 'updateCompletedStatus']);
    Route::delete('/{id}', [TodosController::class, 'delete']);
});
