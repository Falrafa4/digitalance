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
    
    public function index()
    {
        // Pakai eager loading skomda_student agar nama asli muncul di dashboard admin
        $offers = Offer::with(['order.service.freelancer.skomda_student'])->latest()->get();
        $negotiations = Negotiation::with(['order.client.skomda_student', 'order.service.freelancer.skomda_student'])->latest()->get();
        
        return view('dashboard.admin.offers', compact('offers', 'negotiations'));
    }

    /**
     * Tampilan Daftar Penawaran untuk Freelancer
     */
    public function freelancerIndex(Request $request)
    {
        $freelancer = auth('freelancer')->user();
        
        $offers = Offer::with('order.service.freelancer')
            ->whereHas('order.service.freelancer', function ($query) use ($freelancer) {
                $query->where('id', $freelancer->id);
            })->get();

        return view('dashboard.freelancer.offers', compact('offers'));
    }

    /**
     * Simpan Penawaran Baru dari Freelancer
     */
    public function freelancerStore(StoreOfferRequest $request)
    {
        $validatedData = $request->validated();
        $freelancer = auth('freelancer')->user();

        // Pastikan order yang ditawarkan adalah milik freelancer yang sedang login
        Order::where('id', $validatedData['order_id'])
            ->whereHas('service.freelancer', function ($query) use ($freelancer) {
                $query->where('id', $freelancer->id);
            })->firstOrFail();

        // Buat offer baru
        Offer::create(array_merge($validatedData, [
            'status' => 'Sent' // Set status default ke Sent sesuai database
        ]));

        return redirect()->route('freelancer.offers.index')->with('success', 'Penawaran berhasil dibuat');
    }

    /**
     * Update Penawaran oleh Freelancer
     */
    public function freelancerUpdate(UpdateOfferRequest $request, Offer $offer)
    {
        // Cek kepemilikan
        if ($offer->order->service->freelancer_id !== auth('freelancer')->id()) {
            abort(403, 'Akses ditolak.');
        }

        // Hanya bisa edit jika belum direspon (status Sent)
        if ($offer->status !== 'Sent') {
            return redirect()->route('freelancer.offers.index')->with('error', 'Penawaran yang sudah diproses tidak bisa diubah.');
        }

        $offer->update($request->validated());

        return redirect()->route('freelancer.offers.index')->with('success', 'Penawaran berhasil diperbarui');
    }
}