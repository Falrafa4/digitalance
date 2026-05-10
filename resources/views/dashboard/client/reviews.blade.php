@extends('layouts.dashboard')
@section('title', 'Reviews | Digitalance')

@section('content')
<section class="animate-fadeUp">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
    <div>
      <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Reviews Saya</h1>
      <p class="text-slate-500 mt-1">Riwayat review yang pernah Anda berikan.</p>
    </div>
  </div>

  @if($reviews->isEmpty())
    @include('dashboard.client._ui.empty', [
      'icon' => 'ri-star-line',
      'title' => 'Belum ada review',
      'desc' => 'Review akan muncul di sini setelah Anda memberikan rating pada order yang selesai.',
      'actionUrl' => route('client.orders.index'),
      'actionLabel' => 'Lihat Orders'
    ])
  @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      @foreach($reviews as $review)
        <div class="bg-white border border-slate-200 rounded-[18px] p-5 hover:shadow-md transition-all">
          <div class="flex items-start justify-between gap-4 mb-4 pb-4 border-b border-slate-100">
            <div>
              <p class="font-extrabold text-slate-900 text-[15px]">{{ $review->order->service->title ?? 'Service' }}</p>
              <p class="text-slate-500 text-[12px] mt-1">Order #{{ $review->order_id }}</p>
            </div>
            <form action="{{ route('client.reviews.destroy', $review->order_id) }}" method="POST" onsubmit="return confirm('Hapus review ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-[11px] font-bold hover:bg-red-100 transition-all">
                <i class="ri-delete-bin-line mr-1"></i> Hapus
              </button>
            </form>
          </div>

          <div class="flex gap-1 mb-3">
            @for($i = 1; $i <= 5; $i++)
              <i class="ri-star-fill text-{{ $i <= $review->rating ? 'amber-400' : 'slate-200' }} text-[18px]"></i>
            @endfor
          </div>

          <p class="text-slate-600 text-[13.5px] leading-relaxed line-clamp-3">
            {{ $review->comment ?? 'Tanpa komentar.' }}
          </p>

          <p class="text-slate-400 text-[11px] font-bold mt-4">
            {{ $review->created_at->format('d M Y') }}
          </p>
        </div>
      @endforeach
    </div>

    @if($reviews->hasPages())
    <div class="mt-8 flex justify-center">
      {{ $reviews->links() }}
    </div>
    @endif
  @endif
</section>
@endsection