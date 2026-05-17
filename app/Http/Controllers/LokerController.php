<?php

namespace App\Http\Controllers;

use App\Models\Loker;
use App\Models\LokerApplication;
use App\Models\Notification;
use App\Models\Order;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class LokerController extends Controller
{
    // ========================
    // CLIENT: Job Posting
    // ========================

    public function clientIndex()
    {
        $client = auth('client')->user();

        $lokkers = Loker::with(['category', 'applications.freelancer.skomda_student'])
            ->where('client_id', $client->id)
            ->latest()
            ->get();

        return view('dashboard.client.loker.index', compact('lokkers'));
    }

    public function clientCreate()
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.client.loker.create', compact('categories'));
    }

    public function clientStore(Request $request)
    {
        $client = auth('client')->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'category_id' => 'nullable|exists:service_categories,id',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
        ]);

        Loker::create(array_merge($validated, [
            'client_id' => $client->id,
            'status' => 'Open',
        ]));

        return redirect()->route('client.loker.index')->with('success', 'Lowongan berhasil diposting!');
    }

    public function clientEdit(Loker $loker)
    {
        $client = auth('client')->user();
        abort_unless($loker->client_id === $client->id, 403);

        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.client.loker.edit', compact('loker', 'categories'));
    }

    public function clientUpdate(Request $request, Loker $loker)
    {
        $client = auth('client')->user();
        abort_unless($loker->client_id === $client->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'category_id' => 'nullable|exists:service_categories,id',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'status' => 'required|in:Open,Closed',
        ]);

        $loker->update($validated);

        return redirect()->route('client.loker.index')->with('success', 'Lowongan berhasil diperbarui.');
    }

    public function clientDestroy(Loker $loker)
    {
        $client = auth('client')->user();
        abort_unless($loker->client_id === $client->id, 403);

        $loker->delete();

        return redirect()->route('client.loker.index')->with('success', 'Lowongan berhasil dihapus.');
    }

    // CLIENT: Applications
    public function approveApplication(LokerApplication $application)
    {
        $client = auth('client')->user();
        abort_unless($application->loker->client_id === $client->id, 403);

        $application->update(['status' => 'Approved']);
        $application->loker->update(['status' => 'Closed']);

        Order::create([
            'service_id' => null,
            'client_id' => $client->id,
            'freelancer_id' => $application->freelancer_id,
            'loker_application_id' => $application->id,
            'brief' => $application->loker->title . ' - ' . $application->loker->description,
            'status' => 'Pending',
            'agreed_price' => $application->proposed_price,
        ]);

        Notification::create([
            'title' => 'Lamaran Disetujui',
            'message' => 'Client telah menyetujui lamaranmu untuk: ' . $application->loker->title . '. Order sudah dibuat, tunggu konfirmasi pembayaran.',
            'type' => 'success',
            'role' => 'freelancer',
            'user_id' => $application->freelancer_id,
            'link' => '/freelancer/orders',
        ]);

        return back()->with('success', 'Lamaran disetujui! Order telah dibuat untuk freelancer.');
    }

    public function rejectApplication(LokerApplication $application)
    {
        $client = auth('client')->user();
        abort_unless($application->loker->client_id === $client->id, 403);

        $application->update(['status' => 'Rejected']);

        Notification::create([
            'title' => 'Lamaran Ditolak',
            'message' => 'Maaf, lamaranmu untuk: ' . $application->loker->title . ' tidak disetujui.',
            'type' => 'warning',
            'role' => 'freelancer',
            'user_id' => $application->freelancer_id,
            'link' => '/freelancer/loker/my/applications',
        ]);

        return back()->with('success', 'Lamaran freelancer ditolak.');
    }

    // ========================
    // FREELANCER: Browse & Apply
    // ========================

    public function freelancerIndex(Request $request)
    {
        $freelancer = auth('freelancer')->user();

        $query = Loker::with(['category', 'client', 'applications'])
            ->where('status', 'Open');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('client', fn ($cq) => $cq->where('name', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->query('category'));
        }

        $lokkers = $query->latest()->get();

        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        foreach ($lokkers as $loker) {
            $loker->hasApplied = $loker->applications->where('freelancer_id', $freelancer->id)->count() > 0;
            $loker->myApplication = $loker->applications->where('freelancer_id', $freelancer->id)->first();
        }

        return view('dashboard.freelancer.loker.index', compact('lokkers', 'categories'));
    }

    public function freelancerShow(Loker $loker)
    {
        $freelancer = auth('freelancer')->user();
        $loker->load(['category', 'client', 'applications.freelancer.skomda_student']);
        $loker->hasApplied = $loker->applications->where('freelancer_id', $freelancer->id)->count() > 0;
        $loker->myApplication = $loker->applications->where('freelancer_id', $freelancer->id)->first();

        return view('dashboard.freelancer.loker.show', compact('loker'));
    }

    public function freelancerApply(Request $request, Loker $loker)
    {
        $freelancer = auth('freelancer')->user();

        if ($loker->status !== 'Open') {
            return back()->with('error', 'Lowongan ini sudah ditutup.');
        }

        $alreadyApplied = LokerApplication::where('loker_id', $loker->id)
            ->where('freelancer_id', $freelancer->id)
            ->exists();

        if ($alreadyApplied) {
            return back()->with('warning', 'Kamu sudah melamar lowongan ini.');
        }

        $validated = $request->validate([
            'proposal' => 'required|string|min:20',
            'proposed_price' => 'nullable|numeric|min:1000',
        ]);

        LokerApplication::create([
            'loker_id' => $loker->id,
            'freelancer_id' => $freelancer->id,
            'proposal' => $validated['proposal'],
            'proposed_price' => $validated['proposed_price'] ?? null,
            'status' => 'Pending',
        ]);

        Notification::create([
            'title' => 'Lamaran Baru',
            'message' => $freelancer->skomda_student->name . ' melamar: ' . $loker->title,
            'type' => 'success',
            'role' => 'client',
            'user_id' => $loker->client_id,
            'link' => '/client/loker',
        ]);

        return redirect()->route('freelancer.loker.index')->with('success', 'Lamaran berhasil dikirim!');
    }

    public function freelancerMyApplications()
    {
        $freelancer = auth('freelancer')->user();

        $applications = LokerApplication::with(['loker.category', 'loker.client'])
            ->where('freelancer_id', $freelancer->id)
            ->latest()
            ->get();

        return view('dashboard.freelancer.loker.applications', compact('applications'));
    }
}
