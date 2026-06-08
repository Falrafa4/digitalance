@extends('layouts.dashboard')

@section('title', 'Manajemen Lowongan | Digitalance')

@php
    $lokersPageData = collect($lokkers instanceof \Illuminate\Pagination\LengthAwarePaginator ? $lokkers->items() : $lokkers)
        ->map(function ($loker) {
            return [
                'id' => $loker->id,
                'title' => $loker->title,
                'description' => $loker->description,
                'status' => $loker->status,
                'budget_min' => $loker->budget_min,
                'budget_max' => $loker->budget_max,
                'deadline' => $loker->deadline ? \Carbon\Carbon::parse($loker->deadline)->toDateString() : null,
                'created_at' => $loker->created_at?->toIso8601String(),
                'applications_count' => $loker->applications->count(),
                'pending_applications_count' => $loker->applications->where('status', 'Pending')->count(),
                'client' => [
                    'name' => $loker->client?->name ?? 'Client',
                    'email' => $loker->client?->email,
                ],
                'category' => $loker->category ? [
                    'name' => $loker->category->name,
                ] : null,
                'routes' => [
                    'update' => route('admin.loker.update', $loker),
                    'destroy' => route('admin.loker.destroy', $loker),
                ],
                'applications' => $loker->applications
                    ->sortByDesc('created_at')
                    ->values()
                    ->map(function ($application) {
                        return [
                            'id' => $application->id,
                            'proposal' => $application->proposal,
                            'proposed_price' => $application->proposed_price,
                            'status' => $application->status,
                            'created_at' => $application->created_at?->toIso8601String(),
                            'freelancer' => [
                                'name' => $application->freelancer?->skomda_student?->name ?? 'Freelancer',
                                'major' => $application->freelancer?->skomda_student?->major,
                                'email' => $application->freelancer?->skomda_student?->email,
                            ],
                            'routes' => [
                                'approve' => route('admin.loker.applications.approve', $application),
                                'reject' => route('admin.loker.applications.reject', $application),
                            ],
                        ];
                    })
                    ->all(),
            ];
        })
        ->values()
        ->all();
@endphp

