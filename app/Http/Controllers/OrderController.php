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

        $query = Order::with(['service.freelancer.skomda_student', 'client']);

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

        $orders = $query->latest()->paginate(12)->withQueryString();

        // Data for dropdowns in Add Order modal (if still needed)
        $clients = \App\Models\Client::orderBy('name')->get();
        $freelancers = \App\Models\Freelancer::with('skomda_student')->get();
        $services = \App\Models\Service::where('status', 'Approved')->orderBy('title')->get();

        return view('dashboard.admin.orders', compact('orders', 'clients', 'freelancers', 'services'));
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

        $service->load(['freelancer.skomda_student', 'service_category']);

        return view('dashboard.client.orders.create', compact('service'));
    }

    public function storePage(Request $request)
    {
        $client = $request->user('client');

        $validated = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'brief' => 'required|string',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        if ($service->status !== 'Approved') {
            return redirect()->route('client.services.index')->with('error', 'Layanan tidak tersedia.');
        }

        $order = Order::create([
            'service_id' => $service->id,
            'client_id' => $client->id,
            'freelancer_id' => $service->freelancer_id,
            'brief' => $validated['brief'],
            'status' => 'Pending',
            'agreed_price' => null,
        ]);

        return redirect()->route('client.orders.show', $order->id)->with('success', 'Order berhasil dibuat');
    }

    public function uploadAttachment(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->store('order-attachments', 'public');

        \App\Models\OrderAttachment::create([
            'order_id' => $order->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => 'client',
        ]);

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
        $freelancer = auth('freelancer')->user();
        abort_unless(($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancer->id, 403);

        $validated = $request->validate([
            'agreed_price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $order->update([
            'agreed_price' => $validated['agreed_price'],
            'status' => 'Negotiated',
        ]);

        // Opsional: Jika ada sistem pesan, simpan catatan sebagai pesan negosiasi
        if ($request->filled('note')) {
            $order->negotiations()->create([
                'sender' => 'freelancer',
                'message' => $validated['note'],
            ]);
        }

        return redirect()->route('freelancer.orders.show', $order->id)->with('success', 'Pesanan diterima dengan penawaran baru');
    }

    public function freelancerReject(Request $request, Order $order)
    {
        $freelancer = auth('freelancer')->user();

        // Cek akses: Pesanan harus milik service yang dikelola freelancer ini
        abort_unless(($order->freelancer_id ?? $order->service?->freelancer_id) === $freelancer->id, 403);

        $request->validate([
            'reason' => 'required|string',
        ]);

        $order->update([
            'status' => 'Cancelled',
            'brief' => $order->brief."\n\nRejection Reason: ".$request->reason,
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

    // =========================
    // CLIENT: Accept Order (after freelancer sends price)
    // =========================
    public function clientAcceptOrder(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        if (!in_array($order->status, ['Pending', 'Negotiated'])) {
            return redirect()->back()->with('error', 'Order tidak dapat diterima.');
        }

        if (! $order->agreed_price) {
            return redirect()->back()->with('error', 'Belum ada harga yang disepakati.');
        }

        return redirect()->route('client.orders.checkout', $order->id);
    }

    public function checkout(Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

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

        if (!in_array($order->status, ['Pending', 'Negotiated'])) {
            return redirect()->route('client.orders.show', $order->id)->with('error', 'Pembayaran tidak dapat diproses.');
        }

        // Idempotency: prevent double payment
        if ($order->transactions()->where('status', 'Paid')->exists()) {
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

        $order->transactions()->create([
            'order_id' => $order->id,
            'amount' => $total,
            'type' => 'Full',
            'status' => 'Paid',
        ]);

        $methodLabel = $paymentMethodLabels[$request->payment_method] ?? 'QRIS';

        return redirect()->route('client.orders.show', $order->id)->with('success', 'Pembayaran sebesar Rp '.number_format($total, 0, ',', '.').' via '.$methodLabel.' berhasil!');
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
            'message' => 'Order ditolak. Alasan: '.$request->reason,
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
            'message' => 'Negosiasi harga: '.$validated['reason']."\n\nHarga tawaran: Rp ".number_format($validated['new_price'], 0, ',', '.')."\n\nDetail: ".($validated['description'] ?? '-'),
        ]);

        if ($order->status === 'Pending') {
            $order->update(['status' => 'Negotiated']);
        }

        return redirect()->back()->with('success', 'Negosiasi berhasil dikirim ke freelancer.');
    }

    // =========================
    // CLIENT: Request Revision
    // =========================
    public function clientRequestRevision(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        if (! in_array($order->status, ['In Progress', 'Completed'])) {
            return redirect()->back()->with('error', 'Revision hanya bisa diminta pada pekerjaan yang sedang berlangsung atau sudah selesai.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'description' => 'nullable|string|max:2000',
        ]);

        $order->negotiations()->create([
            'sender' => 'client',
            'message' => 'Permintaan Revisi: '.$validated['reason']."\n\nDetail: ".($validated['description'] ?? '-'),
        ]);

        $order->update(['status' => 'Revision']);

        return redirect()->back()->with('success', 'Permintaan revisi berhasil dikirim.');
    }

    // =========================
    // CLIENT: Complete Order (Terima Hasil)
    // =========================
    public function clientCompleteOrder(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        if ($order->status !== 'In Progress') {
            return redirect()->back()->with('error', 'Order tidak dalam tahap pengerjaan.');
        }

        if ($order->results->count() === 0) {
            return redirect()->back()->with('error', 'Belum ada hasil kerja dari freelancer.');
        }

        $order->update(['status' => 'Completed']);

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
            'message' => '[REVISION REJECTED] Revisi ditolak. Alasan: '.$request->reason,
        ]);

        return redirect()->back()->with('error', 'Revisi ditolak.');
    }
}
