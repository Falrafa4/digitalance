<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin');
    }

    public function client()
    {
        return view('dashboard.client');
    }

    public function freelancer()
    {
        return view('dashboard.freelancer');
    }
}
