<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlatformController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PlatformController::class, 'home'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
    Route::get('/signup', [AuthController::class, 'showRegister'])->name('signup');
    Route::post('/signup', [AuthController::class, 'register'])->name('signup.perform');
});

Route::get('/browse', [PlatformController::class, 'browse'])->name('browse');
Route::get('/users/{user}', [PlatformController::class, 'profile'])->name('users.show');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/onboarding', [PlatformController::class, 'onboarding'])->name('onboarding');
    Route::post('/onboarding', [PlatformController::class, 'saveOnboarding'])->name('onboarding.save');
    Route::get('/dashboard', [PlatformController::class, 'dashboard'])->name('dashboard');
    Route::post('/users/{user}/save', [PlatformController::class, 'toggleSave'])->name('users.save');
    Route::post('/users/{user}/report', [PlatformController::class, 'reportUser'])->name('users.report');
    Route::post('/users/{user}/block', [PlatformController::class, 'blockUser'])->name('users.block');
    Route::post('/users/{user}/request', [PlatformController::class, 'createRequest'])->name('users.request');
    Route::post('/requests/{swapRequest}/respond', [PlatformController::class, 'respondToRequest'])->name('requests.respond');
    Route::get('/swaps/{swap}', [PlatformController::class, 'showSwap'])->name('swaps.show');
    Route::post('/swaps/{swap}/messages', [PlatformController::class, 'addMessage'])->name('swaps.messages.store');
    Route::post('/swaps/{swap}/sessions', [PlatformController::class, 'addSession'])->name('swaps.sessions.store');
    Route::post('/swaps/{swap}/progress', [PlatformController::class, 'updateProgress'])->name('swaps.progress.update');
    Route::post('/swaps/{swap}/reviews', [PlatformController::class, 'addReview'])->name('swaps.reviews.store');
    Route::get('/settings', [PlatformController::class, 'settings'])->name('settings');
    Route::post('/settings', [PlatformController::class, 'updateSettings'])->name('settings.update');
});
