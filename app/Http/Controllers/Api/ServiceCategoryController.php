<?php

namespace App\Http\Controllers\Api;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    /**
     * Get All Service Categories
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
     * Store New Service Category
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
     * Get Service Category By ID
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
     * Update Service Category By ID
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
     * Delete Service Category By ID
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
