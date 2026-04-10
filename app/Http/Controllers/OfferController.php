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
    // Mengambil data dengan Eager Loading agar tidak berat
    $offers = Offer::with(['order.service.freelancer', 'order.client'])->latest()->get();
    $negotiations = Negotiation::with(['order.client', 'order.service.freelancer'])->latest()->get();

    return view('dashboard.admin.offers', [
        'offers' => $offers,
        'negotiations' => $negotiations
    ]);
}

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