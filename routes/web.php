<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\LokerController;
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
Route::get('/services', [ServiceController::class, 'publicIndex'])->name('services.index');
Route::get('/login', [PageController::class, 'login'])->name('login')->middleware(['guest', 'throttle:5,1']);
Route::post('/login', [AuthController::class, 'login'])->name('login-process');
Route::get('/register-client', [PageController::class, 'registerClient'])->name('register-client');
Route::get('/register-freelancer', [PageController::class, 'registerFreelancer'])->name('register-freelancer');
Route::post('/register-client', [AuthController::class, 'registerClient'])->name('register-process');
Route::post('/register-freelancer', [AuthController::class, 'registerFreelancer'])->name('register-freelancer-process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── SHARED ──────────────────────────────────────────────
Route::middleware('auth:administrator,client,freelancer')->group(function () {
    Route::post('/notifications/mark-all-read', function () {
        $role = null;
        $user = null;

        if (auth('administrator')->check()) {
            $role = 'admin';
            $user = auth('administrator')->user();
        } elseif (auth('client')->check()) {
            $role = 'client';
            $user = auth('client')->user();
        } elseif (auth('freelancer')->check()) {
            $role = 'freelancer';
            $user = auth('freelancer')->user();
        }

        if ($role && $user) {
            \App\Models\Notification::where('role', $role)
                ->where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    })->name('notifications.mark-all-read');

    Route::post('/notifications/{notification}/keep', function (\App\Models\Notification $notification) {
        $request = request();
        $request->validate(['is_kept' => 'boolean']);
        $notification->update(['is_kept' => $request->is_kept]);
        return response()->json(['success' => true]);
    })->name('notifications.keep');

    // Polling endpoint for notification sync (used by frontend polling)
    Route::get('/notifications/poll', function () {
        $role = null;
        $user = null;

        if (auth('administrator')->check()) {
            $role = 'admin';
            $user = auth('administrator')->user();
        } elseif (auth('client')->check()) {
            $role = 'client';
            $user = auth('client')->user();
        } elseif (auth('freelancer')->check()) {
            $role = 'freelancer';
            $user = auth('freelancer')->user();
        }

        if (!$role || !$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $notifications = \App\Models\Notification::where('role', $role)
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'is_read' => (bool) $n->is_read,
                    'is_kept' => (bool) $n->is_kept,
                    'link' => $n->link,
                    'type' => $n->type,
                    'created_at' => optional($n->created_at)->diffForHumans(),
                ];
            });

        $unreadCount = $notifications->where('is_read', false)->count();

        return response()->json(['unread_count' => $unreadCount, 'notifications' => $notifications]);
    })->name('notifications.poll');
});

