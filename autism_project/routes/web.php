<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ParentProfileController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\HomeController;
  


Route::get('/',[HomeController::class,'index'])->name('home');
Route::get('/login',[AuthenticatedSessionController::class,'showloginform'])->name('login');
Route::post('/login',[AuthenticatedSessionController::class,'login'])->name('login.submit');
Route::get('/register',[RegisteredUserController::class,'showRegisterForm'])->name('register');
Route::post('/register',[RegisteredUserController::class,'register'])->name('register.submit');
Route::middleware(['auth'])->group(function(){
Route::post('/logout',[AuthenticatedSessionController::class,'logout'])->name('logout');
});



Route::middleware(['auth','role:parent'])->prefix('parent')->group(function(){
Route::get('/dashboard',[ParentProfileController::class,'dashboard'])->name('parent.dashboard');
Route::post('/appointments',[ParentProfileController::class,'storeAppointment'])->name('parent.appointments.store');
Route::put('/appointments/{id}',[ParentProfileController::class,'updateAppointment'])->name('parents.appoitments.update');
Route::delete('/appointments/{id}',[ParentProfileController::class,'deleteAppointment'])->name('parent.appointments.delete');
});


Route::middleware(['auth','role:specialist'])->prefix('specialist')->group(function(){
Route::get('/dashboard',[SpecialistController::class,'dashboard'])->name('specialist.dashboard');
Route::post('/appointments/{id}/confirm',[SpecialistController::class,'confirmAppointment'])->name('specialist.appointments.confirm');
Route::post('/appointments/{id}/decline',[SpecialistController::class,'declineAppointment'])->name('specialist.appointments.decline');
});

Route::middleware(['auth','role:admin'])->prefix('admin')->group(function(){
Route::get('/dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');
Route::post('/appointments',[AdminController::class,'saveSpecialist'])->name('admin.specialists.save');
Route::put('/appointments/{id}',[AdminController::class,'updateSpecialist'])->name('admin.specialists.update');
Route::delete('/appointments/{id}',[AdminController::class,'deleteSpecialist'])->name('admin.specialists.delete');



Route::post('/appointments',[AdminController::class,'saveParent'])->name('admin.parents.save');
Route::put('/appointments/{id}',[AdminController::class,'updateParent'])->name('admin.parents.update');
Route::delete('/appointments/{id}',[AdminController::class,'deleteParent'])->name('admin.parents.delete');
});

Route::middleware(['auth','role:parent'])->prefix('child')->group(function(){
Route::get('/dashboard',[ChildController::class,'dashboard'])->name('child.dashboard');
Route::post('/create',[ChildController::class,'storeChild'])->name('child.store');
Route::put('/update/{id}', [ChildController::class,'update'])->name('child.update');

});

require __DIR__.'/auth.php';
