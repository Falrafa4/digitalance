<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LokerApplicationIndexRequest;
use App\Http\Requests\Api\LokerApplicationStoreRequest;
use App\Http\Requests\Api\LokerIndexRequest;
use App\Http\Requests\Api\LokerStoreRequest;
use App\Http\Requests\Api\LokerUpdateRequest;
use App\Http\Resources\LokerApplicationResource;
use App\Http\Resources\LokerResource;
use App\Models\Loker;
use App\Models\LokerApplication;
use App\Models\Notification;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LokerControllerApi extends Controller
{
    use ApiResponse;

    public function index(LokerIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Loker::class);

        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $category = $validated['category'] ?? null;
        $status = $validated['status'] ?? null;
        $page = (int) ($validated['page'] ?? 1);
        $user = $request->user();

        $lokersQuery = Loker::query()->with($this->defaultLokerRelations());

        if ($user?->getRole() === 'client') {
            $lokersQuery->where('client_id', $user->id);
        } elseif ($user?->getRole() === 'freelancer') {
            $lokersQuery->where(function ($query) use ($user) {
                $query->where('status', 'Open')
                    ->orWhereHas('applications', fn ($applicationQuery) => $applicationQuery->where('freelancer_id', $user->id));
            });
        }

        if ($category) {
            $lokersQuery->where('category_id', $category);
        }

        if ($status) {
            $lokersQuery->where('status', $status);
        }

        if ($q !== '') {
            $lokersQuery->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('client', fn ($clientQuery) => $clientQuery->where('name', 'like', "%{$q}%"));
            });
        }

        $lokers = $lokersQuery
            ->latest()
            ->paginate(12, ['*'], 'page', $page)
            ->withQueryString();

        $lokers->through(fn ($loker) => (new LokerResource($loker))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'category' => $category,
                'status' => $status,
                'per_page' => 12,
                'page' => $page,
            ],
            'lokers' => $lokers,
        ], 'Data lowongan berhasil diambil');
    }

    public function store(LokerStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Loker::class);

        $loker = Loker::create(array_merge($request->validated(), [
            'client_id' => $request->user()->id,
            'status' => 'Open',
        ]));

        return $this->successResponse(
            new LokerResource($loker->fresh($this->defaultLokerRelations())),
            'Lowongan berhasil diposting',
            201
        );
    }

    public function show(Loker $loker): JsonResponse
    {
        Gate::authorize('view', $loker);

        return $this->successResponse(
            new LokerResource($loker->load($this->defaultLokerRelations())),
            'Detail lowongan berhasil diambil'
        );
    }

    public function update(LokerUpdateRequest $request, Loker $loker): JsonResponse
    {
        Gate::authorize('update', $loker);

        $loker->update($request->validated());

        return $this->successResponse(
            new LokerResource($loker->fresh($this->defaultLokerRelations())),
            'Lowongan berhasil diperbarui'
        );
    }

    public function destroy(Loker $loker): JsonResponse
    {
        Gate::authorize('delete', $loker);

        $loker->delete();

        return $this->successResponse(null, 'Lowongan berhasil dihapus.');
    }

    public function apply(LokerApplicationStoreRequest $request, Loker $loker): JsonResponse
    {
        Gate::authorize('apply', $loker);

        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        if ($loker->status !== 'Open') {
            return $this->errorResponse('Lowongan ini sudah ditutup.', 422);
        }

        $freelancer = $request->user();
        $alreadyApplied = LokerApplication::query()
            ->where('loker_id', $loker->id)
            ->where('freelancer_id', $freelancer->id)
            ->exists();

        if ($alreadyApplied) {
            return $this->errorResponse('Kamu sudah melamar lowongan ini.', 422);
        }

        DB::beginTransaction();

        $application = LokerApplication::create([
            'loker_id' => $loker->id,
            'freelancer_id' => $freelancer->id,
            'proposal' => $request->validated('proposal'),
            'proposed_price' => $request->validated('proposed_price'),
            'status' => 'Pending',
        ]);

        Notification::create([
            'title' => 'Lamaran Baru',
            'message' => $freelancer->skomda_student?->name.' melamar: '.$loker->title,
            'type' => 'success',
            'role' => 'client',
            'user_id' => $loker->client_id,
            'link' => '/client/loker',
        ]);

        DB::commit();

        return $this->successResponse(
            new LokerApplicationResource($application->fresh($this->defaultApplicationRelations())),
            'Lamaran berhasil dikirim',
            201
        );
    }

    public function applicationIndex(LokerApplicationIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', LokerApplication::class);

        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $status = $validated['status'] ?? null;
        $lokerId = $validated['loker_id'] ?? null;
        $page = (int) ($validated['page'] ?? 1);
        $user = $request->user();

        $applicationsQuery = LokerApplication::query()->with($this->defaultApplicationRelations());

        if ($user?->getRole() === 'client') {
            $applicationsQuery->whereHas('loker', fn ($lokerQuery) => $lokerQuery->where('client_id', $user->id));
        } elseif ($user?->getRole() === 'freelancer') {
            $applicationsQuery->where('freelancer_id', $user->id);
        }

        if ($status) {
            $applicationsQuery->where('status', $status);
        }

        if ($lokerId) {
            $applicationsQuery->where('loker_id', $lokerId);
        }

        if ($q !== '') {
            $applicationsQuery->where(function ($query) use ($q) {
                $query->where('proposal', 'like', "%{$q}%")
                    ->orWhereHas('loker', fn ($lokerQuery) => $lokerQuery->where('title', 'like', "%{$q}%"))
                    ->orWhereHas('freelancer.skomda_student', fn ($freelancerQuery) => $freelancerQuery->where('name', 'like', "%{$q}%"));
            });
        }

        $applications = $applicationsQuery
            ->latest()
            ->paginate(12, ['*'], 'page', $page)
            ->withQueryString();

        $applications->through(fn ($application) => (new LokerApplicationResource($application))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'status' => $status,
                'loker_id' => $lokerId,
                'per_page' => 12,
                'page' => $page,
            ],
            'applications' => $applications,
        ], 'Data lamaran lowongan berhasil diambil');
    }

    public function showApplication(LokerApplication $application): JsonResponse
    {
        Gate::authorize('view', $application);

        return $this->successResponse(
            new LokerApplicationResource($application->load($this->defaultApplicationRelations())),
            'Detail lamaran lowongan berhasil diambil'
        );
    }

    public function approveApplication(Request $request, LokerApplication $application): JsonResponse
    {
        Gate::authorize('approve', $application);

        $application->loadMissing('loker', 'freelancer.skomda_student');

        if ($application->status !== 'Pending') {
            return $this->errorResponse('Lamaran ini sudah diproses.', 422);
        }

        if (Order::where('loker_application_id', $application->id)->exists()) {
            return $this->errorResponse('Order untuk lamaran ini sudah dibuat.', 422);
        }

        $order = DB::transaction(function () use ($application) {
            $application->update(['status' => 'Approved']);
            $application->loker->update(['status' => 'Closed']);

            return Order::create([
                'service_id' => null,
                'client_id' => $application->loker->client_id,
                'freelancer_id' => $application->freelancer_id,
                'loker_application_id' => $application->id,
                'brief' => $application->loker->title.' - '.$application->loker->description,
                'status' => 'Pending',
                'agreed_price' => $application->proposed_price,
            ]);
        });

        Notification::create([
            'title' => 'Lamaran Disetujui',
            'message' => 'Client telah menyetujui lamaranmu untuk: '.$application->loker->title.'. Order sudah dibuat, tunggu konfirmasi pembayaran.',
            'type' => 'success',
            'role' => 'freelancer',
            'user_id' => $application->freelancer_id,
            'link' => '/freelancer/orders',
        ]);

        return $this->successResponse([
            'application' => new LokerApplicationResource($application->fresh($this->defaultApplicationRelations())),
            'order' => [
                'id' => $order->id,
                'client_id' => $order->client_id,
                'freelancer_id' => $order->freelancer_id,
                'loker_application_id' => $order->loker_application_id,
                'status' => $order->status,
                'agreed_price' => $order->agreed_price,
            ],
        ], 'Lamaran berhasil disetujui dan order telah dibuat');
    }

    public function rejectApplication(Request $request, LokerApplication $application): JsonResponse
    {
        Gate::authorize('reject', $application);

        $application->loadMissing('loker');

        if ($application->status !== 'Pending') {
            return $this->errorResponse('Lamaran ini sudah diproses.', 422);
        }

        $application->update(['status' => 'Rejected']);

        Notification::create([
            'title' => 'Lamaran Ditolak',
            'message' => 'Maaf, lamaranmu untuk: '.$application->loker->title.' tidak disetujui.',
            'type' => 'warning',
            'role' => 'freelancer',
            'user_id' => $application->freelancer_id,
            'link' => '/freelancer/loker/my/applications',
        ]);

        return $this->successResponse(
            new LokerApplicationResource($application->fresh($this->defaultApplicationRelations())),
            'Lamaran freelancer berhasil ditolak'
        );
    }

    /**
     * @return array<int, string>
     */
    private function defaultLokerRelations(): array
    {
        return [
            'category',
            'client',
            'applications.freelancer.skomda_student',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function defaultApplicationRelations(): array
    {
        return [
            'loker.category',
            'loker.client',
            'freelancer.skomda_student',
        ];
    }

    private function ensureApprovedFreelancer(Request $request): ?JsonResponse
    {
        $freelancer = $request->user();

        if (! $freelancer) {
            return $this->errorResponse('Tidak terautentikasi', 401);
        }

        if ($freelancer->status !== 'Approved') {
            return $this->errorResponse(
                'Akses terbatas. Mohon ajukan verifikasi ke admin melalui panduan onboarding.',
                403
            );
        }

        return null;
    }
}
