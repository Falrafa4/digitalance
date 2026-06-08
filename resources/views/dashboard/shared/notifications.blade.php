{{--
    Halaman Riwayat Notifikasi (full-page view).
    Re-uses component x-notification-drawer dengan mode "embedded" agar
    tidak ada duplikasi UI. Tombol toggle "open drawer" tidak relevan di sini;
    panel langsung terbuka karena data dirender sebagai halaman penuh.
--}}
@extends('layouts.dashboard')

@section('title', 'Riwayat Notifikasi')

@section('content')
    <div class="max-w-3xl mx-auto">
        {{-- Heading --}}
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <h1 class="font-display text-2xl font-extrabold text-slate-900 leading-tight">
                    Riwayat Notifikasi
                </h1>
                <p class="text-[13px] text-slate-500 mt-1">
                    Semua notifikasi yang pernah kamu terima. Gunakan filter atau pencarian untuk menemukan pesan tertentu.
                </p>
            </div>
            <a href="
    @if(auth()->user() instanceof \App\Models\Administrator)
        {{ route('admin.dashboard') }}
    @elseif(auth()->user() instanceof \App\Models\Client)
        {{ route('client.dashboard') }}
    @elseif(auth()->user() instanceof \App\Models\Freelancer)
        {{ route('freelancer.dashboard') }}
    @else
        {{ route('login') }}
    @endif
"
class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-[12px] font-bold text-slate-600 hover:bg-slate-50 transition-all">
    <i class="ri-arrow-left-line mr-1"></i> Kembali
</a>
        </div>

        {{-- Stat ringkas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
            <div class="px-4 py-3 rounded-xl bg-white border border-slate-200">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Aktif</p>
                <p class="text-2xl font-display font-extrabold text-slate-900 mt-1">{{ $allCount ?? 0 }}</p>
            </div>
            <div class="px-4 py-3 rounded-xl bg-white border border-slate-200">
                <p class="text-[11px] font-bold uppercase tracking-widest text-teal-600">Belum Dibaca</p>
                <p class="text-2xl font-display font-extrabold text-teal-700 mt-1">{{ $unreadCount ?? 0 }}</p>
            </div>
            <div class="px-4 py-3 rounded-xl bg-white border border-slate-200">
                <p class="text-[11px] font-bold uppercase tracking-widest text-amber-600">Disimpan</p>
                <p class="text-2xl font-display font-extrabold text-amber-700 mt-1">{{ $keptCount ?? 0 }}</p>
            </div>
            <div class="px-4 py-3 rounded-xl bg-white border border-slate-200">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-600">Arsip</p>
                <p class="text-2xl font-display font-extrabold text-slate-800 mt-1">{{ $archivedCount ?? 0 }}</p>
            </div>
        </div>

        {{-- Reuse drawer component dengan data yang sudah dihitung controller.
             Karena data sudah dikirim dengan variabel yang kompatibel
             (notifNotifications, notifUnreadCount, filter, q, category, dll),
             komponen langsung bisa dirender. --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            @php
                $notifNotifications = $dbNotifications;
                $notifUnreadCount = $unreadCount;
            @endphp
            <x-notification-drawer />
        </div>

        {{-- Paginasi --}}
        @if ($dbNotifications instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="mt-5">
                {{ $dbNotifications->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    {{-- Buka drawer secara otomatis (mode embedded) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof openNotificationDrawer === 'function') {
                openNotificationDrawer();
            }
        });
    </script>
@endpush
