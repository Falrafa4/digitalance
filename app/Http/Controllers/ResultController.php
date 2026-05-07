<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResultRequest;
use App\Http\Requests\UpdateResultRequest;
use App\Models\Order;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    public function store(StoreResultRequest $request, string $orderId)
    {
        $freelancer = auth('freelancer')->user();
        $order = Order::with('service')->findOrFail($orderId);
        $validated = $request->validated();

        if (!$order->service || $order->service->freelancer_id !== $freelancer->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengirim hasil untuk order ini.');
        }

        if (!$request->hasFile('file')) {
            return back()->with('error', 'File tidak ditemukan');
        }

        $filePath = $request->file('file')->store('results', 'public');
        $version = $validated['version'] ?? $validated['message'] ?? null;
        $note = $validated['note'] ?? '';

        if (!$version) {
            Storage::disk('public')->delete($filePath);
            return back()->withErrors(['version' => 'Versi hasil wajib diisi.'])->withInput();
        }

        try {
            DB::transaction(function () use ($order, $filePath, $note, $version) {
                $order->update(['status' => 'Completed']);

                Result::create([
                    'order_id' => $order->id,
                    'file_url' => $filePath,
                    'note' => $note,
                    'version' => $version,
                ]);
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($filePath);
            throw $e;
        }

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
    public function clientIndex()
    {
        $client = auth('client')->user();
        $results = Result::with('order.service.freelancer')
            ->whereHas('order', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })->get();

        return view('dashboard.client.results', compact('results'));
    }

    public function clientShow(Result $result)
    {
        $result = $result->load('order.service.freelancer');

        if (!$result->order || $result->order->client_id !== auth('client')->id()) {
            abort(403, 'Anda tidak memiliki izin untuk melihat hasil ini.');
        }

        return view('dashboard.client.result-detail', compact('result'));
    }
}
