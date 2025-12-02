<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // ---- Translations ----
    // Export route must be above the {id} route to avoid conflicts
    Route::get('translations/export', [TranslationController::class, 'export']); // dev export

    Route::get('translations', [TranslationController::class, 'index']); // dev list/search
    Route::get('translations/{id}', [TranslationController::class, 'show']); // dev view single
    Route::post('translations', [TranslationController::class, 'store']); // dev create
    Route::patch('translations/{id}', [TranslationController::class, 'update']); // dev update
    Route::delete('translations/{id}', [TranslationController::class, 'destroy']); // dev delete

    // ---- Locales ----
    Route::get('locales', [LocaleController::class, 'index']); // dev list
    Route::post('locales', [LocaleController::class, 'store']); // dev create

    // ---- Tags ----
    Route::get('tags', [TagController::class, 'index']); // dev list
    Route::post('tags', [TagController::class, 'store']); // dev create
});
