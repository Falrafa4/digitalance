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
    $freelancer = auth('freelancer')->user();
    $service = Service::with([
      'service_category:id,name'
    ])->where('id', $id)->first();

    if (!$service) {
      return redirect()->route('freelancer.services.index')->with('error', 'Layanan tidak ditemukan');
    }

    return view('freelancer.services.show', compact('service'));
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
      'status' => 'required|in:Approved,Rejected',
      'reject_reason' => 'nullable|string'
    ]);

    $service = Service::findOrFail($id);
    $service->status = $request->status;
    
    if ($request->status === 'Rejected') {
      $service->reject_reason = $request->reject_reason;
    } else {
      $service->reject_reason = null;
    }

    $service->save();

    return response()->json([
      'success' => true,
      'message' => 'Status layanan berhasil diperbarui!',
      'data' => $service
    ]);
  }
}