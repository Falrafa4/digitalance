<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Models\Negotiation;
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
    
    public function freelancerSendMessage(SendMessageRequest $request, $id)
    {
        $freelancer = auth('freelancer')->user();
        $request->validated();

        $negotiation = Negotiation::findOrFail($id);
        $negotiation->messages()->create([
            'sender_type' => 'freelancer',
            'sender_id' => $freelancer->id,
            'message' => $request->message
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil dikirim');
    }
}
