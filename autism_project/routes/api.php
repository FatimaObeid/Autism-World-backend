<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\API\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\AuthenticatedSessionController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\API\ChildController;
use App\Http\Controllers\API\ParentProfileController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\SpecialistController;
use App\Http\Controllers\API\VolunteerController;
use App\Http\Middleware\EnsureSpecialistIsApproved;

Route::post('/register', [RegisterController::class, 'register'])->name('mobile.register');
Route::post('/login', [AuthenticatedSessionController::class, 'mobileLogin'])->name('mobile.login');
Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('mobile.logout');
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('parent')->group(function () {

        Route::get('/dashboard', [ParentProfileController::class, 'dashboard'])->name('mobile.parent.dashboard');
        Route::post('/appointments', [ParentProfileController::class, 'bookAppointment']);
        Route::get('/specialists', [ParentProfileController::class, 'specialists']);
        Route::get('/resources', [ParentProfileController::class, 'resources']);
        Route::post('/daily-progress', [ParentProfileController::class, 'dailyProgress']);
        Route::get('/workshops', [ParentProfileController::class, 'workshops']);
        Route::post('/workshops/{id}/approve-attendance', [ParentProfileController::class, 'approveAttendance']);
        Route::get('/children', [ChildController::class, 'dashboard']);
        Route::post('/children', [ChildController::class, 'storeChild']);
    });
    Route::prefix('specialist')->middleware([EnsureSpecialistIsApproved::class])->group(function () {

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
    Route::prefix('volunteer')->group(function () {
        Route::get('/dashboard', [VolunteerController::class, 'dashboard'])->name('mobile.volunteer.dashboard');
        Route::post('/workshops', [VolunteerController::class, 'addWorkshop'])->name('mobile.volunteer.workshops.add');
    });
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'getDashboardData'])->name('mobile.admin.dashboard');
        Route::post('/volunteers', [AdminController::class, 'saveVolunteer'])->name('mobile.admin.volunteers.add');
        Route::put('/volunteers/{id}', [AdminController::class, 'updateVolunteer'])->name('mobile.admin.volunteers.update');
        Route::delete('/volunteers/{id}', [AdminController::class, 'deleteVolunteer'])->name('mobile.admin.volunteers.delete');
        //specialist management
        Route::post('/specialists', [AdminController::class, 'saveSpecialist'])->name('mobile.admin.specialists.add');
        Route::put('/specialists/{id}', [AdminController::class, 'updateSpecialist'])->name('mobile.admin.specialists.update');
        Route::delete('/specialists/{id}', [AdminController::class, 'deleteSpecialist'])->name('mobile.admin.specialists.delete');
        Route::patch('/specialists/{id}/approve', [AdminController::class, 'approveSpecialist'])->name('mobile.admin.specialists.approve');
        Route::patch('/specialists/{id}/decline', [AdminController::class, 'declineSpecialist'])->name('mobile.admin.specialists.decline');
        //parent management
        Route::post('/parents', [AdminController::class, 'saveParent'])->name('mobile.admin.parents.add');
        Route::put('/parents/{id}', [AdminController::class, 'updateParent'])->name('mobile.admin.parents.update');
        Route::delete('/parents/{id}', [AdminController::class, 'deleteParent'])->name('mobile.admin.parents.delete');

        //  Workshop Pending Approvals 
        Route::patch('/workshops/{id}/approve', [AdminController::class, 'approveWorkshop'])->name('mobile.admin.workshops.approve');
        Route::patch('/workshops/{id}/decline', [AdminController::class, 'declineWorkshop'])->name('mobile.admin.workshops.decline');

        //resource management
        Route::post('/resources', [AdminController::class, 'saveResource'])->name('mobile.admin.resources.add');
        Route::put('/resources/{id}', [AdminController::class, 'updateResource'])->name('mobile.admin.resources.update');
        Route::delete('/resources/{id}', [AdminController::class, 'deleteResource'])->name('mobile.admin.resources.delete');
    });


    Route::get('/profile', [SettingsController::class, 'getProfile'])->name('mobile.profile.get');
    Route::put('/profile/update', [SettingsController::class, 'updateProfile'])->name('mobile.profile.update');
    Route::post('/profile/change-password', [SettingsController::class, 'changePassword'])->name('mobile.profile.change-password');

    // Chat routes — bidirectional, accessible by both specialists and parents
    Route::get('/messages/parent/{parentId}', [ChatController::class, 'index']);          // specialist fetches thread
    Route::get('/messages/specialist/{specialistId}', [ChatController::class, 'indexForParent']); // parent fetches thread
    Route::post('/messages', [ChatController::class, 'store']);                            // send (role-aware)

    Route::post('/chat', [AiChatController::class, 'chat']);
});
