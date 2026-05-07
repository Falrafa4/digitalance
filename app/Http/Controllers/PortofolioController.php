<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePortofolioRequest;
use App\Http\Requests\UpdatePortofolioRequest;
use App\Models\Portofolio;

class PortofolioController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $portofolios = Portofolio::with('service.freelancer.skomda_student')->get();

        return view('dashboard.admin.portofolios', compact('portofolios'));
    }

    // FREELANCER ONLY
    public function freelancerIndex()
    {
        $freelancer = auth('freelancer')->user();
        $portofolios = $freelancer->portofolios()->with('service')->get();
        $services = $freelancer->services()->get();

        return view('dashboard.freelancer.portofolios', compact('portofolios', 'services'));
    }

    public function store(StorePortofolioRequest $request)
    {
        Portofolio::create($request->validated());

        return redirect()->route('freelancer.portofolios.index')->with('success', 'Portofolio berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $portofolio = Portofolio::with('service')->findOrFail($id);

        return view('dashboard.freelancer.portofolios', compact('portofolio'));
    }

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

    // CLIENT ONLY
    public function showAllFreelancerPortofolios(string $freelancer_id)
    {
        $portofolio = Portofolio::with('service.freelancer.skomda_student')->whereHas('service.freelancer', function ($query) use ($freelancer_id) {
            $query->where('id', $freelancer_id);
        })->get();
        return view('dashboard.client.portofolio', compact('portofolio'));
    }

    public function showFreelancerPortofolio(string $id)
    {
        $portofolio = Portofolio::with('service.freelancer.skomda_student')->findOrFail($id);
        return view('dashboard.client.portofolio', compact('portofolio'));
    }
}
