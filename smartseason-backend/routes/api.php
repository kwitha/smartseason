<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FieldAssignmentController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\FieldUpdateController;
use App\Http\Controllers\UserController;




// ── Public ──────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ── Authenticated (any role) ─────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    
    Route::apiResource('fields', FieldController::class)->except(['destroy']);
    // Inside auth:sanctum middleware group
Route::get('my-fields', [FieldController::class, 'myFields']);

    Route::get ('fields/{field}/updates', [FieldUpdateController::class, 'index']);
    Route::post('fields/{field}/updates', [FieldUpdateController::class, 'store']);

    // ── Admin only ───────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

       
        Route::delete('fields/{field}', [FieldController::class, 'destroy']);

        Route::post  ('fields/{field}/assignments',           [FieldAssignmentController::class, 'store']);
        Route::delete('fields/{field}/assignments/{agentId}', [FieldAssignmentController::class, 'destroy']);

        // List all agents (used by admin when assigning)
        Route::get('agents', [UserController::class, 'agents']);
    });
});