@extends('layouts.dashboard')
@section('title', 'Detail Order')

@section('content')
<div class="animate-fadeUp grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="xl:col-span-2 space-y-6">
    <div class="bg-white border border-slate-200 rounded-[18px] p-6">
      <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900">Order #{{ $order->id }}</h1>
      <p class="text-slate-500 mt-1">Jasa: <span class="font-bold">{{ $order->service->title ?? '-' }}</span></p>

      <div class="flex flex-wrap gap-2 mt-4">
        <span class="px-3 py-1 rounded-full text-[12px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
          {{ $order->status ?? '-' }}
        </span>
        <span class="px-3 py-1 rounded-full text-[12px] font-bold bg-white text-slate-600 border border-slate-200">
          Agreed: Rp {{ number_format((float)($order->agreed_price ?? 0), 0, ',', '.') }}
        </span>
      </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-[18px] p-6">
      <h2 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Brief</h2>
      <p class="text-slate-600 mt-3 whitespace-pre-line">{{ $order->brief ?? '-' }}</p>

      <form method="POST" action="{{ route('client.orders.attachments.store', $order->id) }}" enctype="multipart/form-data"
            class="mt-5 flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="file" name="file" class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200"/>
        <button class="px-5 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
          Upload Attachment
        </button>
      </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-[18px] p-6">
      <h2 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Messages / Negosiasi</h2>

      <div class="mt-4 space-y-3">
        @forelse(($order->negotiations ?? []) as $n)
          <div class="border border-slate-200 rounded-[14px] p-4 {{ ($n->sender ?? '') === 'client' ? 'bg-teal-50' : 'bg-slate-50' }}">
            <p class="font-extrabold text-slate-900 text-[13px]">{{ ($n->sender ?? '') === 'client' ? 'Kamu' : 'Freelancer' }}</p>
            <p class="text-slate-600 mt-1">{{ $n->message ?? '-' }}</p>
          </div>
        @empty
          <div class="border-2 border-dashed border-slate-200 rounded-[14px] p-6 text-center">
            <p class="font-extrabold text-slate-900">Belum ada pesan</p>
            <p class="text-slate-500 mt-1">Kirim pesan pertama di bawah.</p>
          </div>
        @endforelse
      </div>

      <form method="POST" action="{{ route('client.messages.send') }}" class="mt-4 flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}"/>
        <input name="message" class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200" placeholder="Tulis pesan...">
        <button class="px-5 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">Kirim</button>
      </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-[18px] p-6">
      <h2 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Review</h2>

      @if(!empty($order->review))
        <p class="text-slate-600 mt-3">Kamu sudah memberi review.</p>
      @else
        <form method="POST" action="{{ route('client.reviews.store') }}" class="mt-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
          @csrf
          <input type="hidden" name="order_id" value="{{ $order->id }}"/>
          <select name="rating" class="px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200">
            <option value="">Rating</option>
            @for($i=5;$i>=1;$i--) <option value="{{ $i }}">{{ $i }}</option> @endfor
          </select>
          <input name="comment" class="sm:col-span-3 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200" placeholder="Komentar...">
          <div class="sm:col-span-4">
            <button class="px-5 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">Kirim Review</button>
          </div>
        </form>
      @endif
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-white border border-slate-200 rounded-[18px] p-6">
      <h3 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Freelancer</h3>
      <p class="text-slate-500 mt-2">
        {{ optional(optional(optional($order->service)->freelancer)->skomda_student)->name ?? 'Freelancer' }}
      </p>
      <a href="{{ route('client.services.show', $order->service_id) }}"
         class="mt-4 inline-flex w-full justify-center px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
        Lihat Jasa
      </a>
    </div>
  </div>
</div>
@endsection