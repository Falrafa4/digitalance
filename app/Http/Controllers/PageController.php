<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;

class PageController
{
    public function home()
    {
        return view('public.home');
    }

    public function login()
    {
        $categories = ServiceCategory::pluck('name')->toArray();
        $students = SkomdaStudent::select('nis', 'name', 'email')->orderBy('name')->get();

        return view('public.login', compact('categories', 'students'));
    }

    public function registerClient()
    {
        return view('auth.register-client');
    }

    public function registerFreelancer()
    {
        return view('auth.register-freelancer');
    }
}