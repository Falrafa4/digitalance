<?php

namespace App\Http\Controllers;

use App\Models\Administrator;

class AdministratorController extends Controller
{
    /**
     * Get All Administrators
     */
    public function profile()
    {
        $administrator = auth()->guard('administrator')->user();

        return view('dashboard.admin.profile', compact('administrator'));
    }
    
    public function index()
    {
        $administrators = Administrator::all();
        return view('dashboard.admin.administrators.index', compact('administrators'));
    }

    /**
     * Get Administrator By ID
     */
    public function show(Administrator $administrator)
    {
        return view('dashboard.admin.administrators.show', compact('administrator'));
    }
}
