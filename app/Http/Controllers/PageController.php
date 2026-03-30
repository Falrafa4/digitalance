<?php

namespace App\Http\Controllers;

class PageController
{
    public function home()
    {
        return view('public.home');
    }

    public function login()
    {
        return view('public.login');
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

