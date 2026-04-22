<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResultRequest;
use App\Http\Requests\UpdateResultRequest;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $results = Result::with('order.service.freelancer')->get();
        return view('dashboard.admin.results', compact('results'));
    }

    // FREELANCER ONLY
    public function freelancerIndex()
    {
        $freelancer = auth('freelancer')->user();
        $results = Result::with('order.service.freelancer')
            ->whereHas('order.service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })->get();

        return view('dashboard.freelancer.results', compact('results'));
    }

    public function store(StoreResultRequest $request)
    {
        $validated = $request->validated();

        if (!$request->hasFile('file')) {
            return back()->with('error', 'File tidak ditemukan');
        }

        $filePath = $request->file('file')->store('results', 'public');

        Result::create([
            'order_id' => $validated['order_id'],
            'file_url' => $filePath,
            'note' => $validated['note'] ?? null,
            'message' => $validated['message'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Hasil kerja berhasil dikirim');
    }

    public function show(Result $result)
    {
        $result = $result->load('order.service.freelancer');
        return view('dashboard.freelancer.result-detail', compact('result'));
    }

    public function update(UpdateResultRequest $request, Result $result)
    {
        $validated = $request->validated();
        $result->update($validated);
        return redirect()->back()->with('success', 'Hasil kerja berhasil diperbarui');
    }

    public function destroy(Result $result)
    {
        $result->delete();
        return redirect()->back()->with('success', 'Hasil kerja berhasil dihapus');
    }

    // CLIENT ONLY
    // TODO: selesaikan fitur result untuk client (all), termasuk validasi dan return view yang sesuai
}
