<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AdminController;

// Email Verification (web — signed URL)
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyFromEmail'])
    ->middleware('signed')
    ->name('verification.verify');

// Halaman Utama
Route::get('/', [PageController::class, 'home']);

// Auth
Route::get('/auth', function () { return view('auth'); });

// Events
Route::get('/events', [PageController::class, 'events'])->name('events.page');
Route::get('/order/{event}', [PageController::class, 'order']);
Route::get('/checkout', [PageController::class, 'checkout']);
Route::get('/payment', [PageController::class, 'payment']);

// Dashboard & Management (Creator Only)
Route::get('/dashboard', [PageController::class, 'dashboard']);
Route::get('/my-events', [PageController::class, 'myEvents']);
Route::get('/create-event', [PageController::class, 'createEvent']);
Route::get('/manage-event/{event}', [PageController::class, 'manageEvent']);
Route::get('/event-report/{event}', [PageController::class, 'eventReport']);

// Admin Dashboard
Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Route::get('/admin/users', [AdminController::class, 'manageUsersPage']);
Route::get('/admin/analytics', [AdminController::class, 'analyticsPage']);
Route::get('/admin/export/csv', [AdminController::class, 'exportCsv']);
Route::get('/admin/export/pdf', [AdminController::class, 'analyticsReport']);

// Auth Support
Route::get('/reset-password', function () { return view('reset-password'); });

// Pusat Bantuan
Route::get('/pusat-bantuan', function () { return view('pusat-bantuan'); });

// Profile & Settings
Route::get('/profile', [PageController::class, 'profile']);
Route::get('/settings', function () { return view('settings'); });

// My Tickets (Pembeli)
Route::get('/my-tickets', [PageController::class, 'myTickets']);

// Static Pages
Route::get('/tentang-kami', function () { return view('tentang-kami'); });
Route::get('/hubungi-kami', function () { return view('hubungi-kami'); });
Route::get('/kebijakan-privasi', function () { return view('kebijakan-privasi'); });
Route::get('/syarat-ketentuan', function () { return view('syarat-ketentuan'); });
