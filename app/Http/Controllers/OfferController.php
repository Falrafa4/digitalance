<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Negotiation;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    // ADMIN
    public function index()
    {
        // Mengambil data dengan Eager Loading agar tidak berat
        $offers = Offer::with(['order.service.freelancer', 'order.client'])->latest()->get();
        $negotiations = Negotiation::with(['order.client', 'order.service.freelancer'])->latest()->get();

        return view('dashboard.admin.offers', [
            'offers' => $offers,
            'negotiations' => $negotiations
        ]);
    }

    // CLIENT
    public function clientIndex()
    {
        $client = auth('client')->user();
        $offers = Offer::with('order.service.freelancer')
            ->whereHas('order.client', function ($query) use ($client) {
                $query->where('id', $client->id);
            })->get();
        return view('dashboard.client.offers', compact('offers'));
    }

    public function clientShow(Offer $offer)
    {
        if ($offer->order->client_id !== auth('client')->id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('dashboard.client.offer-detail', compact('offer'));
    }

    public function clientAccept(Offer $offer)
    {
        if ($offer->order->client_id !== auth('client')->id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($offer->status !== 'Sent') {
            return redirect()->route('client.offers.index')->with('error', 'Penawaran yang sudah diproses tidak bisa disetujui.');
        }

        $offer->update(['status' => 'Accepted']);
        return redirect()->route('client.offers.index')->with('success', 'Penawaran berhasil disetujui');
    }

    public function clientReject(Offer $offer)
    {
        if ($offer->order->client_id !== auth('client')->id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($offer->status !== 'Sent') {
            return redirect()->route('client.offers.index')->with('error', 'Penawaran yang sudah diproses tidak bisa ditolak.');
        }

        $offer->update(['status' => 'Rejected']);
        return redirect()->route('client.offers.index')->with('success', 'Penawaran berhasil ditolak');
    }

    // FREELANCER
    public function freelancerIndex(Request $request)
    {
        $freelancer = auth('freelancer')->user();
        $offers = Offer::with('order.service.freelancer')
            ->whereHas('order.service.freelancer', function ($query) use ($freelancer) {
                $query->where('id', $freelancer->id);
            })->get();
        return view('dashboard.freelancer.offers', compact('offers'));
    }

    public function freelancerStore(StoreOfferRequest $request)
    {
        $validatedData = $request->validated();
        $freelancer = auth('freelancer')->user();

        Order::where('id', $validatedData['order_id'])
            ->whereHas('service.freelancer', function ($query) use ($freelancer) {
                $query->where('id', $freelancer->id);
            })->firstOrFail();

        Offer::create(array_merge($validatedData, [
            'status' => 'Sent'
        ]));

        return redirect()->route('freelancer.offers.index')->with('success', 'Penawaran berhasil dibuat');
    }

    public function freelancerUpdate(UpdateOfferRequest $request, Offer $offer)
    {
        if ($offer->order->service->freelancer_id !== auth('freelancer')->id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($offer->status !== 'Sent') {
            return redirect()->route('freelancer.offers.index')->with('error', 'Penawaran yang sudah diproses tidak bisa diubah.');
        }

        $offer->update($request->validated());
        return redirect()->route('freelancer.offers.index')->with('success', 'Penawaran berhasil diperbarui');
    }
}
