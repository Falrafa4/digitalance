<?php

namespace App\Http\Controllers\Api;

use App\Models\Administrator;

class AdministratorController extends Controller
{
    /**
     * Get All Administrators
     */
    public function index()
    {
        $data = Administrator::all();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
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
