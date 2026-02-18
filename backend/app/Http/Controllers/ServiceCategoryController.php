<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ServiceCategory::all();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        $service_category = ServiceCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => true,
            'data' => $service_category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service_category = ServiceCategory::find($id);

        if (!$service_category) {
            return response()->json([
                'status' => false,
                'message' => 'Kategori layanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $service_category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service_category = ServiceCategory::find($id);

        if (!$service_category) {
            return response()->json([
                'status' => false,
                'message' => 'Kategori layanan tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        $service_category->update($request->all());

        return response()->json([
            'status' => true,
            'data' => $service_category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service_category = ServiceCategory::find($id);

        if (!$service_category) {
            return response()->json([
                'status' => false,
                'message' => 'Kategori layanan tidak ditemukan'
            ], 404);
        }

        $service_category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Kategori layanan berhasil dihapus'
        ]);
    }
}
