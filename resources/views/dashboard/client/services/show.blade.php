@extends('layouts.dashboard')
@section('title', 'Detail Jasa')

@section('content')
  <section class="animate-fadeUp">
    <div class="flex flex-col lg:flex-row gap-6">
      <div class="flex-1 space-y-6">
        <div class="bg-white border border-slate-200 rounded-[18px] p-6">
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <h1 class="font-display text-[1.65rem] font-extrabold text-slate-900 truncate">
                {{ $service->title ?? 'Layanan' }}
              </h1>
              <p class="text-slate-500 mt-1 text-[13.5px]">
                Kategori: <span class="font-bold">{{ $service->service_category->name ?? '-' }}</span>
              </p>
            </div>
            <x-ui.status-badge :status="$service->status ?? 'Approved'" />
          </div>

          <p class="text-slate-600 mt-4 leading-relaxed text-[14px]">
            {{ $service->description ?? 'Belum ada deskripsi.' }}
          </p>

          <div class="flex flex-col sm:flex-row gap-3 mt-6">
            <a href="{{ route('client.orders.create', $service->id) }}"
              class="px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all text-center">
              Order Jasa Ini <i class="ri-arrow-right-line ml-2"></i>
            </a>
            <a href="{{ route('client.services.index') }}" class="px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                      hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
              Kembali <i class="ri-arrow-left-line ml-2"></i>
            </a>
          </div>
        </div>

        @if($otherServices->count() > 0)
          <div class="bg-white border border-slate-200 rounded-[18px] p-6">
            <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Layanan Lain</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
              @foreach($otherServices as $os)
                <a href="{{ route('client.services.show', $os->id) }}"
                  class="border border-slate-200 rounded-[18px] p-5 hover:shadow-md transition-all bg-white">
                  <p class="font-extrabold text-slate-900 truncate">{{ $os->title ?? 'Service' }}</p>
                  <p class="text-slate-500 text-[13px] mt-1">{{ $os->category->name ?? '-' }}</p>
                </a>
              @endforeach
            </div>
          </div>
        @endif
      </div>

      <aside class="w-full lg:w-[360px] shrink-0 space-y-6">
        <div class="bg-white border border-slate-200 rounded-[18px] p-6">
          <h3 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Freelancer</h3>
          @php
            $freelancerName = optional(optional($service->freelancer)->skomda_student)->name ?? '-';
            $freelancerAvatarUrl = $service->freelancer->profile_photo
              ? asset('storage/' . $service->freelancer->profile_photo)
              : ($service->freelancer->skomda_student->avatar
                ? asset('storage/' . $service->freelancer->skomda_student->avatar)
                : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($freelancerName . '-' . ($service->freelancer_id ?? '0')) . '&background=0f766e&color=fff&size=48');
          @endphp
          <a href="{{ $service->freelancer_id ? route('client.talents.show', $service->freelancer_id) : '#' }}"
            class="flex items-start gap-3 hover:bg-slate-50 -mx-2 px-2 py-2 rounded-lg transition-colors">
            <img src="{{ $freelancerAvatarUrl }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-200" />
            <div class="min-w-0">
              <p class="font-extrabold text-slate-900 truncate hover:text-[#0f766e]">
                {{ $freelancerName }}
              </p>
              <p class="text-[#0f766e] text-[12px] font-bold mt-0.5">
                <i class="ri-star-fill text-amber-400 mr-1"></i> Terverifikasi
              </p>
            </div>
          </a>

          <div class="mt-4 pt-4 border-t border-slate-100">
            <p class="text-slate-500 text-[13px]">
              {{ optional($service->freelancer)->bio ?? 'Belum ada bio.' }}
            </p>
          </div>

          <div class="mt-5 pt-5 border-t border-slate-100 flex flex-col gap-3">
            @if($service->freelancer_id)
              <a href="{{ route('client.talents.show', $service->freelancer_id) }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px]
                                hover:bg-[#0a5e58] transition-all">
                Lihat Profil Lengkap <i class="ri-arrow-right-line ml-2"></i>
              </a>
            @endif
            <a href="{{ route('client.talents.index') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                        hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
              Lihat Freelancer Lain <i class="ri-user-search-line ml-2"></i>
            </a>
          </div>
        </div>
      </aside>
    </div>
  </section>
@endsection