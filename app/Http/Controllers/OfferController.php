<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $offers = Offer::with('order.service.freelancer')->get();
        return view('dashboard.admin.offers', compact('offers'));
    }

    // FREELANCER ONLY
    // TODO: selesaikan fitur offer untuk freelancer (all), termasuk validasi dan return view yang sesuai
    public function freelancerIndex(Request $request)
    {
        $freelancer = auth('freelancer')->user();
        $offers = Offer::with('order.service.freelancer')->whereHas('order.service.freelancer', function ($query) use ($freelancer) {
            $query->where('id', $freelancer->id);
        })->get();

        return view('dashboard.freelancer.offers', compact('offers'));
    }

    public function freelancerStore(StoreOfferRequest $request)
    {
        // Validasi input
        $validatedData = $request->validated();

        $freelancer = auth('freelancer')->user();

        // Pastikan order yang ditawarkan adalah milik freelancer yang sedang login
        $order = Order::where('id', $validatedData['order_id'])
            ->whereHas('service.freelancer', function ($query) use ($freelancer) {
                $query->where('id', $freelancer->id);
            })->firstOrFail();

        // Buat offer baru
        Offer::create([
            ...$validatedData,
            'status' => 'Sent' // Set status default ke Sent
        ]);

        return redirect()->route('freelancer.offers.index')->with('success', 'Penawaran berhasil dibuat');
    }

    public function freelancerUpdate(UpdateOfferRequest $request, Offer $offer)
    {
        if ($offer->order->service->freelancer_id !== auth('freelancer')->id()) {
            abort(403, 'Unauthorized');
        }

        if ($offer->status !== 'Sent') {
            return redirect()->route('freelancer.offers.index')->with('error', 'Hanya penawaran dengan status sent yang bisa diubah');
        }

        $offer->update($request->validated());

        return redirect()->route('freelancer.offers.index')->with('success', 'Penawaran berhasil diperbarui');
    }

    // CLIENT ONLY
    // TODO: selesaikan fitur offer untuk client (all), termasuk validasi dan return view yang sesuai
}