@section('content')
    <x-crud-header title="Manajemen Lowongan"
        subtitle="Pantau lowongan client dan moderasi lamaran freelancer dalam satu halaman."
        count="{{ $stats['total'] }}" countLabel="Total Lowongan" />

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8 animate-fadeUp-2">
        <div class="bg-white border border-slate-200 rounded-[20px] p-5">
            <div class="w-11 h-11 rounded-2xl bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center text-xl mb-4">
                <i class="ri-briefcase-2-line"></i>
            </div>
            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400 mb-1">Total Lowongan</p>
            <p class="text-[1.8rem] font-black text-slate-900 leading-none">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-[20px] p-5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4">
                <i class="ri-lock-unlock-line"></i>
            </div>
            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400 mb-1">Masih Dibuka</p>
            <p class="text-[1.8rem] font-black text-slate-900 leading-none">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-[20px] p-5">
            <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl mb-4">
                <i class="ri-door-lock-line"></i>
            </div>
            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400 mb-1">Sudah Ditutup</p>
            <p class="text-[1.8rem] font-black text-slate-900 leading-none">{{ $stats['closed'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-[20px] p-5">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4">
                <i class="ri-user-follow-line"></i>
            </div>
            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400 mb-1">Lamaran Pending</p>
            <p class="text-[1.8rem] font-black text-slate-900 leading-none">{{ $stats['pending_applications'] }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 mb-8 flex-wrap animate-fadeUp-3">
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.loker.index', array_filter(['q' => request('q'), 'category' => request('category')])) }}"
                class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ !request('status') ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Semua
            </a>
            @foreach (['Open' => 'Dibuka', 'Closed' => 'Ditutup'] as $value => $label)
                <a href="{{ route('admin.loker.index', array_filter(['status' => $value, 'q' => request('q'), 'category' => request('category')])) }}"
                    class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('status') === $value ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.loker.index') }}" method="GET" class="flex items-center gap-3 flex-wrap">
            <div class="relative">
                <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[15px]"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari lowongan atau client..."
                    class="pl-10 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
            </div>
            <select name="category"
                class="py-[9px] px-3.5 border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @if (request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <button type="submit"
                class="px-4 py-[9px] rounded-[11px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all shadow-teal-sm">
                Filter
            </button>
        </form>
    </div>

    @if ($lokkers->count() > 0)
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 animate-fadeUp-3">
            @foreach ($lokkers as $loker)
                @php
                    $isOpen = $loker->status === 'Open';
                    $statusClass = $isOpen ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600';
                    $pendingCount = $loker->applications->where('status', 'Pending')->count();
                    $toggleStatus = $isOpen ? 'Closed' : 'Open';
                    $toggleLabel = $isOpen ? 'Tutup' : 'Buka';
                    $budgetText = null;

                    if ($loker->budget_min && $loker->budget_max) {
                        $budgetText = 'Rp' . number_format((float) $loker->budget_min, 0, ',', '.') . ' - Rp' . number_format((float) $loker->budget_max, 0, ',', '.');
                    } elseif ($loker->budget_min) {
                        $budgetText = 'Min Rp' . number_format((float) $loker->budget_min, 0, ',', '.');
                    } elseif ($loker->budget_max) {
                        $budgetText = 'Maks Rp' . number_format((float) $loker->budget_max, 0, ',', '.');
                    }
                @endphp

                <div class="bg-white border border-slate-200 rounded-[22px] p-6 shadow-sm hover:shadow-teal-sm transition-all">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $statusClass }}">
                                    {{ $loker->status }}
                                </span>
                                @if ($loker->category)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-blue-50 text-blue-600">
                                        {{ $loker->category->name }}
                                    </span>
                                @endif
                                @if ($pendingCount > 0)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-600">
                                        {{ $pendingCount }} pending
                                    </span>
                                @endif
                            </div>
                            <h2 class="font-display text-[1.12rem] font-black text-slate-900 leading-tight">
                                {{ $loker->title }}
                            </h2>
                            <p class="text-[12px] text-slate-500 font-semibold mt-2">
                                Client: <span class="text-slate-700">{{ $loker->client->name ?? 'Client' }}</span>
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 border border-slate-100 flex items-center justify-center text-xl flex-shrink-0">
                            <i class="ri-briefcase-2-line"></i>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-[18px] border border-slate-100 p-4 mb-4">
                        <p class="text-[13px] text-slate-600 leading-relaxed line-clamp-3">{{ $loker->description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-[12px] mb-5">
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Budget</p>
                            <p class="font-bold text-slate-700">{{ $budgetText ?? 'Belum ditentukan' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Deadline</p>
                            <p class="font-bold text-slate-700">
                                {{ $loker->deadline ? \Carbon\Carbon::parse($loker->deadline)->format('d M Y') : 'Fleksibel' }}
                            </p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Lamaran</p>
                            <p class="font-bold text-slate-700">{{ $loker->applications->count() }} freelancer</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Diposting</p>
                            <p class="font-bold text-slate-700">{{ $loker->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100">
                        <button type="button" data-open-loker-detail="{{ $loker->id }}"
                            class="flex-1 py-3 rounded-[14px] bg-[#f0fdfa] text-[#0f766e] font-bold text-[13px] hover:bg-[#0f766e] hover:text-white transition-all">
                            Lihat Detail
                        </button>
                        <button type="button"
                            data-submit-form="toggle-loker-{{ $loker->id }}"
                            data-confirm-message="{{ $isOpen ? 'Tutup lowongan ini dari halaman admin?' : 'Buka kembali lowongan ini dari halaman admin?' }}"
                            class="px-4 py-3 rounded-[14px] {{ $isOpen ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} font-bold text-[13px] transition-all">
                            {{ $toggleLabel }}
                        </button>
                        <button type="button"
                            data-submit-form="delete-loker-{{ $loker->id }}"
                            data-confirm-message="Hapus lowongan ini secara permanen?"
                            class="px-4 py-3 rounded-[14px] bg-red-50 text-red-600 hover:bg-red-100 font-bold text-[13px] transition-all">
                            Hapus
                        </button>
                    </div>

                    <form id="toggle-loker-{{ $loker->id }}" action="{{ route('admin.loker.update', $loker) }}" method="POST"
                        class="hidden">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $toggleStatus }}">
                    </form>

                    <form id="delete-loker-{{ $loker->id }}" action="{{ route('admin.loker.destroy', $loker) }}" method="POST"
                        class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-12 flex justify-center pagination-container">
            {{ $lokkers->onEachSide(1)->links('dashboard.admin.partials.loker-pagination') }}
        </div>
    @else
        <x-ui.empty-state icon="ri-briefcase-2-line" title="Tidak Ada Lowongan"
            description="Belum ada lowongan yang sesuai dengan filter pencarian saat ini." />
    @endif
@endsection

@section('modals')
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 p-4"
        id="modal-admin-loker-overlay">
        <div class="modal-box bg-white rounded-[28px] w-full max-w-[760px] max-h-[88vh] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300 flex flex-col"
            id="modal-admin-loker-box" role="dialog" aria-modal="true" aria-labelledby="modal-admin-loker-title">
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__ADMIN_LOKERS_PAGE__ = {
            csrfToken: @json(csrf_token()),
            data: @json($lokersPageData),
        };
    </script>
    <script src="{{ asset('js/dashboard/admin/lokers.js') }}"></script>
@endsection
