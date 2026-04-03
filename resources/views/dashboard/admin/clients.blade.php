@extends('layouts.dashboard')
@section('title', 'User Management | Digitalance')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/clients.css') }}">
@endsection

@section('content')
    <div class="content-scroll flex-1 px-8 py-7 overflow-y-auto">

        <!-- Page Header -->
        <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
            <div>
                <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Client Management</h1>
                <p class="text-slate-500 text-[0.95rem] mt-1">
                    Kelola seluruh client yang terdaftar di platform Digitalance.
                </p>
            </div>

            <div class="header-actions">
                <button id="btn-add-user"
                    class="inline-flex items-center gap-2 px-[22px] py-[11px] bg-[#0f766e] text-white font-display font-bold text-[13px] rounded-[12px] shadow-teal-md hover:bg-[#0a5e58] hover:shadow-teal-lg transition-all duration-200 hover:-translate-y-0.5 cursor-pointer border-none whitespace-nowrap">
                    <i class="ri-user-add-line"></i> Add New User
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="flex items-center justify-between gap-4 mb-4 flex-wrap animate-fadeUp-2">
            <div class="flex gap-2 flex-wrap" id="filter-tabs">
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150 active"
                    data-filter="all">All Users</button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="Client">Clients</button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="Freelancer">Freelancers</button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="Skomda Student">Skomda Students</button>
            </div>

            <div class="relative">
                <i
                    class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
                <input type="text" id="user-search" placeholder="Cari nama, email, role…"
                    class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
            </div>
        </div>

        <!-- Pagination Bar -->
        <div class="pagination-bar animate-fadeUp-2">
            <div class="pagination-left">
                <span class="pagination-meta" id="pagination-meta">Showing 0–0 of 0</span>
            </div>

            <div class="pagination-right">
                <label class="per-page">
                    <span>Per page</span>
                    <select id="per-page" class="per-page-select" disabled>
                        <option value="8">8</option>
                        <option value="12" selected>12</option>
                        <option value="16">16</option>
                        <option value="24">24</option>
                    </select>
                </label>

                <div class="pagination-controls" id="pagination-controls"></div>
            </div>
        </div>

        <!-- User Grid -->
        <div class="grid gap-[18px] pb-6 animate-fadeUp-3"
            style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));" id="user-grid"></div>

    </div>
@endsection

