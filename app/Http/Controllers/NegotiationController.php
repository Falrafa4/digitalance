<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Models\Negotiation;
use App\Models\Order;
use Illuminate\Http\Request;

class NegotiationController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $negotiations = Negotiation::with('order.service.freelancer')->get();
        return view('dashboard.admin.negotiations', compact('negotiations'));
    }

    // FREELANCER ONLY
    // TODO: selesaikan fitur negosiasi untuk freelancer (all), termasuk validasi dan return view yang sesuai
    public function freelancerGetMessages()
    {
        $freelancer = auth('freelancer')->user();
        $negotiations = Negotiation::with('order.service.freelancer')
            ->whereHas('order.service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })->get();

        return view('dashboard.freelancer.negotiations', compact('negotiations'));
    }
    
    public function freelancerSendMessage(SendMessageRequest $request)
    {
        $freelancer = auth('freelancer')->user();
        $order = Order::with('service')->find($request->order_id);
        
        if ($freelancer->id !== $order->service->freelancer_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengirim pesan di negosiasi ini.');
        }
        
        $request->validated();

        Negotiation::create([
            'order_id' => $request->order_id,
            'sender' => 'freelancer',
            'message' => $request->message
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil dikirim');
    }
}
