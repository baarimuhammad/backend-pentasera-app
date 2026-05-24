<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\VerificationController;

// ───────────────────────────────────────────
// Email Verification (user clicks link from email)
// ───────────────────────────────────────────
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyFromEmail'])
    ->middleware('signed')
    ->name('verification.verify');

// Halaman Utama
Route::get('/', [PageController::class, 'home']);

// Auth
Route::get('/auth', function () { return view('auth'); });

// Events
Route::get('/events', [PageController::class, 'events']);
Route::get('/order/{event}', [PageController::class, 'order']);
Route::get('/checkout', [PageController::class, 'checkout']);
Route::get('/payment', function () { return view('payment'); });

// Dashboard & Management (Creator Only — auth dibekukan sementara)
Route::get('/dashboard', function () { return view('dashboard'); });
Route::get('/my-events', [PageController::class, 'myEvents']);
Route::get('/create-event', function () { return view('create-event'); });
Route::get('/manage-access', [PageController::class, 'manageAccess']);
Route::get('/manage-event/{event}', [PageController::class, 'manageEvent']);
Route::get('/event-report/{event}', [PageController::class, 'eventReport']);

// Auth Support
Route::get('/reset-password', function () { return view('reset-password'); });

// Pusat Bantuan
Route::get('/pusat-bantuan', function () { return view('pusat-bantuan'); });

// Profile & Settings
Route::get('/profile', function () { return view('profile'); });
Route::get('/settings', function () { return view('settings'); });

// My Tickets (Pembeli)
Route::get('/my-tickets', function () { return view('my-tickets'); });

// Static Pages
Route::get('/tentang-kami', function () { return view('tentang-kami'); });
Route::get('/hubungi-kami', function () { return view('hubungi-kami'); });
Route::get('/kebijakan-privasi', function () { return view('kebijakan-privasi'); });
Route::get('/syarat-ketentuan', function () { return view('syarat-ketentuan'); });
