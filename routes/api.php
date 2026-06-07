<?php

use App\Http\Controllers\Api\AdministratorControllerApi;
use App\Http\Controllers\Api\AuthControllerApi;
use App\Http\Controllers\Api\ClientControllerApi;
use App\Http\Controllers\Api\FreelancerControllerApi;
use App\Http\Controllers\Api\NegotiationControllerApi;
use App\Http\Controllers\Api\OfferControllerApi;
use App\Http\Controllers\Api\OrderControllerApi;
use App\Http\Controllers\Api\PortofolioControllerApi;
use App\Http\Controllers\Api\ProfileControllerApi;
use App\Http\Controllers\Api\ResultControllerApi;
use App\Http\Controllers\Api\ReviewControllerApi;
use App\Http\Controllers\Api\ServiceCategoryControllerApi;
use App\Http\Controllers\Api\ServiceControllerApi;
use App\Http\Controllers\Api\SkomdaStudentControllerApi;
use App\Http\Controllers\Api\TransactionControllerApi;
use Illuminate\Support\Facades\Route;

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

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/me', [ProfileControllerApi::class, 'show']);
        Route::patch('/me', [ProfileControllerApi::class, 'update']);
        Route::patch('/me/password', [ProfileControllerApi::class, 'updatePassword']);

        Route::apiResource('service-categories', ServiceCategoryControllerApi::class);
        Route::apiResource('services', ServiceControllerApi::class);

        Route::post('services/{service}/submit', [ServiceControllerApi::class, 'submit']);

        Route::apiResource('freelancers', FreelancerControllerApi::class);
        Route::get('freelancers/{freelancer}/services', [FreelancerControllerApi::class, 'showServices']);
        Route::get('freelancers/{freelancer}/portfolios', [PortofolioControllerApi::class, 'showAllFreelancerPortofolios']);
        Route::post('freelancers/{freelancer}/verify', [FreelancerControllerApi::class, 'verify']);
        Route::post('freelancers/{freelancer}/suspend', [FreelancerControllerApi::class, 'suspend']);
        Route::post('freelancers/{freelancer}/unsuspend', [FreelancerControllerApi::class, 'unsuspend']);

        Route::apiResource('portfolios', PortofolioControllerApi::class);

        Route::apiResource('orders', OrderControllerApi::class);
        Route::post('orders/{order}/attachments', [OrderControllerApi::class, 'uploadAttachment']);
        Route::post('orders/{order}/accept', [OrderControllerApi::class, 'accept']);
        Route::post('orders/{order}/reject', [OrderControllerApi::class, 'reject']);
        Route::post('orders/{order}/complete', [OrderControllerApi::class, 'clientComplete']);
        Route::post('orders/{order}/negotiations', [OrderControllerApi::class, 'clientNegotiate']);
        Route::post('orders/{order}/revision-requests', [OrderControllerApi::class, 'clientRequestRevision']);
        Route::post('orders/{order}/revision-requests/approve', [OrderControllerApi::class, 'freelancerApproveRevision']);
        Route::post('orders/{order}/revision-requests/reject', [OrderControllerApi::class, 'freelancerRejectRevision']);
        Route::get('orders/{order}/checkout', [OrderControllerApi::class, 'checkout']);
        Route::post('orders/{order}/checkout', [OrderControllerApi::class, 'processPayment']);
        Route::patch('orders/{order}/price', [OrderControllerApi::class, 'updateAgreedPrice']);

        Route::apiResource('offers', OfferControllerApi::class);
        Route::post('offers/{offer}/accept', [OfferControllerApi::class, 'accept']);
        Route::post('offers/{offer}/reject', [OfferControllerApi::class, 'reject']);

        Route::apiResource('negotiations', NegotiationControllerApi::class);
        Route::post('negotiations/{negotiation}/accept', [NegotiationControllerApi::class, 'accept']);
        Route::post('negotiations/{negotiation}/reject', [NegotiationControllerApi::class, 'reject']);

        Route::apiResource('results', ResultControllerApi::class);
        Route::apiResource('reviews', ReviewControllerApi::class);
        Route::apiResource('transactions', TransactionControllerApi::class);
    });

    Route::middleware(['auth:sanctum', 'role:administrator'])->group(function () {
        Route::get('/users', [ClientControllerApi::class, 'index']);

        Route::apiResource('/clients', ClientControllerApi::class)->only(['store', 'show', 'update', 'destroy']);
        Route::patch('/clients/{id}/password', [ClientControllerApi::class, 'updateClientPassword'])->whereNumber('id');
        Route::patch('/freelancers/{id}/password', [ClientControllerApi::class, 'updateFreelancerPassword'])->whereNumber('id');

        Route::apiResource('/skomda-students', SkomdaStudentControllerApi::class);

        Route::patch('/administrators/{administrator}/password', [AdministratorControllerApi::class, 'updateAdminPassword'])->whereNumber('administrator');
        Route::apiResource('/administrators', AdministratorControllerApi::class);
    });
});
