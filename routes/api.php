<?php

use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/attendance/history', [AttendanceApiController::class, 'history']);
    Route::post('/attendance/check-in', [AttendanceApiController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceApiController::class, 'checkOut']);
});
