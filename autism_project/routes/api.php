<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\API\SpecialistController;
use App\Http\Controllers\API\RegisterController;

Route::post('/login', [AuthenticatedSessionController::class, 'mobileLogin'])->name('mobile.login');
Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('mobile.logout');
Route::post('/register', [RegisterController::class, 'register']);
Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('specialist')->group(function () {

        Route::get('/dashboard', [SpecialistController::class, 'dashboard'])->name('mobile.specialist.dashboard');


        Route::get('/pendingRequests', [SpecialistController::class, 'getPendingRequests'])->name('mobile.specialist.pendingRequests');
        Route::post('/appointments/{id}/confirm', [SpecialistController::class, 'confirmAppointment'])->name('mobile.specialist.appointments.confirm');
        Route::post('/appointments/{id}/decline', [SpecialistController::class, 'declineAppointment'])->name('mobile.specialist.appointments.decline');


        Route::get('/upcoming-appointments', [SpecialistController::class, 'upcomingAppointments'])->name('mobile.specialist.upcoming-appointments');

        Route::get('/clients', [SpecialistController::class, 'getMyClients'])->name('mobile.specialist.clients');
        Route::get('/clients/{childId}', [SpecialistController::class, 'getClientDetails'])->name('mobile.specialist.clients.details');
        Route::put('/clients/{childId}/notes', [SpecialistController::class, 'updateSpecialistNotes'])->name('mobile.specialist.clients.notes');

        Route::get('/events', [SpecialistController::class, 'getUpcomingEvents'])->name('mobile.specialist.events');
        Route::post('/events/{eventId}/register', [SpecialistController::class, 'registerForEvent'])->name('mobile.specialist.events.register');
        Route::delete('/events/{eventId}/unregister', [SpecialistController::class, 'unregisterFromEvent'])->name('mobile.specialist.events.unregister');
    });
});
