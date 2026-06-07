<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class PageController
{
    public function home()
    {
        $showLogoutModal = false;
        $userRole = null;

        if (auth('administrator')->check()) {
            $showLogoutModal = true;
            $userRole = 'admin';
        } elseif (auth('client')->check()) {
            $showLogoutModal = true;
            $userRole = 'client';
        } elseif (auth('freelancer')->check()) {
            $showLogoutModal = true;
            $userRole = 'freelancer';
        }

        // Public stats for landing page
        $usersCount = Client::count() + Freelancer::count();
        $projectsCompleted = Order::where('status', 'Completed')->count();
        $totalTurnover = Transaction::where('status', 'Paid')->sum('amount');
        $avgRating = Review::avg('rating');
        $avgRatingFormatted = $avgRating ? number_format((float) $avgRating, 1) : '0.0';

        $testimonials = Review::with(['order.client', 'order.service.freelancer.skomda_student'])
            ->latest()
            ->take(6)
            ->get();

        return view('public.home', compact(
            'showLogoutModal',
            'userRole',
            'usersCount',
            'projectsCompleted',
            'totalTurnover',
            'avgRatingFormatted',
            'testimonials'
        ));
    }

    public function login()
    {
        $categories = ServiceCategory::pluck('name')->toArray();
        $students = SkomdaStudent::where('is_registered', false)
            ->whereDoesntHave('freelancer')
            ->select('id', 'nis', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('public.login', compact('categories', 'students'));
    }

    public function registerClient()
    {
        return view('auth.register-client');
    }

    public function registerFreelancer(): RedirectResponse
    {
        return redirect()->route('login')->with('show_register', true)->with('default_role', 'freelancer');
    }
}
