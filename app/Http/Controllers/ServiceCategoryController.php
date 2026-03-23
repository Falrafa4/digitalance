<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;

class ServiceCategoryController extends Controller
{
    /**
     * Get All Service Categories
     */
    public function index()
    {
        $serviceCategories = ServiceCategory::paginate(10);
        return view('dashboard.admin.service_categories', compact('serviceCategories'));
    }

    /**
     * Store New Service Category
     */
    public function store(StoreServiceCategoryRequest $request)
    {
        ServiceCategory::create($request->validated());

        return redirect()->route('admin.service-categories')->with('success', 'Kategori layanan berhasil ditambahkan');
    }

    /**
     * Get Service Category By ID
     */
    public function show(string $id)
    {
        $serviceCategory = ServiceCategory::findOrFail($id);

        return view('dashboard.admin.service_categories', compact('serviceCategory'));
    }

    /**
     * Update Service Category By ID
     */
    public function update(UpdateServiceCategoryRequest $request, string $id)
    {
        $serviceCategory = ServiceCategory::findOrFail($id);
        $serviceCategory->update($request->validated());

        return redirect()->route('admin.service-categories')->with('success', 'Kategori layanan berhasil diperbarui');
    }

    /**
     * Delete Service Category By ID
     */
    public function destroy(string $id)
    {
        $serviceCategory = ServiceCategory::findOrFail($id);

        $serviceCategory->delete();

        return redirect()->route('admin.service-categories')->with('success', 'Kategori layanan berhasil dihapus');
    }
}
