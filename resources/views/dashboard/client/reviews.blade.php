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
            <button onclick="window.openDeleteReview({{ $review->order_id }})" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-[11px] font-bold hover:bg-red-100 transition-all">
              <i class="ri-delete-bin-line mr-1"></i> Hapus
            </button>
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

<!-- Delete Confirmation Modal -->
<div class="fixed inset-0 z-[200] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-delete-review-client">
    <div class="bg-white rounded-[24px] w-full max-w-[400px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300">
        <div class="px-8 pt-8 pb-6 text-center">
            <div class="w-[72px] h-[72px] mx-auto mb-5 bg-red-50 rounded-full flex items-center justify-center text-[2rem] text-red-500">
                <i class="ri-error-warning-fill"></i>
            </div>
            <h3 class="text-[1.3rem] font-black text-slate-900 mb-2">Hapus Review?</h3>
            <p class="text-[13.5px] text-slate-500 leading-relaxed">Review ini akan dihapus permanen dan tidak dapat dikembalikan.</p>
        </div>
        <div class="flex gap-3 px-8 pb-8">
            <button onclick="window.closeDeleteReviewClient()" class="flex-1 py-3.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-[13px] hover:bg-slate-200 transition-all">Batal</button>
            <button id="btn-confirm-delete-review-client" class="flex-1 py-3.5 rounded-xl bg-red-500 text-white font-bold text-[13px] hover:bg-red-600 transition-all shadow-lg shadow-red-200">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
    window.openDeleteReview = function(id) {
        const overlay = document.getElementById('modal-delete-review-client');
        overlay.classList.remove('opacity-0', 'pointer-events-none');

        const btn = document.getElementById('btn-confirm-delete-review-client');
        btn.onclick = function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/client/reviews/' + id;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        };
    };

    window.closeDeleteReviewClient = function() {
        const overlay = document.getElementById('modal-delete-review-client');
        overlay.classList.add('opacity-0', 'pointer-events-none');
    };
</script>
@endsection