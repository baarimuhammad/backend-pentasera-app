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
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\SettingsController;

# ───────────────────────────────────────────
# PUBLIC ROUTES (tanpa token)
# ───────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

# Email Verification
Route::post('/email/resend-verification', [AuthController::class, 'resendVerification']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyFromApi'])
    ->middleware('signed')
    ->name('api.verification.verify');

# Events & tickets publik (bisa dilihat tanpa login)
Route::get('/events',       [EventController::class, 'index']);
Route::get('/events/{event}',  [EventController::class, 'show']);
Route::get('/tickets',      [TicketController::class, 'index']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::get('/organizers',   [OrganizerController::class, 'index']);
Route::get('/organizers/{id}', [OrganizerController::class, 'show']);

# ───────────────────────────────────────────
# PROTECTED ROUTES (butuh token + email verified)
# ───────────────────────────────────────────
Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    # Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    # Orders (buyer)
    Route::post('/orders',      [OrderController::class, 'store']);
    Route::get('/orders',       [OrderController::class, 'index']);
    Route::get('/orders/{order}',  [OrderController::class, 'show']);

    # Transactions (buyer)
    Route::post('/transactions', [TransactionController::class, 'store'])
        ->middleware('role:buyer');
    Route::post('/orders/{id}/confirm-payment', [TransactionController::class, 'confirmPayment']);

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

    # My Orders & My Tickets (buyer)
    Route::get('/my-orders',    [MyOrderController::class, 'index']);
    Route::get('/my-orders/{id}', [MyOrderController::class, 'show']);
    Route::get('/my-tickets',   [MyOrderController::class, 'myTickets']);
    Route::get('/e-tickets/{id}', [MyOrderController::class, 'showETicket']);

    # Profile
    Route::patch('/profile',        [ProfileController::class, 'update']);
    Route::post('/profile/avatar',  [ProfileController::class, 'uploadAvatar']);

    # Checkin Scan
    Route::post('/checkin/scan',    [CheckinController::class, 'scan']);

    # Settings
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword']);
    Route::delete('/settings/account', [SettingsController::class, 'deleteAccount']);

    # ───────────────────────────────────────────
    # CREATOR ONLY (role:creator,admin)
    # ───────────────────────────────────────────
    Route::middleware('role:creator,admin')->group(function () {
        Route::post('/events',          [EventController::class, 'store']);
        Route::patch('/events/{id}',    [EventController::class, 'update']);
        Route::delete('/events/{id}',   [EventController::class, 'destroy']);
        Route::post('/tickets',         [TicketController::class, 'store']);
        Route::patch('/tickets/{id}',   [TicketController::class, 'update']);
        Route::delete('/tickets/{id}',  [TicketController::class, 'destroy']);
        
        // Organizers
        Route::post('/organizers',      [OrganizerController::class, 'store']);
        Route::patch('/organizers/{id}', [OrganizerController::class, 'update']);

        // Dashboard & Reports & Stats
        Route::get('/dashboard/stats',    [DashboardController::class, 'stats']);
        Route::get('/my-events',          [DashboardController::class, 'myEvents']);
        Route::get('/events/{id}/report', [DashboardController::class, 'eventReport']);
        Route::get('/events/{id}/stats',  [DashboardController::class, 'eventStats']);

        // Checkin lists per event
        Route::get('/events/{id}/checkins', [CheckinController::class, 'eventCheckins']);
    });

    # ───────────────────────────────────────────
    # ADMIN ONLY (role:admin)
    # ───────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/users',           [UserController::class, 'index']);
        Route::post('/users',          [UserController::class, 'store']);
        Route::patch('/users/{id}',    [UserController::class, 'update']);
    });
});
