<?php

namespace App\Http\Controllers;

class PageController
{
    public function home()
    {
        return view('home');
    }

    public function login()
    {
        return view('login');
    }

    public function registerClient()
    {
        return view('auth.register_client');
    }

    public function registerFreelancer()
    {
        return view('auth.register_freelancer');
    }
}
