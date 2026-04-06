<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\NegotiationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SkomdaStudentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// ── PUBLIC ──────────────────────────────────────────────
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/login', [PageController::class, 'login'])->name('login')->middleware(['guest', 'throttle:5,1']);
Route::post('/login', [AuthController::class, 'login'])->name('login-process');
Route::get('/register-client', [PageController::class, 'registerClient']);
Route::get('/register-freelancer', [PageController::class, 'registerFreelancer']);
Route::post('/register-client', [AuthController::class, 'register_client'])->name('register-process');
Route::post('/register-freelancer', [AuthController::class, 'register_freelancer'])->name('register-freelancer-process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── ADMIN ────────────────────────────────────────────────
Route::middleware('auth:administrator')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/profile', [AdministratorController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdministratorController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AdministratorController::class, 'updatePassword'])->name('password.update');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/verify-freelancer/{id}', [DashboardController::class, 'verifyFreelancer'])->name('verify');
    Route::post('/reject-freelancer/{id}', [DashboardController::class, 'rejectFreelancer'])->name('reject');

    // User
    Route::get('/user', [ClientController::class, 'index'])->name('user');

    // Administrators
    Route::get('/admins', [AdministratorController::class, 'index'])->name('admins.index');
    Route::get('/admins/{administrator}', [AdministratorController::class, 'show'])->name('admins.show');
    Route::post('/admins', [AdministratorController::class, 'store'])->name('admins.store');
    Route::put('/admins/{administrator}', [AdministratorController::class, 'update'])->name('admins.update');
    Route::delete('/admins/{administrator}', [AdministratorController::class, 'destroy'])->name('admins.destroy');

    // Clients (CRUD)
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

        // Freelancers (CRUD)
    Route::get('/freelancers', [FreelancerController::class, 'index'])->name('freelancers.index');
    Route::get('/freelancers/create', [FreelancerController::class, 'create'])->name('freelancers.create');
    Route::post('/freelancers', [FreelancerController::class, 'store'])->name('freelancers.store');
    Route::get('/freelancers/{freelancer}', [FreelancerController::class, 'show'])->name('freelancers.show');
    Route::get('/freelancers/{freelancer}/edit', [FreelancerController::class, 'edit'])->name('freelancers.edit');
    Route::put('/freelancers/{freelancer}', [FreelancerController::class, 'update'])->name('freelancers.update');
    Route::delete('/freelancers/{freelancer}', [FreelancerController::class, 'destroy'])->name('freelancers.destroy');

    // Freelancers (Actions)
    Route::post('/freelancers/{freelancer}/verify', [FreelancerController::class, 'verify'])->name('admin.freelancers.verify');
Route::post('/freelancers/{freelancer}/suspend', [FreelancerController::class, 'suspend'])->name('admin.freelancers.suspend');
Route::post('/freelancers/{freelancer}/unsuspend', [FreelancerController::class, 'unsuspend'])->name('admin.freelancers.unsuspend');

    // Skomda Students (CRUD)
    Route::get('/skomda-students', [SkomdaStudentController::class, 'index'])->name('skomda-students.index');
    Route::get('/skomda-students/create', [SkomdaStudentController::class, 'create'])->name('skomda-students.create');
    Route::post('/skomda-students', [SkomdaStudentController::class, 'store'])->name('skomda-students.store');
    Route::get('/skomda-students/{skomda_student}', [SkomdaStudentController::class, 'show'])->name('skomda-students.show');
    Route::get('/skomda-students/{skomda_student}/edit', [SkomdaStudentController::class, 'edit'])->name('skomda-students.edit');
    Route::put('/skomda-students/{skomda_student}', [SkomdaStudentController::class, 'update'])->name('skomda-students.update');
    Route::delete('/skomda-students/{skomda_student}', [SkomdaStudentController::class, 'destroy'])->name('skomda-students.destroy');

    // Services (CRUD)
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/admin/services/{id}/status', [App\Http\Controllers\ServiceController::class, 'updateStatus'])->name('admin.services.updateStatus');

    // Service Categories (CRUD)
    Route::get('/service-categories', [ServiceCategoryController::class, 'index'])->name('service-categories.index');
    Route::post('/service-categories', [ServiceCategoryController::class, 'store'])->name('service-categories.store');
    Route::get('/service-categories/{service_category}', [ServiceCategoryController::class, 'show'])->name('service-categories.show');
    Route::put('/service-categories/{service_category}', [ServiceCategoryController::class, 'update'])->name('service-categories.update');
    Route::delete('/service-categories/{service_category}', [ServiceCategoryController::class, 'destroy'])->name('service-categories.destroy');

    // Portofolios (CRUD)
    Route::get('/portofolios', [PortofolioController::class, 'index'])->name('portofolios.index');

    // Order (CRUD)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Offer (CRUD)
    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');

    // Transactions (CRUD)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Results (CRUD)
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');

    // Reviews (CRUD)
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

    // Negotiations (CRUD)
    Route::get('/negotiations', [NegotiationController::class, 'index'])->name('negotiations.index');
});

// ── CLIENT ───────────────────────────────────────────────
Route::middleware('auth:client')->prefix('client')->name('client.')->group(function () {
    Route::get('/', [DashboardController::class, 'client'])->name('dashboard');
    Route::get('/profile', [ClientController::class, 'profile'])->name('profile');
});

// ── FREELANCER ───────────────────────────────────────────
Route::middleware('auth:freelancer')->prefix('freelancer')->name('freelancer.')->group(function () {
    Route::get('/', [DashboardController::class, 'freelancer'])->name('dashboard');
    Route::get('/profile', [FreelancerController::class, 'profile'])->name('profile');

    // crud skomda students
    Route::get('/skomda-students', [SkomdaStudentController::class, 'freelancerIndex'])->name('skomda-students.index');

    // crud service categories
    Route::get('/service-categories', [ServiceCategoryController::class, 'freelancerIndex'])->name('service-categories.index');

    // crud client
    Route::get('/clients', [ClientController::class, 'freelancerIndex'])->name('clients.index');

    // crud order
    Route::get('/orders', [OrderController::class, 'freelancerIndex'])->name('orders.index');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{id}/price', [OrderController::class, 'updateAgreedPrice'])->name('orders.updateAgreedPrice');

    // crud review
    Route::get('/reviews', [ReviewController::class, 'freelancerIndex'])->name('reviews.index');
    Route::get('/reviews/order/{orderId}', [ReviewController::class, 'showReviewByOrderId'])->name('reviews.showByOrderId');

    // crud transaction
    Route::get('/transactions', [TransactionController::class, 'freelancerIndex'])->name('transactions.index');
    Route::get('/transactions/order/{orderId}', [TransactionController::class, 'showTransactionByOrderId'])->name('transactions.showByOrderId');

    // crud services
    Route::get('/services', [ServiceController::class, 'freelancerIndex'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // crud portofolios
    Route::get('/portofolios', [PortofolioController::class, 'freelancerIndex'])->name('portofolios.index');
    Route::post('/portofolios', [PortofolioController::class, 'store'])->name('portofolios.store');
    Route::get('/portofolios/{portofolio}', [PortofolioController::class, 'show'])->name('portofolios.show');
    Route::put('/portofolios/{portofolio}', [PortofolioController::class, 'update'])->name('portofolios.update');
    Route::delete('/portofolios/{portofolio}', [PortofolioController::class, 'destroy'])->name('portofolios.destroy');
});
