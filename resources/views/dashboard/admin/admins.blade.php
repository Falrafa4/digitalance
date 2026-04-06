@extends('layouts.dashboard')
@section('title', 'Admin Management | Digitalance')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/admins.css') }}">
@endsection

@section('content')
    <div class="content-scroll flex-1 px-8 py-7 overflow-y-auto">
        <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
            <div>
                <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Admin Management</h1>
                <p class="text-slate-500 text-[0.95rem] mt-1">
                    Kelola akun admin dan hak akses di platform Digitalance.
                </p>
            </div>

            <div class="header-actions">
                <button id="btn-add-admin"
                    class="inline-flex items-center gap-2 px-[22px] py-[11px] bg-[#0f766e] text-white font-display font-bold text-[13px] rounded-[12px] shadow-teal-md hover:bg-[#0a5e58] hover:shadow-teal-lg transition-all duration-200 hover:-translate-y-0.5 cursor-pointer border-none whitespace-nowrap">
                    <i class="ri-shield-user-line"></i> Tambah Admin
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 mb-4 flex-wrap animate-fadeUp-2">
            <div class="flex gap-2 flex-wrap" id="filter-tabs">
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150 active"
                    data-filter="all">Semua</button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="Active">Aktif</button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="Inactive">Nonaktif</button>
            </div>

            <div class="relative">
                <i
                    class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
                <input type="text" id="user-search" placeholder="Cari nama, email, role…"
                    class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
            </div>
        </div>

        <div class="grid gap-[18px] pb-6 animate-fadeUp-3"
            style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));" id="admin-card-grid">
            @foreach ($administrators as $admin)
                <div class="user-card-item bg-white border-[1.5px] border-slate-100 rounded-[20px] p-5 flex flex-col hover:border-[#0f766e] hover:shadow-xl transition-all duration-300 group"
                    data-id="{{ $admin->id }}" data-name="{{ $admin->name }}" data-email="{{ $admin->email }}"
                    data-phone="{{ $admin->phone }}" data-status="{{ $admin->status }}" data-bio="{{ $admin->bio }}">

                    <div class="flex justify-between items-start mb-4">
                        <div class="relative">
                            <img class="w-14 h-14 rounded-[15px] object-cover border-2 border-slate-50 group-hover:border-teal-100 transition-all"
                                src="{{ $admin->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($admin->name) . '&background=0f766e&color=ffffff' }}"
                                alt="{{ $admin->name }}" />
                            <span
                                class="absolute -bottom-1 -right-1 w-4 h-4 border-2 border-white rounded-full {{ $admin->status === 'Active' ? 'bg-green-500' : 'bg-slate-300' }}"></span>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $admin->status === 'Active' ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' }}">
                            {{ $admin->status }}
                        </span>
                    </div>

                    <h3 class="font-display font-bold text-[16px] text-slate-900 mb-0.5 line-clamp-1">{{ $admin->name }}</h3>
                    <div class="flex items-center gap-1.5 text-[12px] text-[#0f766e] font-bold mb-3">
                        <i class="ri-shield-user-line"></i>
                        <span>Administrator</span>
                    </div>

                    <div class="space-y-1.5 mb-5">
                        <div class="flex items-center gap-2 text-[12.5px] text-slate-500">
                            <i class="ri-mail-line text-slate-400"></i>
                            <span class="truncate">{{ $admin->email }}</span>
                        </div>
                    </div>

                    <button
                        class="mt-auto w-full py-2.5 rounded-xl bg-slate-50 text-slate-600 font-bold text-[12.5px] flex items-center justify-center gap-2 hover:bg-[#0f766e] hover:text-white transition-all duration-200"
                        onclick="openAdminModal({{ $admin->id }})">
                        <i class="ri-eye-line"></i> Lihat Profil
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('modals')
    <!-- MODAL: View Admin Profile -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="admin-modal-overlay">
        <div class="modal-box bg-white rounded-3xl w-full max-w-[580px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden"
            id="admin-modal-box">
        </div>
    </div>

    <!-- MODAL: Create Admin -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modalCreate">
        <div
            class="modal-box bg-white rounded-3xl w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
                <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Tambah Admin Baru</span>
                <button type="button" id="btn-close-add-admin"
                    class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <form id="form-add-admin" action="{{ route('admin.admins.store') }}" method="POST"
                class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama
                                Lengkap</label>
                            <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                                class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] transition-all" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email</label>
                            <input type="email" name="email" required placeholder="budi@digitalance.id"
                                class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Password</label>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter"
                                class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] transition-all" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Konfirmasi
                                Password</label>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi password"
                                class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] transition-all" />
                        </div>
                    </div>

                    <input type="hidden" name="status" value="Active">
                </div>

                <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50">
                    <button type="button" id="btn-cancel-add-admin"
                        class="flex-1 py-3 rounded-xl bg-slate-200 text-slate-600 font-bold text-[13px] hover:bg-slate-300 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-[#0f766e] text-white font-bold text-[13px] shadow-teal-sm hover:bg-[#0a5e58] transition-all">Simpan
                        Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Admin -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-edit-admin">
    </div>

    <!-- MODAL: Delete Admin -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-delete-admin">
        <div class="modal-box bg-white rounded-3xl w-full max-w-[400px] shadow-2xl overflow-hidden">
            <div class="px-[26px] pt-[30px] pb-[24px] text-center">
                <div
                    class="w-[72px] h-[72px] mx-auto mb-5 bg-red-50 rounded-full flex items-center justify-center text-[2rem] text-red-500">
                    <i class="ri-error-warning-fill"></i>
                </div>
                <h3 class="font-display text-[1.2rem] font-extrabold text-slate-900 mb-2">Hapus Admin?</h3>
                <p class="text-[13.5px] text-slate-500 leading-relaxed">Tindakan ini tidak bisa dibatalkan.</p>
            </div>
            <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50">
                <button
                    class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer"
                    onclick="closeModal('modal-delete-admin')">Batal</button>
                <button id="btn-confirm-delete-admin"
                    class="flex-1 py-[11px] rounded-[11px] bg-red-500 text-white font-bold text-[13px] cursor-pointer hover:bg-red-600 transition-all">Ya,
                    Hapus</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/dashboard/admin/admins.js') }}"></script>
@endsection