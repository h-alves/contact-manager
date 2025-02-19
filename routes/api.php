<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

Route::delete('/account', [AuthController::class, 'deleteAccount'])->middleware('auth:sanctum');

Route::apiResource('/contact', ContactController::class)->middleware('auth:sanctum');

Route::get('/address/search', [AddressController::class, 'search'])->middleware('auth:sanctum');
