<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePortofolioRequest;
use App\Http\Requests\UpdatePortofolioRequest;
use App\Models\Portofolio;

class PortofolioController extends Controller
{
    /**
     * Display a listing of the resource. (ADMIN ONLY)
     */
    public function index()
    {
        $portofolios = Portofolio::with('service.freelancer.skomda_student')->get();

        return view('dashboard.admin.portofolios', compact('portofolios'));
    }

    /**
     * Display a listing of the resource. (FREELANCER ONLY)
     */
    public function freelancerIndex()
    {
        $freelancer = auth('freelancer')->user();
        $portofolios = Portofolio::with('service')->where('freelancer_id', $freelancer->id)->get();
        $services = $freelancer->service()->get();

        return view('dashboard.freelancer.portofolios', compact('portofolios', 'services'));
    }

    /**
     * Store a newly created resource in storage. (FREELANCER ONLY)
     */
    public function store(StorePortofolioRequest $request)
    {
        Portofolio::create($request->validated());

        return redirect()->route('freelancer.portofolios.index')->with('success', 'Portofolio berhasil ditambahkan');
    }

    /**
     * Display the specified resource. (FREELANCER ONLY)
     */
    public function show(string $id)
    {
        $portofolio = Portofolio::with('service')->findOrFail($id);

        return view('dashboard.freelancer.portofolios', compact('portofolio'));
    }

    /**
     * Update the specified resource in storage. (FREELANCER ONLY)
     */
    public function update(UpdatePortofolioRequest $request, string $id)
    {
        $freelancer = auth('freelancer')->user();
        $portofolio = Portofolio::with('service')->findOrFail($id);

        if ($freelancer->id !== $portofolio->service->freelancer_id) {
            return redirect()->route('freelancer.portofolios.index')->with('error', 'Anda tidak memiliki akses untuk mengedit portofolio ini');
        }

        $portofolio->update($request->validated());

        return redirect()->route('freelancer.portofolios.index')->with('success', 'Portofolio berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage. (FREELANCER ONLY)
     */
    public function destroy(string $id)
    {
        $freelancer = auth('freelancer')->user();
        $portofolio = Portofolio::with('service')->findOrFail($id);

        if ($freelancer->id !== $portofolio->service->freelancer_id) {
            return redirect()->route('freelancer.portofolios.index')->with('error', 'Anda tidak memiliki akses untuk menghapus portofolio ini');
        }
        
        $portofolio->delete();

        return redirect()->route('freelancer.portofolios.index')->with('success', 'Portofolio berhasil dihapus');
    }
}
