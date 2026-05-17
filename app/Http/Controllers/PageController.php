<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
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

        return view('public.home', compact('showLogoutModal', 'userRole'));
    }

    public function login()
    {
        $categories = ServiceCategory::pluck('name')->toArray();
        $students = SkomdaStudent::whereDoesntHave('freelancer')
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