// ── ADMIN ────────────────────────────────────────────────
Route::middleware('auth:administrator')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/profile', [AdministratorController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdministratorController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AdministratorController::class, 'updatePassword'])->name('password.update');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::get('/search', [DashboardController::class, 'search'])->name('search');
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
    Route::put('/admins/{administrator}/password', [AdministratorController::class, 'updateAdminPassword'])->name('admins.password');

    // Clients (CRUD)
    Route::get('/users', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::put('/clients/{client}/password', [ClientController::class, 'updateClientPassword'])->name('clients.password');

    // Freelancers (CRUD) - No create/edit pages, handled via modal
    Route::get('/freelancers', [FreelancerController::class, 'index'])->name('freelancers.index');
    Route::post('/freelancers', [FreelancerController::class, 'store'])->name('freelancers.store');
    Route::get('/freelancers/{freelancer}', [FreelancerController::class, 'show'])->name('freelancers.show');
    Route::put('/freelancers/{freelancer}', [FreelancerController::class, 'update'])->name('freelancers.update');
    Route::delete('/freelancers/{freelancer}', [FreelancerController::class, 'destroy'])->name('freelancers.destroy');
    Route::put('/freelancers/{freelancer}/password', [ClientController::class, 'updateFreelancerPassword'])->name('freelancers.password');

    // Freelancers (Actions)
    Route::post('/freelancers/{freelancer}/verify', [FreelancerController::class, 'verify'])->name('freelancers.verify');
    Route::post('/freelancers/{freelancer}/suspend', [FreelancerController::class, 'suspend'])->name('freelancers.suspend');
    Route::post('/freelancers/{freelancer}/unsuspend', [FreelancerController::class, 'unsuspend'])->name('freelancers.unsuspend');

    // Skomda Students (CRUD) - No create/edit pages, handled via modal
    Route::get('/skomda-students', [SkomdaStudentController::class, 'index'])->name('skomda-students.index');
    Route::post('/skomda-students', [SkomdaStudentController::class, 'store'])->name('skomda-students.store');
    Route::get('/skomda-students/{skomda_student}', [SkomdaStudentController::class, 'show'])->name('skomda-students.show');
    Route::put('/skomda-students/{skomda_student}', [SkomdaStudentController::class, 'update'])->name('skomda-students.update');
    Route::delete('/skomda-students/{skomda_student}', [SkomdaStudentController::class, 'destroy'])->name('skomda-students.destroy');
    Route::put('/skomda-students/{skomda_student}/password', [ClientController::class, 'updateSkomdaPassword'])->name('skomda-students.password');

    // Results (CRUD)
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{result}', [ResultController::class, 'show'])->name('results.show');
    Route::delete('/results/{result}', [ResultController::class, 'destroy'])->name('results.destroy');

    // Services (CRUD)
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services/{id}/status', [ServiceController::class, 'updateStatus'])->name('services.updateStatus');

    // Service Categories (CRUD)
    Route::get('/service-categories', [ServiceCategoryController::class, 'index'])->name('service-categories.index');
    Route::post('/service-categories', [ServiceCategoryController::class, 'store'])->name('service-categories.store');
    Route::get('/service-categories/{service_category}', [ServiceCategoryController::class, 'show'])->name('service-categories.show');
    Route::put('/service-categories/{service_category}', [ServiceCategoryController::class, 'update'])->name('service-categories.update');
    Route::delete('/service-categories/{service_category}', [ServiceCategoryController::class, 'destroy'])->name('service-categories.destroy');

    // Portofolios (CRUD)
    Route::get('/portofolios', [PortofolioController::class, 'index'])->name('portofolios.index');
    Route::put('/portofolios/{id}', [PortofolioController::class, 'adminUpdate'])->name('portofolios.update');
    Route::delete('/portofolios/{id}', [PortofolioController::class, 'adminDestroy'])->name('portofolios.destroy');

    // Order (CRUD)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}/dispute', [DashboardController::class, 'getDisputeDetail'])->name('orders.dispute');
    Route::get('/freelancers/{id}/detail', [DashboardController::class, 'getFreelancerDetail'])->name('freelancers.detail');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/{order}/transfer', [\App\Http\Controllers\TransactionController::class, 'adminTransfer'])->name('orders.transfer');
    Route::get('/orders/{order}/payout', [\App\Http\Controllers\TransactionController::class, 'adminPayoutDetail'])->name('orders.payout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Offer (CRUD)
    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');

    // Transactions (CRUD)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');

    // Reviews (CRUD)
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Negotiations (CRUD)
    Route::get('/negotiations', [NegotiationController::class, 'index'])->name('negotiations.index');
});

