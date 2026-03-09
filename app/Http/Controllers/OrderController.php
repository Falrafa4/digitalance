<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // FOR ADMIN
    public function index()
    {
        $data = Order::with('service', 'client')->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function adminShow(Order $order)
    {
        $order->load('service', 'client');

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }

    public function update(Request $request, Order $order)
    {
        //
    }

    public function destroy(Order $order)
    {
        //
    }

    // FOR CLIENT
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

    public function clientIndex(Request $request) {
        $client = auth('client')->user();

        $orders = Order::with('service')
            ->where('client_id', $client->id)
            ->get();
        
            return response()->json([
                'status' => true,
                'data' => $orders
            ]);
    }

    public function clientShow(Order $order) {
        if ($order->client_id !== auth('client')->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $order->load('service');

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }
}
