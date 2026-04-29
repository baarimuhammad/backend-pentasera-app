<?php

use Illuminate\Support\Facades\Route;

// Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/auth', function () { return view('auth'); });

// Events
Route::get('/events', function () { return view('events'); });
Route::get('/order/{id}', function ($id) { return view('order', compact('id')); });
Route::get('/checkout', function () { return view('checkout'); });
Route::get('/payment', function () { return view('payment'); });

// Dashboard & Management (Creator Only — auth dibekukan sementara)
Route::get('/dashboard', function () { return view('dashboard'); });
Route::get('/my-events', function () { return view('my-events'); });
Route::get('/create-event', function () { return view('create-event'); });
Route::get('/manage-access', function () { return view('manage-access'); });
Route::get('/manage-event/{id}', function ($id) { return view('manage-event', compact('id')); });
Route::get('/event-report/{id}', function ($id) { return view('event-report', compact('id')); });

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
