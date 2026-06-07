<?php

use App\Http\Controllers\Api\AuthControllerApi;
use App\Http\Controllers\Api\AdministratorControllerApi;
use App\Http\Controllers\Api\ClientControllerApi;
use App\Http\Controllers\Api\FreelancerControllerApi;
use App\Http\Controllers\Api\SkomdaStudentControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json([
        'status' => true,
        'message' => 'OK',
    ]);
});

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register/client', [AuthControllerApi::class, 'registerClient']);
        Route::post('/register/freelancer', [AuthControllerApi::class, 'registerFreelancer']);
        Route::post('/login', [AuthControllerApi::class, 'login'])->middleware('throttle:5,1');
        Route::post('/logout', [AuthControllerApi::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/me', [AuthControllerApi::class, 'me'])->middleware('auth:sanctum');
    });

    Route::middleware(['auth:sanctum', 'role:administrator'])->group(function () {
        Route::get('/users', [ClientControllerApi::class, 'index']);
        Route::apiResource('/clients', ClientControllerApi::class)->only(['store', 'show', 'update', 'destroy']);
        Route::put('/clients/{id}/password', [ClientControllerApi::class, 'updateClientPassword']);
        Route::apiResource('/skomda-students', SkomdaStudentControllerApi::class);
        
        Route::put('/freelancers/{id}/password', [ClientControllerApi::class, 'updateFreelancerPassword']);
        Route::get('/freelancers/{freelancer}/services', [FreelancerControllerApi::class, 'showServices'])->whereNumber('freelancer');
        Route::post('/freelancers/{freelancer}/verify', [FreelancerControllerApi::class, 'verify'])->whereNumber('freelancer');
        Route::post('/freelancers/{freelancer}/suspend', [FreelancerControllerApi::class, 'suspend'])->whereNumber('freelancer');
        Route::post('/freelancers/{freelancer}/unsuspend', [FreelancerControllerApi::class, 'unsuspend'])->whereNumber('freelancer');
        Route::apiResource('/freelancers', FreelancerControllerApi::class)->whereNumber('freelancer');

        Route::put('/administrators/profile', [AdministratorControllerApi::class, 'updateProfile']);
        Route::put('/administrators/{administrator}/password', [AdministratorControllerApi::class, 'updateAdminPassword']);
        Route::put('/administrators/password', [AdministratorControllerApi::class, 'updatePassword']);
        Route::apiResource('/administrators', AdministratorControllerApi::class);
    });

    Route::middleware(['auth:sanctum', 'role:freelancer'])->group(function () {
        Route::get('/freelancers/clients', [ClientControllerApi::class, 'freelancerIndex']);
        Route::get('/freelancers/skomda-students', [SkomdaStudentControllerApi::class, 'freelancerIndex']);
        
        Route::get('/freelancers/profile', [FreelancerControllerApi::class, 'profile']);
        Route::put('/freelancers/profile', [FreelancerControllerApi::class, 'updateProfile']);
        Route::put('/freelancers/password', [FreelancerControllerApi::class, 'updatePassword']);
        Route::delete('/freelancers/account', [FreelancerControllerApi::class, 'deleteAccount']);
        Route::post('/freelancers/verification', [FreelancerControllerApi::class, 'applyForVerification']);
    });

    Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
        Route::get('/clients/profile', [ClientControllerApi::class, 'profile']);
        Route::put('/clients/profile', [ClientControllerApi::class, 'updateProfile']);
        Route::put('/clients/password', [ClientControllerApi::class, 'updatePassword']);
        Route::get('/talents', [FreelancerControllerApi::class, 'clientFindTalent']);
        Route::get('/talents/{freelancer}', [FreelancerControllerApi::class, 'clientTalentShow'])->whereNumber('freelancer');
    });
});
