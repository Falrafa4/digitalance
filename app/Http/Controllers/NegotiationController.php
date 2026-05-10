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
    public function clientInbox()
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

    // =========================
    // CLIENT: Store Negotiation (New Price Proposal)
    // =========================
    public function clientStoreNegotiation(Request $request, Offer $offer)
    {
        $client = auth('client')->user();

        if ($offer->order->client_id !== $client->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($offer->status !== 'Sent') {
            return redirect()->back()->with('error', 'Penawaran yang sudah diproses tidak bisa dinegosiasikan.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'new_price' => 'required|integer|min:1000',
            'description' => 'nullable|string|max:2000',
        ]);

        $message = "Negosiasi harga: " . $validated['reason'] . 
                   "\nHarga tawaran: Rp " . number_format($validated['new_price'], 0, ',', '.') . 
                   "\nDeskripsi: " . ($validated['description'] ?? '-');

        $negotiation = Negotiation::create([
            'order_id' => $offer->order_id,
            'sender' => 'client',
            'message' => $message,
        ]);

        $offer->order->update(['status' => 'Negotiated']);

        return redirect()->route('client.offers.show', $offer->id)->with('success', 'Negosiasi berhasil dikirim');
    }

    // =========================
    // FREELANCER: Accept Negotiation
    // =========================
    public function freelancerAcceptNegotiation(Negotiation $negotiation)
    {
        $freelancer = auth('freelancer')->user();

        if ($negotiation->order->service->freelancer_id !== $freelancer->id) {
            abort(403, 'Akses ditolak.');
        }

        $negotiation->update([
            'message' => $negotiation->message . "\n\n[SISTEM: Negosiasi harga diterima oleh Freelancer]",
        ]);

        return redirect()->back()->with('success', 'Negosiasi diterima.');
    }

    // =========================
    // FREELANCER: Reject Negotiation
    // =========================
    public function freelancerRejectNegotiation(Negotiation $negotiation)
    {
        $freelancer = auth('freelancer')->user();

        if ($negotiation->order->service->freelancer_id !== $freelancer->id) {
            abort(403, 'Akses ditolak.');
        }

        $negotiation->update([
            'message' => $negotiation->message . "\n\n[SISTEM: Negosiasi harga ditolak oleh Freelancer]",
        ]);

        return redirect()->back()->with('success', 'Negosiasi ditolak.');
    }

    // =========================
    // FREELANCER: Show Negotiation Detail
    // =========================
    public function freelancerShowNegotiation(Negotiation $negotiation)
    {
        $freelancer = auth('freelancer')->user();

        if ($negotiation->order->service->freelancer_id !== $freelancer->id) {
            abort(403, 'Akses ditolak.');
        }

        $negotiation->load(['order.service', 'order.client', 'order.offers']);

        return view('dashboard.freelancer.negotiation-view', compact('negotiation'));
    }
}
