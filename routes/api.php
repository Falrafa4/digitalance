<?php

use App\Http\Controllers\Api\AdministratorControllerApi;
use App\Http\Controllers\Api\AuthControllerApi;
use App\Http\Controllers\Api\ClientControllerApi;
use App\Http\Controllers\Api\FreelancerControllerApi;
use App\Http\Controllers\Api\OfferControllerApi;
use App\Http\Controllers\Api\OrderControllerApi;
use App\Http\Controllers\Api\PortofolioControllerApi;
use App\Http\Controllers\Api\ProfileControllerApi;
use App\Http\Controllers\Api\ServiceCategoryControllerApi;
use App\Http\Controllers\Api\ServiceControllerApi;
use App\Http\Controllers\Api\SkomdaStudentControllerApi;
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

        Route::get('/service-categories', [ServiceCategoryControllerApi::class, 'index']);
        Route::post('/service-categories', [ServiceCategoryControllerApi::class, 'store']);
        Route::get('/service-categories/{serviceCategory}', [ServiceCategoryControllerApi::class, 'show'])->whereNumber('serviceCategory');
        Route::patch('/service-categories/{serviceCategory}', [ServiceCategoryControllerApi::class, 'update'])->whereNumber('serviceCategory');
        Route::delete('/service-categories/{serviceCategory}', [ServiceCategoryControllerApi::class, 'destroy'])->whereNumber('serviceCategory');

        Route::get('/services', [ServiceControllerApi::class, 'index']);
        Route::post('/services', [ServiceControllerApi::class, 'store']);
        Route::post('/services/{service}/submit', [ServiceControllerApi::class, 'submit'])->whereNumber('service');
        Route::patch('/services/{service}/status', [ServiceControllerApi::class, 'updateStatus'])->whereNumber('service');
        Route::get('/services/{service}', [ServiceControllerApi::class, 'show'])->whereNumber('service');
        Route::patch('/services/{service}', [ServiceControllerApi::class, 'update'])->whereNumber('service');
        Route::delete('/services/{service}', [ServiceControllerApi::class, 'destroy'])->whereNumber('service');

        Route::get('/freelancers', [FreelancerControllerApi::class, 'index']);
        Route::post('/freelancers', [FreelancerControllerApi::class, 'store']);
        Route::get('/freelancers/{freelancer}/services', [FreelancerControllerApi::class, 'showServices'])->whereNumber('freelancer');
        Route::get('/freelancers/{freelancer}/portofolios', [PortofolioControllerApi::class, 'showAllFreelancerPortofolios'])->whereNumber('freelancer');
        Route::post('/freelancers/{freelancer}/verify', [FreelancerControllerApi::class, 'verify'])->whereNumber('freelancer');
        Route::post('/freelancers/{freelancer}/suspend', [FreelancerControllerApi::class, 'suspend'])->whereNumber('freelancer');
        Route::post('/freelancers/{freelancer}/unsuspend', [FreelancerControllerApi::class, 'unsuspend'])->whereNumber('freelancer');
        Route::get('/freelancers/{freelancer}', [FreelancerControllerApi::class, 'show'])->whereNumber('freelancer');
        Route::patch('/freelancers/{freelancer}', [FreelancerControllerApi::class, 'update'])->whereNumber('freelancer');
        Route::delete('/freelancers/{freelancer}', [FreelancerControllerApi::class, 'destroy'])->whereNumber('freelancer');

        Route::get('/portofolios', [PortofolioControllerApi::class, 'index']);
        Route::post('/portofolios', [PortofolioControllerApi::class, 'store']);
        Route::get('/portofolios/{portofolio}', [PortofolioControllerApi::class, 'show'])->whereNumber('portofolio');
        Route::patch('/portofolios/{portofolio}', [PortofolioControllerApi::class, 'update'])->whereNumber('portofolio');
        Route::delete('/portofolios/{portofolio}', [PortofolioControllerApi::class, 'destroy'])->whereNumber('portofolio');

        Route::get('/orders', [OrderControllerApi::class, 'index']);
        Route::post('/orders', [OrderControllerApi::class, 'store']);
        Route::post('/orders/{order}/attachments', [OrderControllerApi::class, 'uploadAttachment'])->whereNumber('order');
        Route::post('/orders/{order}/accept', [OrderControllerApi::class, 'accept'])->whereNumber('order');
        Route::post('/orders/{order}/reject', [OrderControllerApi::class, 'reject'])->whereNumber('order');
        Route::post('/orders/{order}/complete', [OrderControllerApi::class, 'clientComplete'])->whereNumber('order');
        Route::post('/orders/{order}/negotiations', [OrderControllerApi::class, 'clientNegotiate'])->whereNumber('order');
        Route::post('/orders/{order}/revision-requests', [OrderControllerApi::class, 'clientRequestRevision'])->whereNumber('order');
        Route::post('/orders/{order}/revision-requests/approve', [OrderControllerApi::class, 'freelancerApproveRevision'])->whereNumber('order');
        Route::post('/orders/{order}/revision-requests/reject', [OrderControllerApi::class, 'freelancerRejectRevision'])->whereNumber('order');
        Route::get('/orders/{order}/checkout', [OrderControllerApi::class, 'checkout'])->whereNumber('order');
        Route::post('/orders/{order}/checkout', [OrderControllerApi::class, 'processPayment'])->whereNumber('order');
        Route::patch('/orders/{order}/price', [OrderControllerApi::class, 'updateAgreedPrice'])->whereNumber('order');
        Route::get('/orders/{order}', [OrderControllerApi::class, 'show'])->whereNumber('order');
        Route::patch('/orders/{order}', [OrderControllerApi::class, 'updateStatus'])->whereNumber('order');
        Route::delete('/orders/{order}', [OrderControllerApi::class, 'destroy'])->whereNumber('order');

        Route::get('/offers', [OfferControllerApi::class, 'index']);
        Route::post('/offers', [OfferControllerApi::class, 'store']);
        Route::post('/offers/{offer}/accept', [OfferControllerApi::class, 'accept'])->whereNumber('offer');
        Route::post('/offers/{offer}/reject', [OfferControllerApi::class, 'reject'])->whereNumber('offer');
        Route::get('/offers/{offer}', [OfferControllerApi::class, 'show'])->whereNumber('offer');
        Route::patch('/offers/{offer}', [OfferControllerApi::class, 'update'])->whereNumber('offer');
        Route::delete('/offers/{offer}', [OfferControllerApi::class, 'destroy'])->whereNumber('offer');
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
