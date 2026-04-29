<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes are prefixed with /api (set in bootstrap/app.php)
| Versioned under /api/v1
*/

Route::prefix('v1')->group(function () {

    // ── Public routes ──────────────────────────────────────────────────────
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::get('/health', fn () => response()->json([
        'status'  => 'ok',
        'service' => config('app.name'),
        'version' => 'v1',
    ]));

    // ── Protected routes (Sanctum token auth) ─────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout',  [AuthController::class, 'logout']);
        Route::get('/me',       [AuthController::class, 'me']);

        // Users (admin only)
        Route::middleware('can:admin')->group(function () {
            Route::apiResource('users', UserController::class);
        });

        // Posts (any authenticated user)
        Route::apiResource('posts', PostController::class);
    });
});
