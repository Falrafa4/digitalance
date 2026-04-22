<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Get All Services (ADMIN ONLY)
     */
    public function index()
    {
        $services = Service::with([
            'service_category:id,name',
            'freelancer' => function ($query) {
                $query->select('id', 'student_id')
                    ->with('skomda_student:id,name');
            }
        ])->latest()->get();

        return view('dashboard.admin.services', compact('services'));
    }

    /**
     * CLIENT: Katalog Jasa (Page)
     * - Tanpa filter
     * - Ambil dari DB + eager load
     */
    public function clientIndex()
{
    $services = Service::with([
        'category:id,name',
        'freelancer.skomda_student:id,name'
    ])->latest()->get();

    return view('dashboard.client.services.index', compact('services'));
}

public function clientShow(Service $service)
{
    $service->load([
        'category:id,name',
        'freelancer.skomda_student:id,name,email'
    ]);

    $otherServices = Service::with('category:id,name')
        ->where('freelancer_id', $service->freelancer_id)
        ->where('id', '!=', $service->id)
        ->latest()
        ->take(6)
        ->get();

    return view('dashboard.client.services.show', compact('service', 'otherServices'));
}

    /**
     * FREELANCER ONLY
     */
    public function freelancerIndex()
    {
        $freelancer = auth('freelancer')->user();

        $services = Service::with('service_category:id,name')
            ->where('freelancer_id', $freelancer->id)
            ->get();

        return view('dashboard.freelancer.services', compact('services'));
    }

    /**
     * Store New Service (FREELANCER ONLY)
     */
    public function store(StoreServiceRequest $request)
    {
        $freelancer = auth('freelancer')->user();
        Service::create(array_merge($request->validated(), ['freelancer_id' => $freelancer->id]));

        return redirect()->route('freelancer.services.index')->with('success', 'Layanan berhasil ditambahkan');
    }

    /**
     * Get Service By ID (FREELANCER ONLY)
     */
    public function show(string $id)
    {
        $service = Service::with([
            'service_category:id,name'
        ])->where('id', $id)->first();

        if (!$service) {
            return redirect()->route('freelancer.services.index')->with('error', 'Layanan tidak ditemukan');
        }

        return view('dashboard.freelancer.services.show', compact('service'));
    }

    /**
     * Update Service By ID (FREELANCER ONLY)
     */
    public function update(UpdateServiceRequest $request, string $id)
    {
        $freelancer = auth('freelancer')->user();
        $service = Service::where('freelancer_id', $freelancer->id)->findOrFail($id);
        $service->update($request->validated());

        return redirect()->route('freelancer.services.index')->with('success', 'Layanan berhasil diperbarui');
    }

    /**
     * Delete Service By ID (FREELANCER ONLY)
     */
    public function destroy(string $id)
    {
        $freelancer = auth('freelancer')->user();
        $service = Service::where('freelancer_id', $freelancer->id)->findOrFail($id);
        $service->delete();

        return redirect()->route('freelancer.services.index')->with('success', 'Layanan berhasil dihapus');
    }

    /**
     * Update Service Status (ADMIN ONLY)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Draft,Pending,Approved,Rejected',
            'reject_reason' => 'nullable|string'
        ]);

        $service = Service::findOrFail($id);
        $service->update([
            'status' => $request->status,
            'reject_reason' => $request->status === 'Rejected' ? $request->reject_reason : null
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Status layanan berhasil diperbarui');
    }
}