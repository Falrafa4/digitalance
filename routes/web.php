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
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ── PUBLIC ──────────────────────────────────────────────
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/login', [PageController::class, 'login'])->name('login');
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
    Route::post('/verify-freelancer/{id}', [DashboardController::class, 'verifyFreelancer'])->name('verify');
    Route::post('/reject-freelancer/{id}', [DashboardController::class, 'rejectFreelancer'])->name('reject');

    // User
    Route::get('/user', [ClientController::class, 'index'])->name('user');

    // Administrators
    Route::get('/admins', [AdministratorController::class, 'index'])->name('admins');
    Route::get('/admins/{administrator}', [AdministratorController::class, 'show'])->name('admins.show');

    // Clients (CRUD)
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
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

    // Skomda Students (CRUD)
    Route::get('/skomda-students', [SkomdaStudentController::class, 'index'])->name('skomda-students.index');
    Route::get('/skomda-students/create', [SkomdaStudentController::class, 'create'])->name('skomda-students.create');
    Route::post('/skomda-students', [SkomdaStudentController::class, 'store'])->name('skomda-students.store');
    Route::get('/skomda-students/{skomda_student}', [SkomdaStudentController::class, 'show'])->name('skomda-students.show');
    Route::get('/skomda-students/{skomda_student}/edit', [SkomdaStudentController::class, 'edit'])->name('skomda-students.edit');
    Route::put('/skomda-students/{skomda_student}', [SkomdaStudentController::class, 'update'])->name('skomda-students.update');
    Route::delete('/skomda-students/{skomda_student}', [SkomdaStudentController::class, 'destroy'])->name('skomda-students.destroy');

    // Services
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

    // Service Categories
    Route::get('/service-categories', [ServiceCategoryController::class, 'index'])->name('service-categories.index');
    Route::get('/service-categories/create', [ServiceCategoryController::class, 'create'])->name('service-categories.create');
    Route::post('/service-categories', [ServiceCategoryController::class, 'store'])->name('service-categories.store');
    Route::get('/service-categories/{service_category}', [ServiceCategoryController::class, 'show'])->name('service-categories.show');
    Route::get('/service-categories/{service_category}/edit', [ServiceCategoryController::class, 'edit'])->name('service-categories.edit');
    Route::put('/service-categories/{service_category}', [ServiceCategoryController::class, 'update'])->name('service-categories.update');
    Route::delete('/service-categories/{service_category}', [ServiceCategoryController::class, 'destroy'])->name('service-categories.destroy');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
});

// ── CLIENT ───────────────────────────────────────────────
Route::middleware('auth:client')->prefix('client')->group(function () {
    Route::get('/', [DashboardController::class, 'client'])->name('client-dashboard');
    Route::get('/profile', [ClientController::class, 'profile'])->name('client-profile');
});

// ── FREELANCER ───────────────────────────────────────────
Route::middleware('auth:freelancer')->prefix('freelancer')->name('freelancer.')->group(function () {
    Route::get('/', [DashboardController::class, 'freelancer'])->name('dashboard');
    Route::get('/profile', [FreelancerController::class, 'profile'])->name('profile');

    // crud services
    Route::get('/services', [ServiceController::class, 'freelancerIndex'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
});
