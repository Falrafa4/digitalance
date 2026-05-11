<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePortofolioRequest;
use App\Http\Requests\UpdatePortofolioRequest;
use App\Models\Portofolio;

class PortofolioController extends Controller
{
    // ADMIN ONLY
    public function index(\Illuminate\Http\Request $request)
    {
        $search = $request->query('q');
        
        $query = Portofolio::with('service.freelancer.skomda_student');

        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhereHas('service.freelancer.skomda_student', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
        }

        $portofolios = $query->latest()->paginate(12)->withQueryString();

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

    public function adminUpdate(UpdatePortofolioRequest $request, string $id)
    {
        $portofolio = Portofolio::findOrFail($id);
        $portofolio->update($request->validated());

        return redirect()->route('admin.portofolios.index')->with('success', 'Portofolio berhasil diperbarui oleh Admin');
    }

    public function adminDestroy(string $id)
    {
        $portofolio = Portofolio::findOrFail($id);
        $portofolio->delete();

        return redirect()->route('admin.portofolios.index')->with('success', 'Portofolio berhasil dihapus oleh Admin');
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
