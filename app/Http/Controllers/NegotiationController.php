<?php

namespace App\Http\Controllers;

use App\Events\NegotiationSent;
use App\Http\Requests\SendMessageRequest;
use App\Models\Negotiation;
use App\Models\Order;
use Illuminate\Http\Request;

class NegotiationController extends Controller
{
    // =========================
    // ADMIN ONLY
    // =========================
    public function index()
    {
        $negotiations = Negotiation::with('order.service.freelancer')->get();
        return view('dashboard.admin.negotiations', compact('negotiations'));
    }

    // =========================
    // FREELANCER ONLY
    // =========================
    public function freelancerGetMessages()
    {
        $freelancer = auth('freelancer')->user();

        $negotiations = Negotiation::with('order.service.freelancer')
            ->whereHas('order.service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })->get();

        return view('dashboard.freelancer.messages', compact('negotiations'));
    }

    public function freelancerSendMessage(SendMessageRequest $request)
    {
        $freelancer = auth('freelancer')->user();
        $order = Order::with('service')->find($request->order_id);

        if (!$order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan.');
        }

        if ($freelancer->id !== $order->service->freelancer_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengirim pesan di negosiasi ini.');
        }

        $request->validated();

        $negotiation = Negotiation::create([
            'order_id' => $request->order_id,
            'sender' => 'freelancer',
            'message' => $request->message
        ]);

        broadcast(new NegotiationSent($negotiation))->toOthers();

        return redirect()->back()->with('success', 'Pesan berhasil dikirim');
    }

    // =========================
    // CLIENT ONLY (MVP)
    // =========================

    /**
     * CLIENT: Messages inbox (sidebar Messages)
     * Menampilkan list negotiation berdasarkan order milik client
     */
    public function clientIndex()
    {
        $client = auth('client')->user();

        $threads = Negotiation::with('order.service.freelancer.skomda_student')
            ->whereHas('order', fn($q) => $q->where('client_id', $client->id))
            ->latest()
            ->get()
            ->groupBy('order_id')
            ->map(fn($messages) => $messages->first())
            ->values();

        return view('dashboard.client.messages', compact('threads'));
    }

    public function clientSendMessage(Request $request)
    {
        $client = auth('client')->user();

        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'message' => 'required|string|max:2000',
        ]);

        $order = Order::where('client_id', $client->id)->findOrFail($validated['order_id']);

        $negotiation = Negotiation::create([
            'order_id' => $order->id,
            'sender' => 'client',
            'message' => $validated['message'],
        ]);

        broadcast(new NegotiationSent($negotiation))->toOthers();

        if ($order->status === 'Pending') {
            $order->status = 'Negotiated';
            $order->save();
        }

        return back()->with('success', 'Pesan terkirim');
    }
}
