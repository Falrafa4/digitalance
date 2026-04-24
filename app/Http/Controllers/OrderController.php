<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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
        $validated = $request->validate([
            'status' => 'required|in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update($validated);

        return redirect()->route('admin.orders.index')->with('success', 'Status order berhasil diperbarui');
    }

    /**
     * Store (dipakai admin route & client route di repo kamu)
     * NOTE: method ini saat ini mengembalikan JSON (sesuai kode awalmu).
     * Kalau kamu butuh redirect ke halaman orders setelah store, bikin method lain (aku sediakan storePage di bawah).
     */
    public function store(Request $request)
    {
        $client = $request->user('client');

        $validated = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'brief' => 'required|string',
            'status' => 'nullable|in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled',
            'agreed_price' => 'nullable|decimal:2'
        ]);

        Order::create([
            ...$validated,
            'client_id' => $client->id,
            'status' => $validated['status'] ?? 'Pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Sukses melakukan order jasa'
        ]);
    }

    /**
     * CLIENT ONLY (JSON) - sesuai kode awalmu
     */
    public function clientIndex(Request $request)
    {
        $client = auth('client')->user();

        $orders = Order::with('service')
            ->where('client_id', $client->id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
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

    /**
     * CLIENT: detail order versi route lama repo kamu (clientShow)
     * Di routes/web.php kamu ada: Route::get('/orders/{id}', [OrderController::class, 'clientShow'])
     * Aku buat kompatibel: return view bukan JSON.
     */
    public function clientShow(string $id)
    {
        // agar route lama tetap jalan dan tidak error
        $order = Order::findOrFail($id);
        return $this->clientShowPage($order);
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

    public function updateStatusFreelancer(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update($validated);

        return redirect()->route('freelancer.orders.index')->with('success', 'Status order berhasil diperbarui');
    }

    public function updateAgreedPrice(Request $request, string $id)
    {
        $validated = $request->validate([
            'agreed_price' => 'required|decimal:2'
        ]);

        $order = Order::findOrFail($id);
        $order->update($validated);

        return redirect()->route('freelancer.orders.index')->with('success', 'Harga yang disepakati berhasil diperbarui');
    }
}