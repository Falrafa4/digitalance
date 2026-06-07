<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private const STATUS_RULE = 'required|in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled';

    // =========================
    // ADMIN ONLY
    // =========================
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('q');
        $payout = strtolower(trim((string) $request->query('payout', 'all')));

        $query = Order::with(['service.freelancer.skomda_student', 'client', 'transactions']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('service', function ($sq) use ($search) {
                        $sq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        if ($payout === 'paid') {
            $query->whereHas('transactions', function ($trx) {
                $trx->where('type', 'Full')->where('status', 'Paid');
            });
        } elseif ($payout === 'pending') {
            $query->where('status', 'Completed')
                ->whereDoesntHave('transactions', function ($trx) {
                    $trx->where('type', 'Full')->where('status', 'Paid');
                });
        }

        $orders = $query->latest()->paginate(12)->withQueryString();

        // Data for dropdowns in Add Order modal (if still needed)
        $clients = \App\Models\Client::orderBy('name')->get();
        $freelancers = \App\Models\Freelancer::with('skomda_student')->get();
        $services = Service::where('status', 'Approved')->orderBy('title')->get();

        return view('dashboard.admin.orders', compact('orders', 'clients', 'freelancers', 'services', 'payout'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate($this->statusValidationRules());

        $order = Order::findOrFail($id);
        $order->update($validated);

        return redirect()->route('admin.orders.index')->with('success', 'Status order berhasil diperbarui');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'service_id' => 'required|integer|exists:services,id',
            'brief' => 'required|string',
            'status' => 'nullable|in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled',
            'agreed_price' => 'nullable|decimal:2',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        Order::create([
            ...$validated,
            'freelancer_id' => $service->freelancer_id,
            'status' => $validated['status'] ?? 'Pending',
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Order berhasil dibuat');
    }

    // =========================
    // CLIENT PANEL (PAGE)
    // =========================

    public function clientIndexPage()
    {
        $client = auth('client')->user();

        $orders = Order::with(['service.freelancer.skomda_student', 'client', 'offers'])
            ->where('client_id', $client->id)
            ->latest()
            ->get();

        return view('dashboard.client.orders.index', compact('orders'));
    }

    public function clientShowPage(Order $order)
    {
        $client = auth('client')->user();

        abort_unless($order->client_id === $client->id, 403);

        $order->load([
            'service.freelancer.skomda_student',
            'service.service_category',
            'negotiations',
            'offers',
            'transactions',
            'results',
            'review',
            'attachments',
        ]);

        return view('dashboard.client.orders.show', compact('order'));
    }

    public function create(Service $service)
    {
        if ($service->status !== 'Approved') {
            return redirect()->route('client.services.index')->with('warning', 'Layanan tidak tersedia untuk diorder.');
        }

        if (!$service->freelancer_id || !$service->freelancer || $service->freelancer->status !== 'Approved') {
            return redirect()->route('client.services.index')->with('warning', 'Layanan tidak tersedia untuk diorder.');
        }

        $service->load(['category:id,name', 'freelancer.skomda_student']);

        $freelancerServices = Service::with('category:id,name')
            ->where('freelancer_id', $service->freelancer_id)
            ->where('status', 'Approved')
            ->whereHas('freelancer', function ($query) {
                $query->where('status', 'Approved');
            })
            ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$service->id])
            ->latest()
            ->get();

        return view('dashboard.client.orders.create', compact('service', 'freelancerServices'));
    }

    public function storePage(Request $request)
    {
        $client = $request->user('client');
        $validated = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'brief' => 'required|string',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|max:51200',
            'deadline' => 'nullable|date',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        if ($service->status !== 'Approved') {
            return redirect()->route('client.services.index')->with('error', 'Layanan tidak tersedia.');
        }

        if (!$service->freelancer_id || !$service->freelancer || $service->freelancer->status !== 'Approved') {
            return redirect()->route('client.services.index')->with('error', 'Layanan tidak tersedia.');
        }

        $order = Order::create([
            'service_id' => $service->id,
            'client_id' => $client->id,
            'freelancer_id' => $service->freelancer_id,
            'brief' => $validated['brief'],
            'deadline' => $validated['deadline'] ?? null,
            'status' => 'Pending',
            'agreed_price' => null,
        ]);

        $this->storeUploadedAttachments($order, $request->file('attachments') ?? [], 'client');

        // NOTIFIKASI: Beritahu Freelancer bahwa ada pesanan masuk
        \App\Models\Notification::create([
            'title' => 'Pesanan Baru Masuk',
            'message' => "Klien mengajukan pesanan baru untuk layanan '{$service->title}'. Silakan periksa detail pesanan.",
            'type' => 'info',
            'role' => 'freelancer',
            'user_id' => $service->freelancer_id,
            'link' => route('freelancer.orders.show', $order->id),
        ]);

        return redirect()->route('client.orders.show', $order->id)->with('success', 'Order berhasil dibuat');
    }

    public function uploadAttachment(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        $request->validate([
            'file' => 'required|array|max:10',
            'file.*' => 'file|max:51200',
        ]);

        $this->storeUploadedAttachments($order, $request->file('file') ?? [], 'client');

        return back()->with('success', 'Attachment berhasil diupload');
    }

    public function clientProjects()
    {
        $client = auth('client')->user();

        $projects = Order::with(['service'])
            ->where('client_id', $client->id)
            ->whereIn('status', ['Pending', 'Negotiated', 'Paid', 'In Progress', 'Revision'])
            ->latest()
            ->get();

        return view('dashboard.client.projects.index', compact('projects'));
    }

    public function clientHistory()
    {
        $client = auth('client')->user();

        $orders = Order::with(['service', 'review'])
            ->where('client_id', $client->id)
            ->whereIn('status', ['Completed', 'Cancelled'])
            ->latest()
            ->get();

        return view('dashboard.client.history', compact('orders'));
    }

    // =========================
    // FREELANCER ONLY
    // =========================

    public function freelancerIndex(Request $request)
    {
        $freelancer = $request->user('freelancer');

        $orders = Order::with(['service.freelancer.skomda_student', 'client', 'offers'])
            ->whereHas('service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })
            ->latest()
            ->get();

        return view('dashboard.freelancer.orders', compact('orders'));
    }

    public function freelancerShow(Order $order)
    {
        $freelancer = auth('freelancer')->user();

        abort_unless(($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancer->id, 403);

        $order->load(['service.service_category', 'client', 'negotiations', 'offers', 'transactions', 'results', 'review', 'attachments']);

        return view('dashboard.freelancer.orders.show', compact('order'));
    }

    public function updateStatusFreelancer(Request $request, string $id)
    {
        if ($resp = $this->ensureFreelancerApproved())
            return $resp;

        $validated = $request->validate($this->statusValidationRules());

        $order = Order::findOrFail($id);

        // Cek akses
        $freelancer = auth('freelancer')->user();
        abort_unless(($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancer->id, 403);

        $order->update($validated);

        return redirect()->route('freelancer.orders.index')->with('success', 'Status order berhasil diperbarui');
    }

    public function updateAgreedPrice(Request $request, string $id)
    {
        if ($resp = $this->ensureFreelancerApproved())
            return $resp;

        // Sanitize possible localized Rupiah input (e.g. "2.220.000") before validation
        $request->merge(['agreed_price' => $this->sanitizeRupiahInput($request->input('agreed_price'))]);

        $validated = $request->validate([
            'agreed_price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);

        // Cek akses
        $freelancer = auth('freelancer')->user();
        abort_unless(($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancer->id, 403);

        $order->update([
            'agreed_price' => $validated['agreed_price'],
            'status' => 'Negotiated',
        ]);

        return redirect()->route('freelancer.orders.show', $order->id)->with('success', 'Penawaran harga berhasil dikirim');
    }

    public function freelancerAccept(Request $request, Order $order)
    {
        if ($resp = $this->ensureFreelancerApproved())
            return $resp;

        $freelancer = auth('freelancer')->user();
        abort_unless(($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancer->id, 403);

        // Sanitize rupiah formatted input before validation
        $request->merge(['agreed_price' => $this->sanitizeRupiahInput($request->input('agreed_price'))]);

        $validated = $request->validate([
            'agreed_price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $order->update([
            'agreed_price' => $validated['agreed_price'],
            'status' => 'Negotiated',
        ]);

        if ($request->filled('note')) {
            $order->negotiations()->create([
                'sender' => 'freelancer',
                'message' => $validated['note'],
            ]);
        }

        // NOTIFIKASI: Beritahu Klien tentang harga negosiasi baru dari Freelancer
        \App\Models\Notification::create([
            'title' => 'Tawaran Harga dari Freelancer',
            'message' => "Freelancer mengajukan kesepakatan harga baru sebesar Rp " . number_format($validated['agreed_price'], 0, ',', '.') . ". Silakan lakukan checkout pembayaran.",
            'type' => 'warning',
            'role' => 'client',
            'user_id' => $order->client_id,
            'link' => route('client.orders.show', $order->id),
        ]);

        return redirect()->route('freelancer.orders.show', $order->id)->with('success', 'Pesanan diterima dengan penawaran baru');
    }

    public function freelancerReject(Request $request, Order $order)
    {
        if ($resp = $this->ensureFreelancerApproved())
            return $resp;

        $freelancer = auth('freelancer')->user();
        abort_unless(($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancer->id, 403);

        $request->validate([
            'reason' => 'required|string',
        ]);

        $order->update([
            'status' => 'Cancelled',
            'brief' => $order->brief . "\n\nRejection Reason: " . $request->reason,
        ]);

        // NOTIFIKASI: Informasikan ke Klien bahwa pesanan dibatalkan/ditolak oleh Freelancer
        \App\Models\Notification::create([
            'title' => 'Pesanan Ditolak Freelancer',
            'message' => "Freelancer menolak pesanan Anda. Alasan: '{$request->reason}'",
            'type' => 'danger',
            'role' => 'client',
            'user_id' => $order->client_id,
            'link' => route('client.orders.show', $order->id),
        ]);

        return redirect()->route('freelancer.orders.index')->with('success', 'Pesanan telah ditolak');
    }

    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order berhasil dihapus');
    }

    private function statusValidationRules(): array
    {
        return [
            'status' => self::STATUS_RULE,
        ];
    }

    private function storeUploadedAttachments(Order $order, array $files, string $uploadedBy): void
    {
        $existingCount = $order->attachments()->count();
        $remaining = max(0, 10 - $existingCount);

        foreach (array_slice($files, 0, $remaining) as $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store('order-attachments', 'public');

            \App\Models\OrderAttachment::create([
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
     * Sanitize localized Rupiah input into plain numeric value.
     * Accepts strings like "2.220.000", "2,220,000" or "2220000.00" and returns numeric string or null.
     */
    private function sanitizeRupiahInput($value)
    {
        if ($value === null)
            return null;

        if (is_numeric($value))
            return $value;

        $raw = trim((string) $value);
        if ($raw === '')
            return null;

        // Remove currency symbol and spaces
        $clean = preg_replace('/[^0-9,\.\-]/u', '', $raw);

        // If contains comma and comma likely decimal separator (e.g. '1234,56')
        if (strpos($clean, ',') !== false && preg_match('/,[0-9]{1,2}$/', $clean)) {
            $normalized = str_replace('.', '', $clean); // remove thousand sep
            $normalized = str_replace(',', '.', $normalized); // make decimal dot
            return is_numeric($normalized) ? $normalized : null;
        }

        // Otherwise treat comma as thousand separator as well: remove dots and commas
        $normalized = str_replace(['.', ','], '', $clean);
        return is_numeric($normalized) ? $normalized : null;
    }

    // =========================
    // CLIENT: Accept Order (after freelancer sends price)
    // =========================
    public function clientAcceptOrder(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        if (!$order->service_id || !$order->service?->freelancer_id) {
            return redirect()->back()->with('error', 'Transaksi hanya tersedia untuk order client dan freelancer.');
        }

        if (!in_array($order->status, ['Pending', 'Negotiated'])) {
            return redirect()->back()->with('error', 'Order tidak dapat diterima.');
        }

        if (!$order->agreed_price) {
            return redirect()->back()->with('error', 'Belum ada harga yang disepakati.');
        }

        return redirect()->route('client.orders.checkout', $order->id);
    }

    public function checkout(Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        if (!$order->service_id || !$order->service?->freelancer_id) {
            return redirect()->route('client.orders.show', $order->id)->with('error', 'Transaksi hanya tersedia untuk order client dan freelancer.');
        }

        if (!in_array($order->status, ['Pending', 'Negotiated'])) {
            return redirect()->route('client.orders.show', $order->id)->with('error', 'Pembayaran tidak dapat diproses.');
        }

        $order->load(['service.freelancer.skomda_student', 'service.service_category', 'freelancer.skomda_student']);

        return view('dashboard.client.orders.checkout', compact('order'));
    }

    public function processPayment(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);
        $expectsJson = $request->expectsJson();

        if (!$order->service_id || !$order->service?->freelancer_id) {
            if ($expectsJson) {
                return response()->json(['message' => 'Transaksi hanya tersedia untuk order client dan freelancer.'], 422);
            }

            return redirect()->route('client.orders.show', $order->id)->with('error', 'Transaksi hanya tersedia untuk order client dan freelancer.');
        }

        if (!in_array($order->status, ['Pending', 'Negotiated'])) {
            if ($expectsJson) {
                return response()->json(['message' => 'Pembayaran tidak dapat diproses.'], 422);
            }

            return redirect()->route('client.orders.show', $order->id)->with('error', 'Pembayaran tidak dapat diproses.');
        }

        // Idempotency: prevent double payment
        if ($order->transactions()->where('status', 'Paid')->exists()) {
            if ($expectsJson) {
                return response()->json(['message' => 'Pembayaran sudah pernah dilakukan.'], 409);
            }

            return redirect()->route('client.orders.show', $order->id)->with('warning', 'Pembayaran sudah pernah dilakukan.');
        }

        // Validasi payment method
        $request->validate([
            'payment_method' => 'required|in:qris,va_bca,va_mandiri,va_bri',
        ]);

        // Hitung total dengan biaya platform 10%
        $price = (float) $order->agreed_price;
        $platformFee = $price * 0.1;
        $total = $price + $platformFee;

        // Simulasikan Payment Berhasil
        $order->update(['status' => 'Paid']);

        $paymentMethodLabels = [
            'qris' => 'QRIS',
            'va_bca' => 'BCA Virtual Account',
            'va_mandiri' => 'Mandiri Virtual Account',
            'va_bri' => 'BRI Virtual Account',
        ];

        // 1. Sistem mencatat transaksi ke database
        $order->transactions()->create([
            'order_id' => $order->id,
            'amount' => $total,
            'type' => 'Full',
            'status' => 'Paid',
        ]);

        // ─── DI SINI PENEMPATAN NOTIFIKASINYA ───
        // 2. Kirim Notifikasi real-time agar dibaca sistem polling header freelancer
        \App\Models\Notification::create([
            'title' => 'Pembayaran Pesanan Diterima',
            'message' => "Klien telah melunasi pembayaran untuk pesanan '{$order->service->title}'. Status berubah menjadi 'Paid'. Silakan mulai pengerjaan project.",
            'type' => 'success',
            'role' => 'freelancer',
            'user_id' => $order->freelancer_id,
            'link' => route('freelancer.orders.show', $order->id),
        ]);
        // ────────────────────────────────────────

        $methodLabel = $paymentMethodLabels[$request->payment_method] ?? 'QRIS';

        if ($expectsJson) {
            return response()->json([
                'message' => 'Pembayaran sebesar Rp ' . number_format($total, 0, ',', '.') . ' via ' . $methodLabel . ' berhasil!',
                'redirect' => route('client.orders.show', $order->id),
            ]);
        }

        return redirect()->route('client.orders.show', $order->id)->with('success', 'Pembayaran sebesar Rp ' . number_format($total, 0, ',', '.') . ' via ' . $methodLabel . ' berhasil!');
    }

    // =========================
    // CLIENT: Reject Order
    // =========================
    public function clientRejectOrder(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        $request->validate(['reason' => 'required|string|max:500']);

        $order->update([
            'status' => 'Cancelled',
        ]);

        $order->negotiations()->create([
            'sender' => 'client',
            'message' => 'Order ditolak. Alasan: ' . $request->reason,
        ]);

        return redirect()->route('client.orders.index')->with('success', 'Order telah ditolak.');
    }

    // =========================
    // CLIENT: Negotiate Order
    // =========================
    public function clientNegoOrder(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'new_price' => 'required|integer|min:1000',
            'description' => 'nullable|string|max:2000',
        ]);

        $order->negotiations()->create([
            'sender' => 'client',
            'message' => 'Negosiasi harga: ' . $validated['reason'] . "\n\nHarga tawaran: Rp " . number_format($validated['new_price'], 0, ',', '.') . "\n\nDetail: " . ($validated['description'] ?? '-'),
        ]);

        if ($order->status === 'Pending') {
            $order->update(['status' => 'Negotiated']);
        }

        // NOTIFIKASI: Beritahu Freelancer bahwa Klien meminta negosiasi harga baru
        \App\Models\Notification::create([
            'title' => 'Permintaan Negosiasi Klien',
            'message' => "Klien mengajukan tawaran harga baru sebesar Rp " . number_format($validated['new_price'], 0, ',', '.') . ".",
            'type' => 'warning',
            'role' => 'freelancer',
            'user_id' => $order->freelancer_id,
            'link' => route('freelancer.orders.show', $order->id),
        ]);

        return redirect()->back()->with('success', 'Negosiasi berhasil dikirim ke freelancer.');
    }

    // =========================
    // CLIENT: Request Revision
    // =========================
    public function clientRequestRevision(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        if (!in_array($order->status, ['In Progress', 'Completed'])) {
            return redirect()->back()->with('error', 'Revision hanya bisa diminta pada pekerjaan yang sedang berlangsung atau sudah selesai.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
        ]);

        $order->negotiations()->create([
            'sender' => 'client',
            'message' => 'Permintaan Revisi: ' . $validated['reason'] . "\n\nDetail: " . ($validated['description'] ?? '-'),
        ]);

        $order->update(['status' => 'Revision']);

        // NOTIFIKASI: Beritahu Freelancer bahwa hasilnya perlu direvisi kembali
        \App\Models\Notification::create([
            'title' => 'Permintaan Revisi dari Klien',
            'message' => "Klien meminta perbaikan/revisi hasil kerja untuk project '{$order->service->title}'. Alasan: {$validated['reason']}",
            'type' => 'warning',
            'role' => 'freelancer',
            'user_id' => $order->freelancer_id,
            'link' => route('freelancer.orders.show', $order->id),
        ]);

        return redirect()->back()->with('success', 'Permintaan revisi berhasil dikirim.');
    }

    // =========================
    // CLIENT: Complete Order (Terima Hasil)
    // =========================
    public function clientCompleteOrder(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        if (!$order->service_id || !$order->service?->freelancer_id) {
            return redirect()->back()->with('error', 'Aksi ini hanya berlaku untuk order client dan freelancer.');
        }

        if ($order->status !== 'In Progress') {
            return redirect()->back()->with('error', 'Order tidak dalam tahap pengerjaan.');
        }

        if ($order->results->count() === 0) {
            return redirect()->back()->with('error', 'Belum ada hasil kerja dari freelancer.');
        }

        $order->update(['status' => 'Completed']);

        // NOTIFIKASI: Beritahu Freelancer bahwa project dinyatakan selesai sukses oleh Klien
        \App\Models\Notification::create([
            'title' => 'Project Diterima & Selesai',
            'message' => "Selamat! Klien telah menerima hasil pekerjaan Anda untuk project '{$order->service->title}'. Status pesanan: Completed.",
            'type' => 'success',
            'role' => 'freelancer',
            'user_id' => $order->freelancer_id,
            'link' => route('freelancer.orders.show', $order->id),
        ]);

        return redirect()->back()->with('success', 'Hasil pekerjaan berhasil diterima. Terima kasih!');
    }

    // =========================
    // FREELANCER: Approve Revision
    // =========================
    public function freelancerApproveRevision(Order $order)
    {
        $freelancer = auth('freelancer')->user();
        abort_unless(($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancer->id, 403);

        if ($order->status !== 'Revision') {
            return redirect()->back()->with('error', 'Order bukan dalam status revisi.');
        }

        $order->update(['status' => 'In Progress']);

        $order->negotiations()->create([
            'sender' => 'freelancer',
            'message' => '[REVISION APPROVED] Revisi telah disetujui dan akan segera dikerjakan.',
        ]);

        // NOTIFIKASI: Informasikan ke Klien bahwa Freelancer sedang mengerjakan revisinya
        \App\Models\Notification::create([
            'title' => 'Permintaan Revisi Disetujui',
            'message' => "Freelancer menyetujui pengerjaan revisi Anda. Pekerjaan kembali berstatus 'In Progress'.",
            'type' => 'success',
            'role' => 'client',
            'user_id' => $order->client_id,
            'link' => route('client.orders.show', $order->id),
        ]);

        return redirect()->back()->with('success', 'Revisi disetujui. Pengerjaan revisi dimulai.');
    }

    // =========================
    // FREELANCER: Reject Revision
    // =========================
    public function freelancerRejectRevision(Request $request, Order $order)
    {
        $freelancer = auth('freelancer')->user();
        abort_unless(($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancer->id, 403);
        $request->validate(['reason' => 'required|string|max:500']);

        if ($order->status !== 'Revision') {
            return redirect()->back()->with('error', 'Order bukan dalam status revisi.');
        }

        $order->update(['status' => 'Completed']);

        $order->negotiations()->create([
            'sender' => 'freelancer',
            'message' => '[REVISION REJECTED] Revisi ditolak. Alasan: ' . $request->reason,
        ]);

        // NOTIFIKASI: Beritahu Klien bahwa Freelancer tidak menyetujui revisi dan mengembalikan status ke Selesai
        \App\Models\Notification::create([
            'title' => 'Permintaan Revisi Ditolak',
            'message' => "Freelancer menolak permintaan revisi Anda. Alasan: '{$request->reason}'. Status kembali ke Completed.",
            'type' => 'danger',
            'role' => 'client',
            'user_id' => $order->client_id,
            'link' => route('client.orders.show', $order->id),
        ]);

        return redirect()->back()->with('error', 'Revisi ditolak.');
    }
}
