<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerificationController; // Tambahkan ini untuk mengimpor controller verifikasi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rute API Registrasi dan Login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Rute yang memerlukan otentikasi menggunakan Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    
    // Logout hanya boleh dilakukan oleh user yang sudah terautentikasi
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Rute untuk mendapatkan data user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Rute verifikasi email
Route::middleware(['auth:sanctum', 'signed'])->group(function () {
    // Verifikasi email
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->name('verification.verify');

    // Resend verifikasi email
    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->name('verification.send');
});

