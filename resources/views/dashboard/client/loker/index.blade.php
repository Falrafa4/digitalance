@extends('layouts.dashboard')
@section('title', 'Lowongan Kerja | Digitalance')

@section('content')
<div class="content-scroll flex-1 px-8 py-7 overflow-y-auto relative overflow-hidden">
    {{-- Decorative Background Icon --}}
    <div class="absolute -right-12 -top-12 w-64 h-64 opacity-[0.03] pointer-events-none rotate-12">
        <i class="ri-briefcase-2-line text-[250px]"></i>
    </div>

    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp relative z-10">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Lowongan Kerja</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Posting lowongan dan kelola lamaran freelancer.</p>
        </div>
        <a href="{{ route('client.loker.create') }}"
            class="px-5 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all shadow-teal-sm flex items-center gap-2">
            <i class="ri-add-line"></i> Posting Lowongan
        </a>
    </div>

    @if($lokkers->isEmpty())
        <div class="text-center py-20 px-5 bg-white border-2 border-dashed border-slate-200 rounded-[20px] animate-fadeUp relative z-10">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-4">
                <i class="ri-briefcase-2-line"></i>
            </div>
            <h3 class="font-display text-[1.15rem] font-bold text-slate-700 mb-1">Belum Ada Lowongan</h3>
            <p class="text-[13px] text-slate-400 mb-5">Posting lowongan kerja untuk menemukan freelancer terbaik.</p>
            <a href="{{ route('client.loker.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all">
                <i class="ri-add-line"></i> Posting Sekarang
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 pb-8">
            @foreach($lokkers as $loker)
                @php
                    $statusClass = $loker->status === 'Open'
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-slate-100 text-slate-600';
                    $appCount = $loker->applications->count();
                    $pendingCount = $loker->applications->where('status', 'Pending')->count();
                @endphp
                <div class="bg-white border border-slate-200 rounded-[18px] p-6 hover:shadow-teal-sm transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="ri-briefcase-2-line text-[50px] text-slate-100/50 -rotate-12 translate-x-4 -translate-y-4"></i>
                    </div>
                    <div class="flex items-start justify-between gap-3 mb-4 relative z-10">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $statusClass }}">
                                    {{ $loker->status }}
                                </span>
                                @if($loker->category)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 uppercase tracking-wider">
                                        {{ $loker->category->name }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-extrabold text-[15px] text-slate-900 leading-tight">{{ $loker->title }}</h3>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button type="button" onclick="openApplicantsPreviewModal({{ $loker->id }})"
                                class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 hover:bg-indigo-100 transition-all relative" title="Lihat Pelamar">
                                <i class="ri-user-follow-line text-[14px]"></i>
                                @if($appCount > 0)
                                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-indigo-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center">{{ $appCount > 9 ? '9+' : $appCount }}</span>
                                @endif
                            </button>
                            <button type="button" onclick="openDetailModal({{ $loker->id }})"
                                class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                                <i class="ri-eye-line text-[14px]"></i>
                            </button>
                            <a href="{{ route('client.loker.edit', $loker->id) }}"
                                class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-all">
                                <i class="ri-pencil-line text-[14px]"></i>
                            </a>
                            <form action="{{ route('client.loker.destroy', $loker->id) }}" method="POST"
                                onsubmit="event.preventDefault(); customConfirm('Hapus lowongan ini?').then(res => { if(res) this.submit(); });">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-400 hover:bg-red-100 hover:text-red-600 transition-all">
                                    <i class="ri-delete-bin-line text-[14px]"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="text-[13px] text-slate-500 leading-relaxed line-clamp-2 mb-4">{{ $loker->description }}</p>

                    @if($loker->budget_min || $loker->budget_max)
                        <p class="text-[12px] font-bold text-[#0f766e] mb-3">
                            Budget: @if($loker->budget_min && $loker->budget_max)
                                Rp{{ number_format((float)$loker->budget_min, 0, ',', '.') }} - Rp{{ number_format((float)$loker->budget_max, 0, ',', '.') }}
                            @elseif($loker->budget_min)
                                Min Rp{{ number_format((float)$loker->budget_min, 0, ',', '.') }}
                            @else
                                Maks Rp{{ number_format((float)$loker->budget_max, 0, ',', '.') }}
                            @endif
                        </p>
                    @endif

                    @if($appCount > 0)
                        <div class="bg-slate-50 rounded-xl p-4 mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                    Lamaran Masuk
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-indigo-600">{{ $appCount }} freelancer</span>
                                    @if($appCount > 3)
                                        <button type="button" onclick="openApplicantsModal({{ $loker->id }})"
                                            class="text-[10px] font-bold text-[#0f766e] hover:underline">
                                            Lihat Semua
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @foreach($loker->applications->take(3) as $app)
                                <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-slate-200 flex items-center justify-center text-slate-500">
                                            <i class="ri-user-line text-[14px]"></i>
                                        </div>
                                        <div>
                                            <p class="text-[12px] font-bold text-slate-800">{{ $app->freelancer->skomda_student->name ?? 'Freelancer' }}</p>
                                            @if($app->proposed_price)
                                                <p class="text-[10px] text-slate-400">Rp{{ number_format((float) $app->proposed_price, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        @if($app->status === 'Pending')
                                            <form action="{{ route('client.loker.applications.approve', $app->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-[10px] hover:bg-emerald-100 transition-all">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('client.loker.applications.reject', $app->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg bg-red-50 text-red-500 font-bold text-[10px] hover:bg-red-100 transition-all">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md {{ $app->status === 'Approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $app->status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @if($appCount > 3)
                                <p class="text-[11px] text-center text-slate-400 mt-2 font-semibold">+{{ $appCount - 3 }} lainnya</p>
                            @endif
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-xl p-4 mb-4 text-center">
                            <p class="text-[12px] text-slate-400 font-semibold">Belum ada lamaran masuk</p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 relative z-10">
                        <div class="flex items-center gap-1 text-[11px] text-slate-400 font-semibold">
                            <i class="ri-time-line"></i>
                            {{ $loker->created_at->diffForHumans() }}
                        </div>
                        @if($loker->deadline)
                            <span class="text-[11px] text-slate-400 font-semibold">
                                Batas: {{ \Carbon\Carbon::parse($loker->deadline)->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('modals')
<div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-detail-overlay">
    <div class="bg-white rounded-[24px] w-full max-w-[560px] max-h-[85vh] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-detail-box">
    </div>
</div>
<div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-applicants-preview-overlay">
    <div class="bg-white rounded-[24px] w-full max-w-[640px] max-h-[85vh] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-applicants-preview-box">
    </div>
</div>
<div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-applicants-overlay">
    <div class="bg-white rounded-[24px] w-full max-w-[600px] max-h-[80vh] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-applicants-box">
    </div>
</div>
@endsection

@php
$lokkersData = $lokkers->map(function($l) {
    return [
        'id' => $l->id,
        'title' => $l->title,
        'description' => $l->description,
        'status' => $l->status,
        'category' => $l->category ? $l->category->name : null,
        'budget_min' => $l->budget_min,
        'budget_max' => $l->budget_max,
        'deadline' => $l->deadline ? \Carbon\Carbon::parse($l->deadline)->format('d M Y') : null,
        'created_at' => $l->created_at->diffForHumans(),
        'applications' => $l->applications->map(function($a) {
            return [
                'id' => $a->id,
                'freelancer_name' => $a->freelancer->skomda_student->name ?? 'Freelancer',
                'proposal' => $a->proposal,
                'proposed_price' => $a->proposed_price,
                'status' => $a->status,
                'created_at' => $a->created_at->toDateTimeString(),
            ];
        })->values()->all(),
    ];
})->values()->all();
@endphp

@section('scripts')
<script>
window.__LOKKERS_DATA__ = @json($lokkersData);

function formatRupiah(val) {
    if (!val) return '';
    return window.DigitalanceUtils.formatRupiah(val);
}

function openDetailModal(id) {
    const loker = window.__LOKKERS_DATA__.find(l => l.id === id);
    if (!loker) return;

    let budgetText = '';
    if (loker.budget_min && loker.budget_max) {
        budgetText = formatRupiah(loker.budget_min) + ' - ' + formatRupiah(loker.budget_max);
    } else if (loker.budget_min) {
        budgetText = 'Min ' + formatRupiah(loker.budget_min);
    } else if (loker.budget_max) {
        budgetText = 'Maks ' + formatRupiah(loker.budget_max);
    }

    const appCount = loker.applications.length;
    const pendingCount = loker.applications.filter(a => a.status === 'Pending').length;

    let applicantsHtml = '';
    if (appCount > 0) {
        applicantsHtml = `
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Lamaran Masuk</p>
                    <span class="text-[11px] font-bold text-indigo-600">${appCount} freelancer</span>
                </div>
                <div class="space-y-2">
                    ${loker.applications.slice(0, 3).map(app => `
                        <div class="flex items-center justify-between py-2 px-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-[#0f766e]/10 flex items-center justify-center text-[#0f766e] font-bold text-xs">
                                    ${app.freelancer_name.charAt(0)}
                                </div>
                                <div>
                                    <p class="text-[12px] font-bold text-slate-800">${app.freelancer_name}</p>
                                    ${app.proposed_price ? `<p class="text-[10px] text-slate-400">${formatRupiah(app.proposed_price)}</p>` : ''}
                                </div>
                            </div>
                            ${app.status === 'Pending' ? `
                                <div class="flex items-center gap-1">
                                    <form action="/client/loker/applications/${app.id}/approve" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-100 transition-all">
                                            <i class="ri-check-line text-[11px]"></i>
                                        </button>
                                    </form>
                                    <form action="/client/loker/applications/${app.id}/reject" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-all">
                                            <i class="ri-close-line text-[11px]"></i>
                                        </button>
                                    </form>
                                </div>
                            ` : `<span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md ${app.status === 'Approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'}">${app.status}</span>`}
                        </div>
                    `).join('')}
                </div>
                ${appCount > 3 ? `<button type="button" onclick="closeDetailModal(); openApplicantsModal(${id});" class="w-full mt-2 text-[11px] font-bold text-[#0f766e] hover:underline">+${appCount - 3} lainnya, lihat semua</button>` : ''}
            </div>`;
    } else {
        applicantsHtml = `
            <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                <p class="text-[12px] text-slate-400 font-semibold">Belum ada lamaran masuk</p>
            </div>`;
    }

    const box = document.getElementById('modal-detail-box');
    box.innerHTML = `
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider ${loker.status === 'Open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'}">${loker.status}</span>
                ${loker.category ? `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 uppercase tracking-wider">${loker.category}</span>` : ''}
            </div>
            <button onclick="closeDetailModal()" class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(85vh-200px)]">
            <h2 class="text-[1.15rem] font-black text-slate-900 leading-tight mb-3">${loker.title}</h2>
            <div class="flex flex-wrap gap-4 mb-4 text-[13px] text-slate-600">
                <span class="flex items-center gap-1.5 font-bold"><i class="ri-money-rupee-circle-line text-[#0f766e]"></i> ${budgetText || '-'}</span>
                ${loker.deadline ? `<span class="flex items-center gap-1.5 font-semibold"><i class="ri-calendar-line text-slate-400"></i> Batas: ${loker.deadline}</span>` : ''}
                <span class="flex items-center gap-1.5 font-semibold"><i class="ri-time-line text-slate-400"></i> ${loker.created_at}</span>
            </div>
            <div class="border-t border-slate-100 pt-4">
                <h3 class="text-[11px] font-extrabold text-slate-500 uppercase tracking-[.1em] mb-2">Deskripsi</h3>
                <p class="text-[13.5px] text-slate-700 leading-relaxed whitespace-pre-line">${loker.description}</p>
            </div>
            ${applicantsHtml}
        </div>
    `;

    document.getElementById('modal-detail-overlay').classList.remove('opacity-0', 'pointer-events-none');
}

function closeDetailModal() {
    document.getElementById('modal-detail-overlay').classList.add('opacity-0', 'pointer-events-none');
}

document.addEventListener('click', function(e) {
    if (e.target.id === 'modal-detail-overlay') closeDetailModal();
});

window.openApplicantsPreviewModal = function(lokerId) {
    const loker = window.__LOKKERS_DATA__.find(l => l.id === lokerId);
    if (!loker) return;

    const apps = loker.applications;
    const appCount = apps.length;
    const pendingApps = apps.filter(a => a.status === 'Pending');
    const approvedApps = apps.filter(a => a.status === 'Approved');
    const rejectedApps = apps.filter(a => a.status === 'Rejected');

    let appsHtml = '';
    if (appCount > 0) {
        appsHtml = apps.map(app => `
            <div class="bg-white border border-slate-100 rounded-xl p-4 hover:shadow-md transition-all">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#0f766e] to-emerald-400 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                            ${app.freelancer_name.charAt(0)}
                        </div>
                        <div>
                            <p class="font-bold text-[13px] text-slate-800">${app.freelancer_name}</p>
                            <p class="text-[11px] text-slate-400">${new Date(app.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'})}</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-lg ${app.status === 'Approved' ? 'bg-emerald-100 text-emerald-700' : app.status === 'Rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700'}">${app.status === 'Approved' ? 'Diterima' : app.status === 'Rejected' ? 'Ditolak' : 'Menunggu'}</span>
                </div>
                ${app.proposed_price ? `<p class="text-[12px] font-bold text-[#0f766e] mb-3">${formatRupiah(app.proposed_price)}</p>` : ''}
                <p class="text-[12px] text-slate-600 leading-relaxed mb-3">${app.proposal.substring(0, 150)}${app.proposal.length > 150 ? '...' : ''}</p>
                <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                    <span class="text-[10px] text-slate-400 font-semibold">${app.proposal.length} karakter</span>
                    <div class="flex items-center gap-1.5">
                        ${app.status === 'Pending' ? `
                            <form action="/client/loker/applications/${app.id}/approve" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-[11px] hover:bg-emerald-100 transition-all">
                                    <i class="ri-check-line mr-1"></i>Terima
                                </button>
                            </form>
                            <form action="/client/loker/applications/${app.id}/reject" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-500 font-bold text-[11px] hover:bg-red-100 transition-all">
                                    <i class="ri-close-line mr-1"></i>Tolak
                                </button>
                            </form>
                        ` : `<span class="text-[11px] font-semibold text-slate-400">Tidak ada aksi</span>`}
                    </div>
                </div>
            </div>
        `).join('');
    } else {
        appsHtml = `
            <div class="text-center py-12 px-5 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-slate-300 text-2xl mx-auto mb-3 shadow-sm">
                    <i class="ri-user-search-line"></i>
                </div>
                <p class="font-bold text-slate-500 text-[13px]">Belum ada pelamar</p>
                <p class="text-[11px] text-slate-400 mt-1">Freelancer akan melamar lowongan ini</p>
            </div>`;
    }

    const summaryHtml = appCount > 0 ? `
        <div class="flex items-center gap-4 mb-5">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-lg">
                <i class="ri-user-line text-slate-400"></i>
                <span class="text-[12px] font-bold text-slate-600">${appCount} Pelamar</span>
            </div>
            ${pendingApps.length > 0 ? `<div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 rounded-lg"><i class="ri-time-line text-amber-500"></i><span class="text-[12px] font-bold text-amber-600">${pendingApps.length} Menunggu</span></div>` : ''}
            ${approvedApps.length > 0 ? `<div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 rounded-lg"><i class="ri-check-line text-emerald-500"></i><span class="text-[12px] font-bold text-emerald-600">${approvedApps.length} Diterima</span></div>` : ''}
            ${rejectedApps.length > 0 ? `<div class="flex items-center gap-2 px-3 py-1.5 bg-red-50 rounded-lg"><i class="ri-close-line text-red-400"></i><span class="text-[12px] font-bold text-red-500">${rejectedApps.length} Ditolak</span></div>` : ''}
        </div>
    ` : '';

    const box = document.getElementById('modal-applicants-preview-box');
    box.innerHTML = `
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-[1.15rem] font-black text-slate-900">Pelamar Lowongan</h2>
                <p class="text-[12px] text-slate-500 mt-0.5">${loker.title}</p>
            </div>
            <button onclick="closeApplicantsPreviewModal()" class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(85vh-180px)]">
            ${summaryHtml}
            <div class="space-y-4">
                ${appsHtml}
            </div>
        </div>
    `;

    document.getElementById('modal-applicants-preview-overlay').classList.remove('opacity-0', 'pointer-events-none');
};

function closeApplicantsPreviewModal() {
    document.getElementById('modal-applicants-preview-overlay').classList.add('opacity-0', 'pointer-events-none');
}

document.addEventListener('click', function(e) {
    if (e.target.id === 'modal-applicants-preview-overlay') closeApplicantsPreviewModal();
});

window.openApplicantsModal = function(lokerId) {
    const loker = window.__LOKKERS_DATA__.find(l => l.id === lokerId);
    if (!loker) return;

    const box = document.getElementById('modal-applicants-box');
    box.innerHTML = `
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-[1.2rem] font-black text-slate-900">Semua Pelamar</h2>
                <p class="text-[12px] text-slate-500 mt-0.5">${loker.title}</p>
            </div>
            <button onclick="closeApplicantsModal()" class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(80vh-140px)]">
            <div class="space-y-4">
                ${loker.applications.map(app => `
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#0f766e]/10 flex items-center justify-center text-[#0f766e] font-bold text-sm">
                                    ${app.freelancer_name.charAt(0)}
                                </div>
                                <div>
                                    <p class="font-bold text-[13px] text-slate-800">${app.freelancer_name}</p>
                                    <p class="text-[11px] text-slate-400">${new Date(app.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'})}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                ${app.status === 'Pending' ? `
                                    <form action="/client/loker/applications/${app.id}/approve" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-[11px] hover:bg-emerald-100 transition-all">
                                            <i class="ri-check-line mr-1"></i>Terima
                                        </button>
                                    </form>
                                    <form action="/client/loker/applications/${app.id}/reject" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-500 font-bold text-[11px] hover:bg-red-100 transition-all">
                                            <i class="ri-close-line mr-1"></i>Tolak
                                        </button>
                                    </form>
                                ` : `
                                    <span class="text-[11px] font-bold uppercase px-2.5 py-1 rounded-lg ${app.status === 'Approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500'}">
                                        ${app.status}
                                    </span>
                                `}
                            </div>
                        </div>
                        ${app.proposed_price ? `<p class="text-[12px] font-bold text-[#0f766e] mb-2">${window.DigitalanceUtils.formatRupiah(app.proposed_price)}</p>` : ''}
                        <p class="text-[12px] text-slate-600 leading-relaxed">${app.proposal}</p>
                    </div>
                `).join('')}
            </div>
        </div>
    `;

    document.getElementById('modal-applicants-overlay').classList.remove('opacity-0', 'pointer-events-none');
};

window.closeApplicantsModal = function() {
    document.getElementById('modal-applicants-overlay').classList.add('opacity-0', 'pointer-events-none');
};

document.addEventListener('click', function(e) {
    if (e.target.id === 'modal-applicants-overlay') {
        closeApplicantsModal();
    }
});
</script>
@endsection
