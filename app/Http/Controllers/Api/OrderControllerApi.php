<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderAgreedPriceRequest;
use App\Http\Requests\Api\OrderAttachmentStoreRequest;
use App\Http\Requests\Api\OrderIndexRequest;
use App\Http\Requests\Api\OrderNegotiationRequest;
use App\Http\Requests\Api\OrderPaymentRequest;
use App\Http\Requests\Api\OrderRejectRequest;
use App\Http\Requests\Api\OrderRevisionRequest;
use App\Http\Requests\Api\OrderStatusUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderAttachment;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get all orders for administrator.
     */
    public function index(OrderIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Order::class);

        if ($request->user()?->getRole() === 'client') {
            return $this->clientIndex($request);
        }

        if ($request->user()?->getRole() === 'freelancer') {
            return $this->freelancerIndex($request);
        }

        $validated = $request->validated();
        $status = $validated['status'] ?? null;
        $search = trim((string) ($validated['q'] ?? ''));
        $payout = strtolower(trim((string) ($validated['payout'] ?? 'all')));
        $page = (int) ($validated['page'] ?? 1);

        $orders = $this->baseOrderQuery()
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($search !== '', fn($query) => $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhereHas('client', fn($clientQuery) => $clientQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('service', fn($serviceQuery) => $serviceQuery->where('title', 'like', "%{$search}%"));
            }))
            ->when($payout === 'paid', fn($query) => $query->whereHas('transactions', function ($transactionQuery) {
                $transactionQuery->where('type', 'Full')->where('status', 'Paid');
            }))
            ->when($payout === 'pending', fn($query) => $query->where('status', 'Completed')
                ->whereDoesntHave('transactions', function ($transactionQuery) {
                    $transactionQuery->where('type', 'Full')->where('status', 'Paid');
                }))
            ->latest()
            ->paginate(12, ['*'], 'page', $page)
            ->withQueryString();

        $orders->through(fn($order) => (new OrderResource($order))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $search,
                'status' => $status,
                'payout' => $payout,
                'per_page' => 12,
                'page' => $page,
            ],
            'orders' => $orders,
        ], 'Data pesanan berhasil diambil');
    }

    /**
     * Store a new order for administrator.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Order::class);

        if ($request->user()?->getRole() === 'client') {
            return $this->clientStore($request);
        }

        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'brief' => ['required', 'string'],
            'status' => ['nullable', 'in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled'],
            'agreed_price' => ['nullable', 'numeric', 'min:0'],
            'deadline' => ['nullable', 'date'],
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $order = Order::create([
            'client_id' => $validated['client_id'],
            'service_id' => $service->id,
            'freelancer_id' => $service->freelancer_id,
            'brief' => $validated['brief'],
            'status' => $validated['status'] ?? 'Pending',
            'agreed_price' => $validated['agreed_price'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
        ]);

        return $this->successResponse(
            new OrderResource($order->fresh($this->defaultRelations())),
            'Pesanan berhasil dibuat',
            201
        );
    }

    /**
     * Get one order for administrator.
     */
    public function show(Order $order): JsonResponse
    {
        Gate::authorize('view', $order);

        return $this->successResponse(
            new OrderResource($order->load($this->detailRelations())),
            'Detail pesanan berhasil diambil'
        );
    }

    /**
     * Update order status for administrator.
     */
    public function updateStatus(OrderStatusUpdateRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('update', $order);

        $order->update($request->validated());

        return $this->successResponse(
            new OrderResource($order->fresh($this->defaultRelations())),
            'Status pesanan berhasil diperbarui'
        );
    }

    /**
     * Delete an order for administrator.
     */
    public function destroy(Order $order): JsonResponse
    {
        Gate::authorize('delete', $order);

        $order->delete();

        return $this->successResponse(null, 'Pesanan berhasil dihapus');
    }

    /**
     * Get authenticated client orders.
     */
    public function clientIndex(Request $request): JsonResponse
    {
        $orders = $this->baseOrderQuery()
            ->with('offers')
            ->where('client_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn($order) => (new OrderResource($order))->toArray($request))
            ->values();

        return $this->successResponse($orders, 'Data pesanan klien berhasil diambil');
    }

    /**
     * Get one authenticated client order.
     */
    public function clientShow(Request $request, Order $order): JsonResponse
    {
        if (! $this->belongsToClient($order, $request->user()->id)) {
            return $this->errorResponse('Anda tidak memiliki akses ke pesanan ini', 403);
        }

        return $this->successResponse(
            new OrderResource($order->load($this->detailRelations())),
            'Detail pesanan berhasil diambil'
        );
    }

    /**
     * Store a new order for authenticated client.
     */
    public function clientStore(Request $request): JsonResponse
    {
        Gate::authorize('create', Order::class);

        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'brief' => ['required', 'string'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:51200'],
            'deadline' => ['nullable', 'date'],
        ]);

        $service = Service::with('freelancer')->findOrFail($validated['service_id']);

        if ($response = $this->ensureOrderableService($service)) {
            return $response;
        }

        $order = Order::create([
            'service_id' => $service->id,
            'client_id' => $request->user()->id,
            'freelancer_id' => $service->freelancer_id,
            'brief' => $validated['brief'],
            'deadline' => $validated['deadline'] ?? null,
            'status' => 'Pending',
            'agreed_price' => null,
        ]);

        $this->storeUploadedAttachments($order, $request->file('attachments') ?? [], 'client');
        $this->notifyFreelancer(
            $service->freelancer_id,
            'Pesanan Baru Masuk',
            "Klien mengajukan pesanan baru untuk layanan '{$service->title}'. Silakan periksa detail pesanan.",
            'info',
            url('/freelancer/orders/' . $order->id)
        );

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Pesanan berhasil dibuat',
            201
        );
    }

    /**
     * Upload attachments to an authenticated client order.
     */
    public function uploadAttachment(OrderAttachmentStoreRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('clientAction', $order);

        $this->storeUploadedAttachments($order, $request->file('file') ?? [], 'client');

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Attachment berhasil diupload'
        );
    }

    /**
     * Get checkout summary for authenticated client.
     */
    public function accept(Request $request, Order $order): JsonResponse
    {
        if ($request->user()?->getRole() === 'client') {
            return $this->clientAccept($request, $order);
        }

        if ($request->user()?->getRole() === 'freelancer') {
            Gate::authorize('freelancerAction', $order);

            if ($response = $this->ensureApprovedFreelancer($request)) {
                return $response;
            }

            $validated = $request->validate([
                'agreed_price' => ['required', 'numeric', 'min:0'],
                'note' => ['nullable', 'string'],
            ]);

            $order->update([
                'agreed_price' => $validated['agreed_price'],
                'status' => 'Negotiated',
            ]);

            if ($request->filled('note')) {
                $order->negotiations()->create([
                    'sender' => 'freelancer',
                    'message' => $validated['note'],
                    'proposed_price' => $validated['agreed_price'],
                    'status' => 'Pending',
                ]);
            }

            $this->notifyClient(
                $order->client_id,
                'Tawaran Harga dari Freelancer',
                'Freelancer mengajukan kesepakatan harga baru sebesar Rp ' . number_format((float) $validated['agreed_price'], 0, ',', '.') . '. Silakan lakukan checkout pembayaran.',
                'warning',
                url('/client/orders/' . $order->id)
            );

            return $this->successResponse(
                new OrderResource($order->fresh($this->detailRelations())),
                'Pesanan diterima dengan penawaran baru'
            );
        }

        return $this->errorResponse('Tidak memiliki izin untuk melakukan aksi ini.', 403);
    }

    public function reject(OrderRejectRequest $request, Order $order): JsonResponse
    {
        if ($request->user()?->getRole() === 'client') {
            return $this->clientReject($request, $order);
        }

        if ($request->user()?->getRole() === 'freelancer') {
            return $this->freelancerReject($request, $order);
        }

        return $this->errorResponse('Tidak memiliki izin untuk melakukan aksi ini.', 403);
    }

    /**
     * Get checkout summary for authenticated client.
     */
    public function clientAccept(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('clientAction', $order);

        if ($response = $this->ensureCheckoutableOrder($order)) {
            return $response;
        }

        return $this->successResponse(
            new OrderResource($order->load($this->defaultRelations())),
            'Pesanan dapat dilanjutkan ke checkout.'
        );
    }

    /**
     * Get checkout summary for authenticated client.
     */
    public function checkout(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('clientAction', $order);

        if ($response = $this->ensureCheckoutableOrder($order)) {
            return $response;
        }

        $price = (float) $order->agreed_price;
        $platformFee = $price * 0.1;

        return $this->successResponse([
            'order' => new OrderResource($order->load($this->defaultRelations())),
            'summary' => [
                'agreed_price' => $price,
                'platform_fee' => $platformFee,
                'total' => $price + $platformFee,
            ],
        ], 'Ringkasan checkout berhasil diambil');
    }

    /**
     * Process simulated payment for authenticated client.
     */
    public function processPayment(OrderPaymentRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('clientAction', $order);

        if ($response = $this->ensureCheckoutableOrder($order)) {
            return $response;
        }

        if ($order->transactions()->where('status', 'Paid')->exists()) {
            return $this->errorResponse('Pembayaran sudah pernah dilakukan.', 409);
        }

        $paymentMethod = $request->validated('payment_method');
        $price = (float) $order->agreed_price;
        $platformFee = $price * 0.1;
        $total = $price + $platformFee;

        $order->update(['status' => 'Paid']);
        $order->transactions()->create([
            'order_id' => $order->id,
            'amount' => $total,
            'type' => 'Full',
            'status' => 'Paid',
        ]);

        $this->notifyFreelancer(
            $order->freelancer_id,
            'Pembayaran Pesanan Diterima',
            "Klien telah melunasi pembayaran untuk pesanan '{$order->service?->title}'. Status berubah menjadi 'Paid'. Silakan mulai pengerjaan project.",
            'success',
            url('/freelancer/orders/' . $order->id)
        );

        $methodLabel = $this->paymentMethodLabels()[$paymentMethod] ?? 'QRIS';

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Pembayaran sebesar Rp ' . number_format($total, 0, ',', '.') . ' via ' . $methodLabel . ' berhasil!'
        );
    }

    /**
     * Reject/cancel an authenticated client order.
     */
    public function clientReject(OrderRejectRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('clientAction', $order);

        $order->update(['status' => 'Cancelled']);
        $order->negotiations()->create([
            'sender' => 'client',
            'message' => 'Order ditolak. Alasan: ' . $request->validated('reason'),
        ]);

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Pesanan telah ditolak.'
        );
    }

    /**
     * Send negotiation from authenticated client.
     */
    public function clientNegotiate(OrderNegotiationRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('clientAction', $order);

        $validated = $request->validated();

        $order->negotiations()->create([
            'sender' => 'client',
            'message' => 'Negosiasi harga: ' . $validated['reason'] . "\n\nHarga tawaran: Rp " . number_format($validated['new_price'], 0, ',', '.') . "\n\nDetail: " . ($validated['description'] ?? '-'),
            'proposed_price' => $validated['new_price'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'Pending',
        ]);

        if ($order->status === 'Pending') {
            $order->update(['status' => 'Negotiated']);
        }

        $this->notifyFreelancer(
            $order->freelancer_id,
            'Permintaan Negosiasi Klien',
            'Klien mengajukan tawaran harga baru sebesar Rp ' . number_format($validated['new_price'], 0, ',', '.') . '.',
            'warning',
            url('/freelancer/orders/' . $order->id)
        );

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Negosiasi berhasil dikirim ke freelancer.'
        );
    }

    /**
     * Request revision from authenticated client.
     */
    public function clientRequestRevision(OrderRevisionRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('clientAction', $order);

        if (! in_array($order->status, ['In Progress', 'Completed'], true)) {
            return $this->errorResponse('Revision hanya bisa diminta pada pekerjaan yang sedang berlangsung atau sudah selesai.', 422);
        }

        $validated = $request->validated();

        $order->negotiations()->create([
            'sender' => 'client',
            'message' => 'Permintaan Revisi: ' . $validated['reason'] . "\n\nDetail: " . ($validated['description'] ?? '-'),
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'Pending',
        ]);

        $order->update(['status' => 'Revision']);

        $this->notifyFreelancer(
            $order->freelancer_id,
            'Permintaan Revisi dari Klien',
            "Klien meminta perbaikan/revisi hasil kerja untuk project '{$order->service?->title}'. Alasan: {$validated['reason']}",
            'warning',
            url('/freelancer/orders/' . $order->id)
        );

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Permintaan revisi berhasil dikirim.'
        );
    }

    /**
     * Complete an order after client accepts freelancer result.
     */
    public function clientComplete(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('clientAction', $order);

        if (! $order->service_id || ! $order->service?->freelancer_id) {
            return $this->errorResponse('Aksi ini hanya berlaku untuk order client dan freelancer.', 422);
        }

        if ($order->status !== 'In Progress') {
            return $this->errorResponse('Order tidak dalam tahap pengerjaan.', 422);
        }

        if ($order->results()->count() === 0) {
            return $this->errorResponse('Belum ada hasil kerja dari freelancer.', 422);
        }

        $order->update(['status' => 'Completed']);

        $this->notifyFreelancer(
            $order->freelancer_id,
            'Project Diterima & Selesai',
            "Selamat! Klien telah menerima hasil pekerjaan Anda untuk project '{$order->service?->title}'. Status pesanan: Completed.",
            'success',
            url('/freelancer/orders/' . $order->id)
        );

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Hasil pekerjaan berhasil diterima. Terima kasih!'
        );
    }

    /**
     * Get authenticated freelancer orders.
     */
    public function freelancerIndex(Request $request): JsonResponse
    {
        $orders = $this->baseOrderQuery()
            ->with('offers')
            ->where(function ($query) use ($request) {
                $query->where('freelancer_id', $request->user()->id)
                    ->orWhereHas('service', fn($serviceQuery) => $serviceQuery->where('freelancer_id', $request->user()->id));
            })
            ->latest()
            ->get()
            ->map(fn($order) => (new OrderResource($order))->toArray($request))
            ->values();

        return $this->successResponse($orders, 'Data pesanan freelancer berhasil diambil');
    }

    /**
     * Get one authenticated freelancer order.
     */
    public function freelancerShow(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('view', $order);

        return $this->successResponse(
            new OrderResource($order->load($this->detailRelations())),
            'Detail pesanan berhasil diambil'
        );
    }

    /**
     * Update order status for authenticated freelancer.
     */
    public function updateStatusFreelancer(OrderStatusUpdateRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('freelancerAction', $order);

        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        $order->update($request->validated());

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Status pesanan berhasil diperbarui'
        );
    }

    /**
     * Update agreed price for authenticated freelancer.
     */
    public function updateAgreedPrice(OrderAgreedPriceRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('freelancerAction', $order);

        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        $validated = $request->validated();

        $order->update([
            'agreed_price' => $validated['agreed_price'],
            'status' => 'Negotiated',
        ]);

        if ($request->filled('note')) {
            $order->negotiations()->create([
                'sender' => 'freelancer',
                'message' => $validated['note'],
                'proposed_price' => $validated['agreed_price'],
                'status' => 'Pending',
            ]);
        }

        $this->notifyClient(
            $order->client_id,
            'Tawaran Harga dari Freelancer',
            'Freelancer mengajukan kesepakatan harga baru sebesar Rp ' . number_format((float) $validated['agreed_price'], 0, ',', '.') . '. Silakan lakukan checkout pembayaran.',
            'warning',
            url('/client/orders/' . $order->id)
        );

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Penawaran harga berhasil dikirim'
        );
    }

    /**
     * Accept order with a price offer for authenticated freelancer.
     */
    public function freelancerAccept(OrderAgreedPriceRequest $request, Order $order): JsonResponse
    {
        return $this->updateAgreedPrice($request, $order);
    }

    /**
     * Reject/cancel an order for authenticated freelancer.
     */
    public function freelancerReject(OrderRejectRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('freelancerAction', $order);

        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        $reason = $request->validated('reason');

        $order->update([
            'status' => 'Cancelled',
            'brief' => $order->brief . "\n\nRejection Reason: " . $reason,
        ]);

        $this->notifyClient(
            $order->client_id,
            'Pesanan Ditolak Freelancer',
            "Freelancer menolak pesanan Anda. Alasan: '{$reason}'",
            'danger',
            url('/client/orders/' . $order->id)
        );

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Pesanan telah ditolak'
        );
    }

    /**
     * Approve revision request for authenticated freelancer.
     */
    public function freelancerApproveRevision(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('freelancerAction', $order);

        if ($order->status !== 'Revision') {
            return $this->errorResponse('Order bukan dalam status revisi.', 422);
        }

        $order->update(['status' => 'In Progress']);
        $order->negotiations()->create([
            'sender' => 'freelancer',
            'message' => '[REVISION APPROVED] Revisi telah disetujui dan akan segera dikerjakan.',
            'status' => 'Approved',
        ]);

        $this->notifyClient(
            $order->client_id,
            'Permintaan Revisi Disetujui',
            "Freelancer menyetujui pengerjaan revisi Anda. Pekerjaan kembali berstatus 'In Progress'.",
            'success',
            url('/client/orders/' . $order->id)
        );

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Revisi disetujui. Pengerjaan revisi dimulai.'
        );
    }

    /**
     * Reject revision request for authenticated freelancer.
     */
    public function freelancerRejectRevision(OrderRejectRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('freelancerAction', $order);

        if ($order->status !== 'Revision') {
            return $this->errorResponse('Order bukan dalam status revisi.', 422);
        }

        $reason = $request->validated('reason');

        $order->update(['status' => 'Completed']);
        $order->negotiations()->create([
            'sender' => 'freelancer',
            'message' => '[REVISION REJECTED] Revisi ditolak. Alasan: ' . $reason,
            'reason' => $reason,
            'status' => 'Rejected',
        ]);

        $this->notifyClient(
            $order->client_id,
            'Permintaan Revisi Ditolak',
            "Freelancer menolak permintaan revisi Anda. Alasan: '{$reason}'. Status kembali ke Completed.",
            'danger',
            url('/client/orders/' . $order->id)
        );

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Revisi ditolak.'
        );
    }

    private function baseOrderQuery()
    {
        return Order::query()->with($this->defaultRelations());
    }

    /**
     * @return array<int, string>
     */
    private function defaultRelations(): array
    {
        return [
            'service.category',
            'service.freelancer.skomda_student',
            'client',
            'freelancer.skomda_student',
            'transactions',
            'attachments',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function detailRelations(): array
    {
        return [
            ...$this->defaultRelations(),
            'negotiations',
            'offers',
            'results',
            'review',
        ];
    }

    private function belongsToClient(Order $order, int $clientId): bool
    {
        return (int) $order->client_id === $clientId;
    }

    private function belongsToFreelancer(Order $order, int $freelancerId): bool
    {
        return (int) ($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancerId;
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

    private function ensureOrderableService(Service $service): ?JsonResponse
    {
        if ($service->status !== 'Approved') {
            return $this->errorResponse('Layanan tidak tersedia.', 422);
        }

        if (! $service->freelancer_id || ! $service->freelancer || $service->freelancer->status !== 'Approved') {
            return $this->errorResponse('Layanan tidak tersedia.', 422);
        }

        return null;
    }

    private function ensureCheckoutableOrder(Order $order): ?JsonResponse
    {
        if (! $order->service_id || ! $order->service?->freelancer_id) {
            return $this->errorResponse('Transaksi hanya tersedia untuk order client dan freelancer.', 422);
        }

        if (! in_array($order->status, ['Pending', 'Negotiated'], true)) {
            return $this->errorResponse('Pembayaran tidak dapat diproses.', 422);
        }

        if (! $order->agreed_price) {
            return $this->errorResponse('Belum ada harga yang disepakati.', 422);
        }

        return null;
    }

    private function storeUploadedAttachments(Order $order, array $files, string $uploadedBy): void
    {
        $existingCount = $order->attachments()->count();
        $remaining = max(0, 10 - $existingCount);

        foreach (array_slice($files, 0, $remaining) as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('order-attachments', 'public');

            OrderAttachment::create([
                'order_id' => $order->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $uploadedBy,
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    private function paymentMethodLabels(): array
    {
        return [
            'qris' => 'QRIS',
            'va_bca' => 'BCA Virtual Account',
            'va_mandiri' => 'Mandiri Virtual Account',
            'va_bri' => 'BRI Virtual Account',
        ];
    }

    private function notifyFreelancer(?int $freelancerId, string $title, string $message, string $type, string $link): void
    {
        if (! $freelancerId) {
            return;
        }

        Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'role' => 'freelancer',
            'user_id' => $freelancerId,
            'link' => $link,
        ]);
    }

    private function notifyClient(?int $clientId, string $title, string $message, string $type, string $link): void
    {
        if (! $clientId) {
            return;
        }

        Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'role' => 'client',
            'user_id' => $clientId,
            'link' => $link,
        ]);
    }
}