// ── CLIENT ───────────────────────────────────────────────
Route::middleware('auth:client')->prefix('client')->name('client.')->group(function () {
    Route::get('/', [DashboardController::class, 'client'])->name('dashboard');
    Route::get('/profile', [ClientController::class, 'profile'])->name('profile');
    Route::put('/profile', [ClientController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [ClientController::class, 'updatePassword'])->name('password.update');
    Route::get('/settings', [ClientController::class, 'settings'])->name('settings');
    Route::get('/search', [DashboardController::class, 'clientSearch'])->name('search');

    // Service Categories
    Route::get('/service-categories', [ServiceCategoryController::class, 'clientIndex'])->name('service-categories.index');

    // Services
    Route::get('/services', [ServiceController::class, 'clientIndex'])->name('services.index');
    Route::get('/services/{service}', [ServiceController::class, 'clientShow'])->name('services.show');

    // Orders (PAGE)
    Route::get('/orders', [OrderController::class, 'clientIndexPage'])->name('orders.index');
    Route::get('/orders/create/{service}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'storePage'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'clientShowPage'])->name('orders.show');
    Route::post('/orders/{order}/attachments', [OrderController::class, 'uploadAttachment'])->name('orders.attachments.store');
    Route::post('/orders/{order}/accept', [OrderController::class, 'clientAcceptOrder'])->name('orders.accept');
    Route::post('/orders/{order}/reject', [OrderController::class, 'clientRejectOrder'])->name('orders.reject');
    Route::post('/orders/{order}/nego', [OrderController::class, 'clientNegoOrder'])->name('orders.nego');
    Route::post('/orders/{order}/revision', [OrderController::class, 'clientRequestRevision'])->name('orders.revision');
    Route::post('/orders/{order}/complete', [OrderController::class, 'clientCompleteOrder'])->name('orders.complete');
    Route::get('/orders/{order}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders/{order}/checkout', [OrderController::class, 'processPayment'])->name('orders.process-payment');

    // Results (Client)
    Route::get('/results', [ResultController::class, 'clientIndex'])->name('results.index');
    Route::get('/results/{result}', [ResultController::class, 'clientShow'])->name('results.show');

    // Talents (Freelancers)
    Route::get('/talents', [FreelancerController::class, 'clientFindTalent'])->name('talents.index');
    Route::get('/talents/{freelancer}', [FreelancerController::class, 'clientTalentShow'])->name('talents.show');

    // Projects
    Route::get('/projects', [OrderController::class, 'clientProjects'])->name('projects.index');

    // Messages (negotiations)
    Route::get('/messages', [NegotiationController::class, 'clientInbox'])->name('messages.index');
    Route::post('/messages/send', [NegotiationController::class, 'clientSendMessage'])->name('messages.send');

    // Payments
    Route::get('/payments', [TransactionController::class, 'clientIndex'])->name('payments.index');
    Route::get('/payments/order/{order}', [TransactionController::class, 'clientShowByOrderId'])->name('payments.show');

    // History
    Route::get('/history', [OrderController::class, 'clientHistory'])->name('history.index');

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'clientIndex'])->name('reviews.index');
    Route::get('/reviews/order/{orderId}', [ReviewController::class, 'clientShowByOrderId'])->name('reviews.showByOrderId');
    Route::get('/reviews/create/{orderId}', [ReviewController::class, 'clientCreate'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'clientStore'])->name('reviews.store');
    Route::delete('/reviews/{orderId}', [ReviewController::class, 'clientDestroy'])->name('reviews.destroy');

    // Offers
    Route::get('/offers', [OfferController::class, 'clientIndex'])->name('offers.index');
    Route::get('/offers/{offer}', [OfferController::class, 'clientShow'])->name('offers.show');
    Route::post('/offers/{offer}/accept', [OfferController::class, 'clientAccept'])->name('offers.accept');
    Route::post('/offers/{offer}/reject', [OfferController::class, 'clientReject'])->name('offers.reject');

    // Negotiations
    Route::post('/offers/{offer}/negotiations', [NegotiationController::class, 'clientStoreNegotiation'])->name('negotiations.store');

    // Portofolios
    Route::get('/freelancers/{freelancer_id}/portofolios', [PortofolioController::class, 'showAllFreelancerPortofolios'])->name('freelancers.portofolios');
    Route::get('/portofolios/{id}', [PortofolioController::class, 'showFreelancerPortofolio'])->name('portofolios.show');

    // Loker (Client)
    Route::get('/loker', [LokerController::class, 'clientIndex'])->name('loker.index');
    Route::get('/loker/create', [LokerController::class, 'clientCreate'])->name('loker.create');
    Route::post('/loker', [LokerController::class, 'clientStore'])->name('loker.store');
    Route::get('/loker/{loker}/edit', [LokerController::class, 'clientEdit'])->name('loker.edit');
    Route::put('/loker/{loker}', [LokerController::class, 'clientUpdate'])->name('loker.update');
    Route::delete('/loker/{loker}', [LokerController::class, 'clientDestroy'])->name('loker.destroy');
    Route::post('/loker/applications/{application}/approve', [LokerController::class, 'approveApplication'])->name('loker.applications.approve');
    Route::post('/loker/applications/{application}/reject', [LokerController::class, 'rejectApplication'])->name('loker.applications.reject');
});

