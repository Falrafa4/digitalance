<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // ADMIN ONLY
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
            'client_id' => $client->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Sukses melakukan order jasa'
        ]);
    }

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

    // FREELANCER ONLY
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
