@extends('layouts.dashboard')
@section('title', 'Hasil Pekerjaan | Digitalance')

@section('content')
  <div class="content-scroll flex-1 px-8 py-7 overflow-y-auto relative overflow-hidden">
    {{-- Decorative Background Icon --}}
    <div class="absolute -right-12 -top-12 w-64 h-64 opacity-[0.03] pointer-events-none rotate-12">
      <i class="ri-task-line text-[250px]"></i>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8 animate-fadeUp relative z-10">
      <div>
        <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Hasil Pekerjaan</h1>
        <p class="text-slate-500 text-[0.95rem] mt-1">Pantau hasil pekerjaan yang dikirim freelancer.</p>
      </div>
    </div>

    @if($results->isEmpty())
      <x-ui.empty-state icon="ri-file-check-line" title="Belum ada hasil"
        description="Hasil pekerjaan akan muncul di sini setelah freelancer mengirim hasil." />
    @else
      <div class="space-y-4">
        @foreach($results as $result)
          @php
            $status = $result->order->status ?? '-';
            $statusClass = match ($status) {
              'In Progress' => 'bg-blue-100 text-blue-700',
              'Revision' => 'bg-amber-100 text-amber-700',
              'Completed' => 'bg-emerald-100 text-emerald-700',
              default => 'bg-slate-100 text-slate-600'
            };
          @endphp
          <div class="bg-white border border-slate-200 rounded-[18px] p-5 hover:shadow-md transition-all">
            <div class="flex items-start justify-between gap-4 mb-4">
              <div class="min-w-0">
                <p class="font-extrabold text-slate-900 truncate">{{ $result->version ?? 'Versi tidak diketahui' }}</p>
                <p class="text-slate-500 text-[13px] mt-1">
                  Order #{{ $result->order_id }} — {{ $result->order->service->title ?? 'Layanan' }}
                </p>
                <p class="text-slate-400 text-[12px] font-bold mt-1">
                  oleh {{ $result->order->service->freelancer->skomda_student->name ?? 'Freelancer' }}
                </p>
                <p class="text-slate-400 text-[12px] font-bold mt-0.5">
                  {{ $result->created_at->timezone(config('app.timezone'))->format('d M Y, H:i') }} WIB
                </p>
              </div>
              <div class="flex items-center gap-2 flex-shrink-0">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase {{ $statusClass }}">{{ $status }}</span>
              </div>
            </div>

            @if($result->note)
              <div class="bg-slate-50 rounded-xl p-4 mb-4">
                <p class="text-[13px] text-slate-600 leading-relaxed">{{ $result->note }}</p>
              </div>
            @endif

            <div class="flex gap-2 mt-4 pt-4 border-t border-slate-100">
              <a href="{{ route('client.results.show', $result->id) }}"
                class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-800 text-white font-bold text-[12.5px] hover:bg-black transition-all text-center flex items-center justify-center gap-2">
                <i class="ri-eye-line"></i> Lihat Detail
              </a>
              @if($result->file_url)
                <a href="{{ asset('storage/' . $result->file_url) }}" target="_blank"
                  class="flex-1 px-4 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[12.5px] hover:bg-[#0a5e58] transition-all text-center flex items-center justify-center gap-2">
                  <i class="ri-download-line"></i> Unduh File
                </a>
              @endif
              <a href="{{ route('client.orders.show', $result->order_id) }}" class="flex-1 px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px]
                            hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
                Lihat Order
              </a>
            </div>
          </div>
        @endforeach
      </div>
    @endif
    </div>
@endsection