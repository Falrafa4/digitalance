<?php

namespace App\Http\Controllers;

use App\Models\Administrator;

class AdministratorController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Display the specified resource.
     */
    public function show(Administrator $administrator)
    {
        return response()->json([
            'status' => true,
            'data' => $administrator
        ]);
    }
}
