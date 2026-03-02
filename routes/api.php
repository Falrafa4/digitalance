<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SkomdaStudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register-client', [AuthController::class, 'register_client']);
    Route::post('/register-freelancer', [AuthController::class, 'register_freelancer']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

// Administrator
Route::middleware('auth:sanctum', 'role:administrator')->group(function () {
    Route::apiResource('administrators', AdministratorController::class)->only(['index', 'show']);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('freelancers', FreelancerController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('skomda-students', SkomdaStudentController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('service-categories', ServiceCategoryController::class)->only(['store', 'update', 'destroy']);
});

// Client
Route::middleware('auth:sanctum', 'role:client')->group(function () {
    Route::put('/clients/profile', [ClientController::class, 'update_profile']);
    Route::put('/clients/password', [ClientController::class, 'update_password']);
    Route::apiResource('clients', ClientController::class)->only(['show', 'update']);
});

// Freelancer
Route::middleware('auth:sanctum', 'role:freelancer')->group(function () {
    Route::put('/freelancers/profile', [FreelancerController::class, 'update_profile']);
    Route::put('/freelancers/password', [FreelancerController::class, 'update_password']);
    // Route::apiResource('freelancers', FreelancerController::class)->only(['show', 'update']);
});

// MULTIPLE ROLES
// Administrator + Freelancer
Route::middleware('auth:sanctum', 'role:administrator,freelancer')->group(function () {
    Route::apiResource('service-categories', ServiceCategoryController::class)->only(['index', 'show']);
    Route::apiResource('services', ServiceController::class)->only(['store', 'update', 'destroy']);
});

// Administrator + Client
Route::middleware('auth:sanctum', 'role:administrator,client')->group(function () {
    Route::apiResource('freelancers', FreelancerController::class)->only(['index', 'show']);
    Route::get('freelancers/{id}/services', [FreelancerController::class, 'show_services']);
});

// Administrator + Client + Freelancer
Route::middleware('auth:sanctum', 'role:administrator,client,freelancer')->group(function () {
    Route::apiResource('services', ServiceController::class)->only(['index', 'show']);
});