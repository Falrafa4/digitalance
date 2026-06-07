<?php

use App\Http\Controllers\Api\AuthControllerApi;
use App\Http\Controllers\Api\AdministratorControllerApi;
use App\Http\Controllers\Api\ClientControllerApi;
use App\Http\Controllers\Api\FreelancerControllerApi;
use App\Http\Controllers\Api\OrderControllerApi;
use App\Http\Controllers\Api\PortofolioControllerApi;
use App\Http\Controllers\Api\ServiceCategoryControllerApi;
use App\Http\Controllers\Api\ServiceControllerApi;
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

    Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
        Route::patch('/clients/profile', [ClientControllerApi::class, 'updateProfile']);
        Route::patch('/clients/password', [ClientControllerApi::class, 'updatePassword']);

        Route::get('/service-catalog', [ServiceControllerApi::class, 'catalog']);
        Route::get('/service-catalog/{service}', [ServiceControllerApi::class, 'clientShow'])->whereNumber('service');
        Route::get('/client/service-categories', [ServiceCategoryControllerApi::class, 'clientIndex']);

        Route::get('/talents', [FreelancerControllerApi::class, 'clientFindTalent']);
        Route::get('/talents/{freelancer}', [FreelancerControllerApi::class, 'clientTalentShow'])->whereNumber('freelancer');

        Route::get('/talents/{freelancer}/portofolios', [PortofolioControllerApi::class, 'showAllFreelancerPortofolios'])->whereNumber('freelancer');
        Route::get('/client/portofolios/{portofolio}', [PortofolioControllerApi::class, 'showFreelancerPortofolio'])->whereNumber('portofolio');

        Route::get('/clients/orders', [OrderControllerApi::class, 'clientIndex']);
        Route::post('/clients/orders', [OrderControllerApi::class, 'clientStore']);
        Route::get('/clients/orders/{order}', [OrderControllerApi::class, 'clientShow'])->whereNumber('order');
        Route::post('/clients/orders/{order}/attachments', [OrderControllerApi::class, 'uploadAttachment'])->whereNumber('order');
        Route::post('/clients/orders/{order}/accept', [OrderControllerApi::class, 'clientAccept'])->whereNumber('order');
        Route::get('/clients/orders/{order}/checkout', [OrderControllerApi::class, 'checkout'])->whereNumber('order');
        Route::post('/clients/orders/{order}/checkout', [OrderControllerApi::class, 'processPayment'])->whereNumber('order');
        Route::post('/clients/orders/{order}/reject', [OrderControllerApi::class, 'clientReject'])->whereNumber('order');
        Route::post('/clients/orders/{order}/nego', [OrderControllerApi::class, 'clientNegotiate'])->whereNumber('order');
        Route::post('/clients/orders/{order}/revision', [OrderControllerApi::class, 'clientRequestRevision'])->whereNumber('order');
        Route::post('/clients/orders/{order}/complete', [OrderControllerApi::class, 'clientComplete'])->whereNumber('order');
    });

    Route::middleware(['auth:sanctum', 'role:freelancer'])->group(function () {
        Route::get('/freelancers/clients', [ClientControllerApi::class, 'freelancerIndex']);
        Route::get('/freelancers/skomda-students', [SkomdaStudentControllerApi::class, 'freelancerIndex']);

        Route::get('/freelancers/profile', [FreelancerControllerApi::class, 'profile']);
        Route::put('/freelancers/profile', [FreelancerControllerApi::class, 'updateProfile']);
        Route::put('/freelancers/password', [FreelancerControllerApi::class, 'updatePassword']);
        Route::delete('/freelancers/account', [FreelancerControllerApi::class, 'deleteAccount']);
        Route::post('/freelancers/verification', [FreelancerControllerApi::class, 'applyForVerification']);

        Route::get('/freelancers/services', [ServiceControllerApi::class, 'freelancerIndex']);
        Route::post('/freelancers/services', [ServiceControllerApi::class, 'store']);
        Route::get('/freelancers/services/{service}', [ServiceControllerApi::class, 'show'])->whereNumber('service');
        Route::put('/freelancers/services/{service}', [ServiceControllerApi::class, 'update'])->whereNumber('service');
        Route::delete('/freelancers/services/{service}', [ServiceControllerApi::class, 'destroy'])->whereNumber('service');
        Route::post('/freelancers/services/{service}/submit', [ServiceControllerApi::class, 'submit'])->whereNumber('service');
        Route::get('/freelancers/service-categories', [ServiceCategoryControllerApi::class, 'freelancerIndex']);

        Route::get('/freelancers/portofolios', [PortofolioControllerApi::class, 'freelancerIndex']);
        Route::post('/freelancers/portofolios', [PortofolioControllerApi::class, 'store']);
        Route::get('/freelancers/portofolios/{portofolio}', [PortofolioControllerApi::class, 'show'])->whereNumber('portofolio');
        Route::put('/freelancers/portofolios/{portofolio}', [PortofolioControllerApi::class, 'update'])->whereNumber('portofolio');
        Route::delete('/freelancers/portofolios/{portofolio}', [PortofolioControllerApi::class, 'destroy'])->whereNumber('portofolio');

        Route::get('/freelancers/orders', [OrderControllerApi::class, 'freelancerIndex']);
        Route::get('/freelancers/orders/{order}', [OrderControllerApi::class, 'freelancerShow'])->whereNumber('order');
        Route::patch('/freelancers/orders/{order}/status', [OrderControllerApi::class, 'updateStatusFreelancer'])->whereNumber('order');
        Route::patch('/freelancers/orders/{order}/price', [OrderControllerApi::class, 'updateAgreedPrice'])->whereNumber('order');
        Route::post('/freelancers/orders/{order}/accept', [OrderControllerApi::class, 'freelancerAccept'])->whereNumber('order');
        Route::post('/freelancers/orders/{order}/reject', [OrderControllerApi::class, 'freelancerReject'])->whereNumber('order');
        Route::post('/freelancers/orders/{order}/revision/approve', [OrderControllerApi::class, 'freelancerApproveRevision'])->whereNumber('order');
        Route::post('/freelancers/orders/{order}/revision/reject', [OrderControllerApi::class, 'freelancerRejectRevision'])->whereNumber('order');
    });

    Route::middleware(['auth:sanctum', 'role:administrator'])->group(function () {
        Route::get('/users', [ClientControllerApi::class, 'index']);
        Route::apiResource('/clients', ClientControllerApi::class)->only(['store', 'show', 'update', 'destroy']);
        Route::patch('/clients/{id}/password', [ClientControllerApi::class, 'updateClientPassword']);
        Route::apiResource('/skomda-students', SkomdaStudentControllerApi::class);
        
        Route::get('/services', [ServiceControllerApi::class, 'index']);
        Route::get('/services/{service}', [ServiceControllerApi::class, 'adminShow'])->whereNumber('service');
        Route::post('/services/{service}/status', [ServiceControllerApi::class, 'updateStatus'])->whereNumber('service');
        
        Route::get('/service-categories', [ServiceCategoryControllerApi::class, 'index']);
        Route::post('/service-categories', [ServiceCategoryControllerApi::class, 'store']);
        Route::get('/service-categories/{serviceCategory}', [ServiceCategoryControllerApi::class, 'show'])->whereNumber('serviceCategory');
        Route::put('/service-categories/{serviceCategory}', [ServiceCategoryControllerApi::class, 'update'])->whereNumber('serviceCategory');
        Route::delete('/service-categories/{serviceCategory}', [ServiceCategoryControllerApi::class, 'destroy'])->whereNumber('serviceCategory');
        
        Route::get('/portofolios', [PortofolioControllerApi::class, 'index']);
        Route::get('/portofolios/{portofolio}', [PortofolioControllerApi::class, 'adminShow'])->whereNumber('portofolio');
        Route::put('/portofolios/{portofolio}', [PortofolioControllerApi::class, 'adminUpdate'])->whereNumber('portofolio');
        Route::delete('/portofolios/{portofolio}', [PortofolioControllerApi::class, 'adminDestroy'])->whereNumber('portofolio');

        Route::get('/orders', [OrderControllerApi::class, 'index']);
        Route::post('/orders', [OrderControllerApi::class, 'store']);
        Route::get('/orders/{order}', [OrderControllerApi::class, 'show'])->whereNumber('order');
        Route::post('/orders/{order}/status', [OrderControllerApi::class, 'updateStatus'])->whereNumber('order');
        Route::delete('/orders/{order}', [OrderControllerApi::class, 'destroy'])->whereNumber('order');
        
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

});
