<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
