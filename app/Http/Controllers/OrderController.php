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

    // CLIENT ONLY
    // TODO: selesaikan fitur order untuk client (all), termasuk validasi dan return view yang sesuai
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
    // TODO: selesaikan fitur order untuk freelancer (all), termasuk validasi dan return view yang sesuai
}