// ── FREELANCER ───────────────────────────────────────────
Route::middleware('auth:freelancer')->prefix('freelancer')->name('freelancer.')->group(function () {
    Route::get('/', [DashboardController::class, 'freelancer'])->name('dashboard');
    // Alias URL (optional): /freelancer/dashboard -> same dashboard page
    Route::get('/dashboard', [DashboardController::class, 'freelancer'])->name('dashboard-alias');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::get('/profile', [FreelancerController::class, 'profile'])->name('profile');
    Route::get('/search', [DashboardController::class, 'freelancerSearch'])->name('search');
    Route::post('/profile', [FreelancerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password', [FreelancerController::class, 'updatePassword'])->name('password.update');
    Route::post('/delete', [FreelancerController::class, 'deleteAccount'])->name('delete');

    // crud skomda students
    Route::get('/skomda-students', [SkomdaStudentController::class, 'freelancerIndex'])->name('skomda-students.index');

    // crud service categories
    Route::get('/service-categories', [ServiceCategoryController::class, 'freelancerIndex'])->name('service-categories.index');

    // crud client
    Route::get('/clients', [ClientController::class, 'freelancerIndex'])->name('clients.index');

    // crud order
    Route::get('/orders', [OrderController::class, 'freelancerIndex'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'freelancerShow'])->name('orders.show');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatusFreelancer'])->name('orders.updateStatus');
    Route::patch('/orders/{id}/price', [OrderController::class, 'updateAgreedPrice'])->name('orders.updateAgreedPrice');
    Route::post('/orders/{order}/accept', [OrderController::class, 'freelancerAccept'])->name('orders.accept');
    Route::post('/orders/{order}/reject', [OrderController::class, 'freelancerReject'])->name('orders.reject');
    Route::post('/orders/{order}/revision/approve', [OrderController::class, 'freelancerApproveRevision'])->name('orders.revision.approve');
    Route::post('/orders/{order}/revision/reject', [OrderController::class, 'freelancerRejectRevision'])->name('orders.revision.reject');

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
    Route::post('/services/{id}/submit', [ServiceController::class, 'submit'])->name('services.submit');

    // crud portofolios
    Route::get('/portofolios', [PortofolioController::class, 'freelancerIndex'])->name('portofolios.index');
    Route::post('/portofolios', [PortofolioController::class, 'store'])->name('portofolios.store');
    Route::get('/portofolios/{portofolio}', [PortofolioController::class, 'show'])->name('portofolios.show');
    Route::put('/portofolios/{portofolio}', [PortofolioController::class, 'update'])->name('portofolios.update');
    Route::delete('/portofolios/{portofolio}', [PortofolioController::class, 'destroy'])->name('portofolios.destroy');

    // crud negotiations
    Route::get('/negotiations', [NegotiationController::class, 'freelancerGetMessages'])->name('negotiations.index');
    Route::get('/negotiations/{negotiation}', [NegotiationController::class, 'freelancerShowNegotiation'])->name('negotiations.show');
    Route::post('/negotiations', [NegotiationController::class, 'freelancerSendMessage'])->name('negotiations.send-message');
    Route::post('/negotiations/{negotiation}/accept', [NegotiationController::class, 'freelancerAcceptNegotiation'])->name('negotiations.accept');
    Route::post('/negotiations/{negotiation}/reject', [NegotiationController::class, 'freelancerRejectNegotiation'])->name('negotiations.reject');

    // crud results
    Route::get('/results', [ResultController::class, 'freelancerIndex'])->name('results.index');
    Route::post('/results/{order_id}', [ResultController::class, 'store'])->name('results.store');
    Route::get('/results/{result}', [ResultController::class, 'show'])->name('results.show');
    Route::put('/results/{result}', [ResultController::class, 'update'])->name('results.update');
    Route::delete('/results/{result}', [ResultController::class, 'destroy'])->name('results.destroy');
    // Route::resource('results', ResultController::class)->except(['index', 'create', 'edit']);

    // crud offers
    Route::get('/offers', [OfferController::class, 'freelancerIndex'])->name('offers.index');
    Route::post('/offers', [OfferController::class, 'freelancerStore'])->name('offers.store');
    Route::put('/offers/{offer}', [OfferController::class, 'freelancerUpdate'])->name('offers.update');

    // loker (freelancer)
    Route::get('/loker', [LokerController::class, 'freelancerIndex'])->name('loker.index');
    Route::get('/loker/{loker}', [LokerController::class, 'freelancerShow'])->name('loker.show');
    Route::post('/loker/{loker}/apply', [LokerController::class, 'freelancerApply'])->name('loker.apply');
    Route::get('/loker/my/applications', [LokerController::class, 'freelancerMyApplications'])->name('loker.my-applications');
    // Onboarding: freelancer applies for admin verification
    Route::post('/onboarding/apply', [FreelancerController::class, 'applyForVerification'])->name('onboarding.apply');
});
