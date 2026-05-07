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
    public function index()
    {
        $orders = Order::with('service', 'client')->get();
        return view('dashboard.admin.orders', compact('orders'));
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
            'agreed_price' => 'nullable|decimal:2'
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

        $orders = Order::with(['service', 'client'])
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
            'service.service_category', // kalau masih kepakai di view lama
            'negotiations',
            'offers',
            'transactions',
            'results',
            'review'
        ]);

        return view('dashboard.client.orders.show', compact('order'));
    }

    public function create(Service $service)
    {
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

        $order = Order::create([
            'service_id' => $service->id,
            'client_id' => $client->id,
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

        $path = $request->file('file')->store('order-attachments', 'public');

        $order->brief = trim(($order->brief ?? '') . "\n\nAttachment: " . $path);
        $order->save();

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

        $orders = Order::with('service', 'client')
            ->whereHas('service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })
            ->get();

        return view('dashboard.freelancer.orders', compact('orders'));
    }

    public function freelancerShow(Order $order)
    {
        $freelancer = auth('freelancer')->user();
        
        // Cek akses: Pesanan harus milik service yang dikelola freelancer ini
        abort_unless($order->service->freelancer_id === $freelancer->id, 403);

        $order->load(['service.service_category', 'client', 'negotiations', 'offers', 'transactions', 'results', 'review']);

        return view('freelancer.orders.show', compact('order'));
    }

    public function updateStatusFreelancer(Request $request, string $id)
    {
        $validated = $request->validate($this->statusValidationRules());

        $order = Order::findOrFail($id);
        
        // Cek akses
        $freelancer = auth('freelancer')->user();
        abort_unless($order->service->freelancer_id === $freelancer->id, 403);

        $order->update($validated);

        return redirect()->route('freelancer.orders.index')->with('success', 'Status order berhasil diperbarui');
    }

    public function updateAgreedPrice(Request $request, string $id)
    {
        $validated = $request->validate([
            'agreed_price' => 'required|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        $order = Order::findOrFail($id);
        
        // Cek akses
        $freelancer = auth('freelancer')->user();
        abort_unless($order->service->freelancer_id === $freelancer->id, 403);

        $order->update([
            'agreed_price' => $validated['agreed_price'],
            'status' => 'Negotiated'
        ]);

        return redirect()->route('freelancer.orders.show', $order->id)->with('success', 'Penawaran harga berhasil dikirim');
    }

    public function freelancerAccept(Request $request, Order $order)
    {
        $freelancer = auth('freelancer')->user();
        abort_unless($order->service->freelancer_id === $freelancer->id, 403);

        $validated = $request->validate([
            'agreed_price' => 'required|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        $order->update([
            'agreed_price' => $validated['agreed_price'],
            'status' => 'Negotiated'
        ]);

        // Opsional: Jika ada sistem pesan, simpan catatan sebagai pesan negosiasi
        if ($request->filled('note')) {
            $order->negotiations()->create([
                'sender' => 'freelancer',
                'message' => $validated['note']
            ]);
        }

        return redirect()->route('freelancer.orders.show', $order->id)->with('success', 'Pesanan diterima dengan penawaran baru');
    }

    public function freelancerReject(Request $request, Order $order)
    {
        $freelancer = auth('freelancer')->user();
        
        // Cek akses: Pesanan harus milik service yang dikelola freelancer ini
        abort_unless($order->service->freelancer_id === $freelancer->id, 403);

        $request->validate([
            'reason' => 'required|string'
        ]);

        $order->update([
            'status' => 'Cancelled',
            'brief' => $order->brief . "\n\nRejection Reason: " . $request->reason
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
}
