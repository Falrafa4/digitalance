<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SkomdaStudentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Main Page
Route::get('/', [PageController::class, 'home']);
Route::get('/login', [PageController::class, 'login']);

// Dashboard Admin
Route::middleware('auth:administrator')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'admin']);
        Route::get('/profile', [AdministratorController::class, 'profile']);
        Route::resource('administrators', AdministratorController::class)->only(['index', 'show']);
        Route::resource('clients', ClientController::class);
        Route::resource('freelancers', FreelancerController::class);
        Route::resource('skomda-students', SkomdaStudentController::class);
        Route::resource('services', ServiceController::class);
        Route::resource('service-categories', ServiceCategoryController::class);
        Route::resource('transactions', TransactionController::class);
    });
});
