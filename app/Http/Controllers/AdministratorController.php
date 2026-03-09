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
        $administrator = auth()->user();

        return view('dashboard.admin.profile', compact('administrator'));
    }
    
    public function index()
    {
        $data = Administrator::all();

        return view('dashboard.admin.administrators.index', compact('data'));
    }

    /**
     * Get Administrator By ID
     */
    public function show(Administrator $administrator)
    {
        return response()->json([
            'status' => true,
            'data' => $administrator
        ]);
    }
}
