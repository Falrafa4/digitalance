<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Get All Services
     */
    public function index()
    {
        $data = Service::select('id', 'category_id', 'freelancer_id', 'title', 'description', 'price_min', 'price_max', 'delivery_time', 'status')
            ->with([
                'service_category:id,name',
                'freelancer' => function ($query) {
                    $query->select('id', 'student_id')
                        ->with('skomda_student:id,name');
                }
            ])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Store New Service
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'freelancer_id' => 'required|exists:freelancers,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'price_min' => 'required|numeric',
            'price_max' => 'required|numeric|gte:price_min',
            'delivery_time' => 'required|integer',
        ]);

        $service = Service::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'freelancer_id' => $request->freelancer_id,
            'delivery_time' => $request->delivery_time,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
        ]);

        return response()->json([
            'status' => true,
            'data' => $service
        ], 201);
    }

    /**
     * Get Service By ID
     */
    public function show(string $id)
    {
        $service = Service::select('id', 'category_id', 'freelancer_id', 'title', 'description', 'price_min', 'price_max', 'delivery_time', 'status')
            ->with([
                'service_category:id,name',
                'freelancer' => function ($query) {
                    $query->select('id', 'student_id')
                        ->with('skomda_student:id,name');
                }
            ])
            ->where('id', $id)->first();

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $service
        ]);
    }

    /**
     * Update Service By ID
     */
    public function update(Request $request, string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'freelancer_id' => 'required|exists:freelancers,id',
            'title' => 'required|string',
            'delivery_time' => 'required|integer',
            'price_min' => 'required|numeric',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|string',
        ]);

        $service->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'freelancer_id' => $request->freelancer_id,
            'delivery_time' => $request->delivery_time,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
        ]);

        return response()->json([
            'status' => true,
            'data' => $service
        ]);
    }

    /**
     * Delete Service By ID
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }

        $service->delete();

        return response()->json([
            'status' => true,
            'message' => 'Layanan berhasil dihapus'
        ]);
    }
}
