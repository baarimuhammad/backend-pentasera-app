<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetailOrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ETicketController;
use App\Http\Controllers\CheckinController;

# ───────────────────────────────────────────
# PUBLIC ROUTES (tanpa token)
# ───────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::patch('/users/{id}', [UserController::class, 'update']);

# Events & tickets publik (bisa dilihat tanpa login)
Route::get('/events',       [EventController::class, 'index']);
Route::get('/events/{id}',  [EventController::class, 'show']);
Route::get('/tickets',      [TicketController::class, 'index']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::get('/organizers',   [OrganizerController::class, 'index']);

# ───────────────────────────────────────────
# PROTECTED ROUTES (butuh token)
# ───────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    # Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    # Orders (buyer)
    Route::post('/orders',      [OrderController::class, 'store']);
    Route::get('/orders',       [OrderController::class, 'index']);
    Route::get('/orders/{id}',  [OrderController::class, 'show']);

    # Detail Orders
    Route::post('/detail-orders', [DetailOrderController::class, 'store']);
    Route::get('/detail-orders',  [DetailOrderController::class, 'index']);

    # Payments
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments',  [PaymentController::class, 'index']);

    # E-Tickets
    Route::post('/e-tickets', [ETicketController::class, 'store']);
    Route::get('/e-tickets',  [ETicketController::class, 'index']);

    # Checkins
    Route::post('/checkins', [CheckinController::class, 'store']);
    Route::get('/checkins',  [CheckinController::class, 'index']);

    # Creator/Admin only
    Route::post('/events',       [EventController::class, 'store']);
    Route::post('/organizers',   [OrganizerController::class, 'store']);
    Route::post('/tickets',      [TicketController::class, 'store']);

    # Admin only
    Route::get('/users',    [UserController::class, 'index']);
    Route::post('/users',   [UserController::class, 'store']);
});