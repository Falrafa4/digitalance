<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;

class ServiceCategoryController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $serviceCategories = ServiceCategory::paginate(10);
        return view('dashboard.admin.service_categories', compact('serviceCategories'));
    }

    public function store(StoreServiceCategoryRequest $request)
    {
        ServiceCategory::create($request->validated());

        return redirect()->route('admin.service-categories.index')->with('success', 'Kategori layanan berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $serviceCategory = ServiceCategory::findOrFail($id);

        return view('dashboard.admin.service_categories', compact('serviceCategory'));
    }

    public function update(UpdateServiceCategoryRequest $request, string $id)
    {
        $serviceCategory = ServiceCategory::findOrFail($id);
        $serviceCategory->update($request->validated());

        return redirect()->route('admin.service-categories.index')->with('success', 'Kategori layanan berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $serviceCategory = ServiceCategory::findOrFail($id);

        $serviceCategory->delete();

        return redirect()->route('admin.service-categories.index')->with('success', 'Kategori layanan berhasil dihapus');
    }

    // FREELANCER ONLY
    public function freelancerIndex()
    {
        $serviceCategories = ServiceCategory::paginate(10);
        return view('dashboard.freelancer.service_categories', compact('serviceCategories'));
    }

    public function freelancerShow(string $id)
    {
        $serviceCategory = ServiceCategory::findOrFail($id);
        return view('dashboard.freelancer.service_categories', compact('serviceCategory'));
    }
}
