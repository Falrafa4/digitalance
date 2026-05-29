<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * PUBLIC: Katalog layanan untuk landing page.
     */
    public function publicIndex(Request $request)
    {
        $categoryId = $request->query('category');
        $search = trim((string) $request->query('q', ''));

        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->withCount([
                'services as approved_services_count' => function ($query) {
                    $query->where('status', 'Approved');
                },
            ])
            ->orderBy('name')
            ->get();

        $servicesQuery = Service::query()
            ->with([
                'category:id,name',
                'freelancer.skomda_student:id,name',
            ])
            ->where('status', 'Approved')
            ->whereNotNull('freelancer_id')
            ->whereHas('freelancer');

        if ($categoryId) {
            $servicesQuery->where('category_id', $categoryId);
        }

        if ($search !== '') {
            $servicesQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('freelancer.skomda_student', function ($freelancerQuery) use ($search) {
                        $freelancerQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $services = $servicesQuery
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $featuredServices = Service::query()
            ->with([
                'category:id,name',
                'freelancer.skomda_student:id,name',
            ])
            ->where('status', 'Approved')
            ->whereNotNull('freelancer_id')
            ->whereHas('freelancer')
            ->latest()
            ->take(3)
            ->get();

        return view('public.services.index', compact(
            'categories',
            'services',
            'featuredServices',
            'search',
            'categoryId'
        ));
    }

    /**
     * Get All Services (ADMIN ONLY)
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $query = Service::with([
            'service_category:id,name',
            'freelancer.skomda_student:id,name',
        ]);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('freelancer.skomda_student', function ($fq) use ($search) {
                        $fq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $services = $query->latest()->paginate(12)->withQueryString();

        return view('dashboard.admin.services', compact('services'));
    }

    /**
     * CLIENT: Katalog Jasa (Page)
     * - Dengan filter
     * - Ambil dari DB + eager load + paginate
     */
    public function clientIndex(Request $request)
    {
        $categoryId = $request->query('category');
        $search = trim((string) $request->query('q', ''));
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');
        $deliveryTime = $request->query('delivery_time');

        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->withCount([
                'services as approved_services_count' => function ($query) {
                    $query->where('status', 'Approved');
                },
            ])
            ->orderBy('name')
            ->get();

        $servicesQuery = Service::with([
            'category:id,name',
            'freelancer.skomda_student:id,name',
        ])
            ->where('status', 'Approved')
            ->whereNotNull('freelancer_id')
            ->whereHas('freelancer');

        if ($categoryId) {
            $servicesQuery->where('category_id', $categoryId);
        }

        if ($search !== '') {
            $servicesQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('freelancer.skomda_student', function ($freelancerQuery) use ($search) {
                        $freelancerQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($priceMin !== '' && is_numeric($priceMin)) {
            $servicesQuery->where('price_min', '>=', $priceMin);
        }

        if ($priceMax !== '' && is_numeric($priceMax)) {
            $servicesQuery->where('price_max', '<=', $priceMax);
        }

        if ($deliveryTime !== '' && is_numeric($deliveryTime)) {
            $servicesQuery->where('delivery_time', '<=', $deliveryTime);
        }

        $services = $servicesQuery->latest()->paginate(12)->withQueryString();

        $featuredServices = Service::query()
            ->with([
                'category:id,name',
                'freelancer.skomda_student:id,name',
            ])
            ->where('status', 'Approved')
            ->whereNotNull('freelancer_id')
            ->whereHas('freelancer')
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.client.services.index', compact(
            'services',
            'featuredServices',
            'categories',
            'search',
            'categoryId',
            'priceMin',
            'priceMax',
            'deliveryTime'
        ));
    }

    public function clientShow(Service $service)
    {
        if ($service->status !== 'Approved') {
            return redirect()->route('client.services.index')->with('warning', 'Layanan tidak tersedia.');
        }

        if (!$service->freelancer_id || !$service->freelancer) {
            return redirect()->route('client.services.index')->with('warning', 'Layanan ini tidak memiliki freelancer yang tertaut.');
        }

        $service->load([
            'service_category:id,name',
            'freelancer.skomda_student:id,name,email',
        ]);

        $otherServices = Service::with('service_category:id,name')
            ->where('freelancer_id', $service->freelancer_id)
            ->where('id', '!=', $service->id)
            ->where('status', 'Approved')
            ->whereNotNull('freelancer_id')
            ->whereHas('freelancer')
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
            ->select(['id', 'freelancer_id', 'service_category_id', 'title', 'price', 'status', 'description', 'created_at'])
            ->where('freelancer_id', $freelancer->id)
            ->get();

        return view('dashboard.freelancer.services', compact('services'));
    }

    /**
     * Show create service form (FREELANCER ONLY)
     */
    public function create()
    {
        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.freelancer.services.create', compact('categories'));
    }

    /**
     * Store New Service (FREELANCER ONLY)
     */
    public function store(StoreServiceRequest $request)
    {
        $freelancer = auth('freelancer')->user();
        $service = Service::create(array_merge($request->validated(), [
            'freelancer_id' => $freelancer->id,
            'status' => 'Pending',
        ]));

        return redirect()->route('freelancer.services.show', $service->id)->with('success', 'Layanan berhasil ditambahkan');
    }

    /**
     * Get Service By ID (FREELANCER ONLY)
     */
    public function show(string $id)
    {
        $service = Service::with([
            'service_category:id,name',
        ])->where('id', $id)->first();

        if (!$service) {
            return redirect()->route('freelancer.services.index')->with('error', 'Layanan tidak ditemukan');
        }

        $freelancer = auth('freelancer')->user();
        if ((int) $service->freelancer_id !== (int) $freelancer->id) {
            return redirect()->route('freelancer.services.index')->with('error', 'Anda tidak memiliki akses ke layanan ini');
        }

        return view('dashboard.freelancer.services.show', compact('service'));
    }

    /**
     * Edit Service By ID (FREELANCER ONLY)
     */
    public function edit(string $id)
    {
        $freelancer = auth('freelancer')->user();
        $service = Service::with('service_category:id,name')
            ->where('freelancer_id', $freelancer->id)
            ->findOrFail($id);

        return view('dashboard.freelancer.services.edit', compact('service'));
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

    public function submit(int $id)
    {
        $freelancer = auth('freelancer')->user();
        $service = Service::where('freelancer_id', $freelancer->id)
            ->where('status', 'Draft')
            ->findOrFail($id);

        $service->update(['status' => 'Pending']);

        return redirect()->route('freelancer.services.show', $id)->with('success', 'Layanan berhasil diajukan untuk tinjauan admin.');
    }

    /**
     * Update Service Status (ADMIN ONLY)
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:Draft,Pending,Approved,Rejected',
            'reject_reason' => 'nullable|string',
        ]);

        $service = Service::findOrFail($id);
        $finalStatus = $request->status;

        // Custom logic: Reject returns to Draft + Notification to Freelancer
        if ($finalStatus === 'Rejected') {
            $finalStatus = 'Draft';

            \App\Models\Notification::create([
                'title' => 'Layanan Perlu Perbaikan',
                'message' => "Layanan '{$service->title}' dikembalikan oleh admin. Alasan: " . ($request->reject_reason ?? 'Tidak ada alasan spesifik') . '. Silakan perbaiki dan ajukan kembali!',
                'type' => 'warning',
                'role' => 'freelancer',
                'user_id' => $service->freelancer_id,
                'link' => route('freelancer.services.edit', $service->id),
            ]);
        }

        $service->update([
            'status' => $finalStatus,
            'reject_reason' => $request->status === 'Rejected' ? $request->reject_reason : null,
        ]);

        if ($request->status === 'Approved') {
            \App\Models\Notification::create([
                'title' => 'Layanan Disetujui',
                'message' => "Layanan '{$service->title}' telah disetujui admin dan sudah tampil di katalog layanan.",
                'type' => 'success',
                'role' => 'freelancer',
                'user_id' => $service->freelancer_id,
                'link' => route('freelancer.services.show', $service->id),
            ]);
        }

        $msg = $request->status === 'Rejected'
            ? 'Layanan telah dikembalikan ke freelancer untuk diperbaiki.'
            : 'Status layanan berhasil diperbarui';

        return redirect()->route('admin.services.index')->with('success', $msg);
    }
}
