<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PeminjamanApiController;
use Illuminate\Support\Facades\Route;

// Publik: tukar email + password dengan token. Dibatasi 10 percobaan per menit.
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('api.login');

// Wajib header "Authorization: Bearer <token>", tanpa token dibalas 401.
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::apiResource('peminjaman', PeminjamanApiController::class)
        ->parameters(['peminjaman' => 'peminjaman'])
        ->names('api.peminjaman');
});