@section('modals')
    <!-- Add User Modal -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-add">
        <div
            class="modal-box bg-white rounded-3xl w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
                <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Add New User</span>
                <button onclick="closeModal('modal-add')"
                    class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
                <div class="grid grid-cols-2 gap-3 mb-4" id="add-basic-group">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Full Name</label>
                        <input id="add-name" type="text" placeholder="John Doe"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Email</label>
                        <input id="add-email" type="email" placeholder="john@example.com"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4" id="add-password-group">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Password</label>
                        <input id="add-password" type="password" placeholder="Min 8 chars"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Confirm
                            Password</label>
                        <input id="add-password-confirmation" type="password" placeholder="Repeat password"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 mb-4">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Role</label>
                    <div id="add-roles" class="flex flex-col gap-2"></div>
                    <input type="hidden" id="add-role" value="Client" />
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4" id="add-client-group">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Location</label>
                        <input id="add-location" type="text" placeholder="Jakarta, Indonesia"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Phone</label>
                        <input id="add-phone" type="text" placeholder="+62 812 xxx xxx"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                </div>

                <div id="add-skomda-group" style="display:none;">
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">NIS</label>
                            <input id="add-nis" type="text" placeholder="123456789"
                                class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Class</label>
                            <input id="add-class" type="text" placeholder="XI SIJA 1"
                                class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5 mb-4">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Major</label>
                        <select id="add-major"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]">
                            <option value="SIJA" selected>SIJA</option>
                            <option value="TJAT">TJAT</option>
                        </select>
                    </div>
                </div>

                <div id="add-freelancer-group" style="display:none;">
                    <div class="flex flex-col gap-1.5 mb-4">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Student ID
                            (skomda_students.id)</label>
                        <input id="add-student-id" type="number" placeholder="Contoh: 1"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                        <p class="text-[11px] text-slate-400 mt-1">Freelancer harus terhubung ke data Skomda Student.</p>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Bio</label>
                    <textarea id="add-bio" placeholder="Short bio…" rows="3"
                        class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 resize-y focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]"></textarea>
                </div>
            </div>

            <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0">
                <button onclick="closeModal('modal-add')"
                    class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Cancel</button>
                <button onclick="submitAddUser()"
                    class="flex-1 py-[11px] rounded-[11px] bg-[#0f766e] text-white font-bold text-[13px] cursor-pointer border-none shadow-teal-sm hover:bg-[#0a5e58] transition-all duration-150">Save
                    User</button>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-edit">
        <div
            class="modal-box bg-white rounded-3xl w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
                <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Edit User</span>
                <button onclick="closeModal('modal-edit')"
                    class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
                <input type="hidden" id="edit-uid" />
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Full Name</label>
                        <input id="edit-name" type="text" required
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Email</label>
                        <input id="edit-email" type="email" required
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 mb-4">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Status</label>
                    <select id="edit-status"
                        class="form-select py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]">
                        <option value="Active">Active</option>
                        <option value="Pending">Pending</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Suspended">Suspended</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Location</label>
                        <input id="edit-location" type="text"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Phone</label>
                        <input id="edit-phone" type="text"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 mb-4">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Bio</label>
                    <textarea id="edit-bio" rows="3"
                        class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 resize-y focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]"></textarea>
                </div>

                <div class="flex flex-col gap-1.5" id="edit-skills-group">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Skills <span
                            class="normal-case tracking-normal font-normal text-slate-400">(comma separated)</span></label>
                    <input id="edit-skills" type="text" placeholder="UI Design, React, Node.js"
                        class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
            </div>

            <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0">
                <button onclick="closeModal('modal-edit')"
                    class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Cancel</button>
                <button onclick="submitEditUser()"
                    class="flex-1 py-[11px] rounded-[11px] bg-[#0f766e] text-white font-bold text-[13px] cursor-pointer border-none shadow-teal-sm hover:bg-[#0a5e58] transition-all duration-150">Save
                    Changes</button>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-detail">
        <div
            class="modal-box bg-white rounded-3xl w-full max-w-[580px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <div id="detail-content" class="flex flex-col flex-1 overflow-y-auto"></div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-delete">
        <div class="modal-box bg-white rounded-3xl w-full max-w-[400px] shadow-2xl overflow-hidden">
            <div class="px-[26px] pt-[30px] pb-[24px] text-center">
                <div
                    class="w-[72px] h-[72px] mx-auto mb-5 bg-red-50 rounded-full flex items-center justify-center text-[2rem] text-red-500">
                    <i class="ri-error-warning-fill"></i>
                </div>
                <h3 class="font-display text-[1.2rem] font-extrabold text-slate-900 mb-2">Hapus Akun?</h3>
                <p class="text-[13.5px] text-slate-500 leading-relaxed" id="delete-text"></p>
            </div>
            <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50">
                <button onclick="closeModal('modal-delete')"
                    class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Batal</button>
                <button id="btn-confirm-delete"
                    class="flex-1 py-[11px] rounded-[11px] bg-red-500 text-white font-bold text-[13px] cursor-pointer border-none shadow-[0_3px_10px_rgba(239,68,68,.25)] hover:bg-red-600 transition-all duration-150">Ya,
                    Hapus</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__CLIENTS_PAGE__ = {
            clientsData: @json($clientsData ?? []),
            freelancersData: @json($freelancersData ?? []),
            skomdaData: @json($skomdaData ?? []),
        };
    </script>

    <script src="{{ asset('js/dashboard/admin/clients.js') }}"></script>
@endsection