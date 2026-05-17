<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use Illuminate\Http\RedirectResponse;

class PageController
{
    public function home()
    {
        if (auth('administrator')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (auth('client')->check()) {
            return redirect()->route('client.dashboard');
        }

        if (auth('freelancer')->check()) {
            return redirect()->route('freelancer.dashboard');
        }

        return view('public.home');
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
