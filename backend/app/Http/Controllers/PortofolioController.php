<?php

namespace App\Http\Controllers;

use App\Models\Portofolio;
use Illuminate\Http\Request;

class PortofolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Portofolio::all();

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
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'media_url' => 'required|url',
        ]);

        $portofolio = Portofolio::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Portofolio berhasil dibuat',
            'data' => $portofolio
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service_category = Portofolio::find($id);

        if (!$service_category) {
            return response()->json([
                'status' => false,
                'message' => 'Portofolio tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $service_category
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $portofolio = Portofolio::find($id);

        if (!$portofolio) {
            return response()->json([
                'status' => false,
                'message' => 'Portofolio tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'media_url' => 'required|url',
        ]);

        $portofolio->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Portofolio berhasil diperbarui',
            'data' => $portofolio,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $portofolio = Portofolio::find($id);

        if (!$portofolio) {
            return response()->json([
                'status' => false,
                'message' => 'Portofolio tidak ditemukan'
            ], 404);
        }

        $portofolio->delete();

        return response()->json([
            'status' => true,
            'message' => 'Portofolio berhasil dihapus'
        ], 204);
    }
}
