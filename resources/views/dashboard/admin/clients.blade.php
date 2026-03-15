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
                <p class="text-slate-500 text-[0.95rem] mt-1">Kelola seluruh client yang terdaftar di platform Digitalance.
                </p>
            </div>
            <button id="btn-add-user"
                class="inline-flex items-center gap-2 px-[22px] py-[11px] bg-[#0f766e] text-white font-display font-bold text-[13px] rounded-[12px] shadow-teal-md hover:bg-[#0a5e58] hover:shadow-teal-lg transition-all duration-200 hover:-translate-y-0.5 cursor-pointer border-none whitespace-nowrap">
                <i class="ri-user-add-line"></i> Add New User
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-[14px] mb-6 animate-fadeUp-1" id="stats-row"></div>

        <!-- Filter Bar -->
        <div class="flex items-center justify-between gap-4 mb-6 flex-wrap animate-fadeUp-2">
            <div class="flex gap-2 flex-wrap" id="filter-tabs">
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150"
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

        <!-- Grid -->
        <div class="grid gap-[18px] pb-6 animate-fadeUp-3"
            style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));" id="user-grid"></div>

    </div>
@endsection

@section('modals')
    <!-- ══ MODAL: Add User ════════════════ -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-add">
        <div
            class="modal-box bg-white rounded-3xl w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
                <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Add New User</span>
                <button onclick="closeModal('modal-add')"
                    class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150"><i
                        class="ri-close-line"></i></button>
            </div>
            <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Full Name</label>
                        <input id="add-name" type="text" placeholder="John Doe" required
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Email</label>
                        <input id="add-email" type="email" placeholder="john@example.com" required
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                </div>
                <div class="flex flex-col gap-1.5 mb-4">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Role</label>
                    <div id="add-roles" class="flex flex-col gap-2"></div>
                    <input type="hidden" id="add-role" value="Client" />
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
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

    <!-- ══ MODAL: Edit User ═══════════════ -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-edit">
        <div
            class="modal-box bg-white rounded-3xl w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
                <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Edit User</span>
                <button onclick="closeModal('modal-edit')"
                    class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150"><i
                        class="ri-close-line"></i></button>
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

    <!-- ══ MODAL: Detail ══════════════════ -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-detail">
        <div
            class="modal-box bg-white rounded-3xl w-full max-w-[580px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <div id="detail-content" class="flex flex-col flex-1 overflow-y-auto"></div>
        </div>
    </div>

    <!-- ══ MODAL: Confirm Delete ══════════ -->
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

    <!-- ══ MODAL: Confirm Suspend ═════════ -->
    <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-suspend">
        <div class="modal-box bg-white rounded-3xl w-full max-w-[400px] shadow-2xl overflow-hidden">
            <div class="px-[26px] pt-[30px] pb-[24px] text-center">
                <div id="suspend-icon"
                    class="w-[72px] h-[72px] mx-auto mb-5 bg-orange-50 rounded-full flex items-center justify-center text-[2rem] text-orange-500">
                    <i class="ri-lock-2-fill"></i>
                </div>
                <h3 class="font-display text-[1.2rem] font-extrabold text-slate-900 mb-2" id="suspend-title">Suspend Akun?
                </h3>
                <p class="text-[13.5px] text-slate-500 leading-relaxed" id="suspend-text"></p>
            </div>
            <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50">
                <button onclick="closeModal('modal-suspend')"
                    class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Batal</button>
                <button id="btn-confirm-suspend"
                    class="flex-1 py-[11px] rounded-[11px] bg-orange-500 text-white font-bold text-[13px] cursor-pointer border-none shadow-[0_3px_10px_rgba(249,115,22,.25)] hover:bg-orange-600 transition-all duration-150">Ya,
                    Suspend</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        /* ── DATA ── */
        const clientsData = @json($clientsData ?? []);
        const freelancersData = @json($freelancersData ?? []);
        const skomdaData = @json($skomdaData ?? []);

        let usersData = [
            ...clientsData.map(u => ({
                ...u,
                _uid: 'c_' + u.id,
                role: 'Client'
            })),
            ...freelancersData.map(u => ({
                ...u,
                _uid: 'f_' + u.id,
                role: 'Freelancer'
            })),
            ...skomdaData.map(u => ({
                ...u,
                _uid: 's_' + u.id,
                role: 'Skomda Student'
            })),
        ];

        /* ── MAPS ── */
        const STATUS_BADGE = {
            Active: 'bg-emerald-100 text-emerald-800',
            Approved: 'bg-emerald-100 text-emerald-800',
            Pending: 'bg-orange-100 text-orange-800',
            Inactive: 'bg-slate-100 text-slate-600',
            Suspended: 'bg-red-100 text-red-800',
            Rejected: 'bg-red-100 text-red-800',
        };
        const STATUS_DOT = {
            Active: 'bg-emerald-400',
            Approved: 'bg-emerald-400',
            Pending: 'bg-orange-400',
            Inactive: 'bg-slate-300',
            Suspended: 'bg-red-500',
            Rejected: 'bg-red-500',
        };
        const ROLE_META = {
            'Client': {
                cls: 'ri-briefcase-4-fill',
                bg: '#eef2ff',
                color: '#6366f1'
            },
            'Freelancer': {
                cls: 'ri-vip-crown-fill',
                bg: '#fff7ed',
                color: '#f97316'
            },
            'Skomda Student': {
                cls: 'ri-graduation-cap-fill',
                bg: '#f0fdfa',
                color: '#0f766e'
            },
        };

        function avatar(u) {
            return u.avatar ||
                `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=f1f5f9&color=0f766e&size=128`;
        }

        function sBadge(s) {
            return STATUS_BADGE[s] || 'bg-slate-100 text-slate-600';
        }

        function sDot(s) {
            return STATUS_DOT[s] || 'bg-slate-300';
        }

        /* ── STATS ── */
        function renderStats() {
            const total = usersData.length;
            const cl = usersData.filter(u => u.role === 'Client').length;
            const fr = usersData.filter(u => u.role === 'Freelancer').length;
            const sk = usersData.filter(u => u.role === 'Skomda Student').length;

            document.getElementById('stats-row').innerHTML = [{
                    icon: 'ri-group-fill',
                    bg: '#f0fdfa',
                    color: '#0f766e',
                    val: total,
                    lbl: 'Total Users'
                },
                {
                    icon: 'ri-briefcase-4-fill',
                    bg: '#eef2ff',
                    color: '#6366f1',
                    val: cl,
                    lbl: 'Clients'
                },
                {
                    icon: 'ri-vip-crown-fill',
                    bg: '#fff7ed',
                    color: '#f97316',
                    val: fr,
                    lbl: 'Freelancers'
                },
                {
                    icon: 'ri-graduation-cap-fill',
                    bg: '#f0fdfa',
                    color: '#0d9488',
                    val: sk,
                    lbl: 'Skomda Students'
                },
            ].map(s => `
      <div class="bg-white border border-slate-200 rounded-2xl px-5 py-[18px] flex items-center gap-3.5 transition-all duration-200 hover:shadow-md hover:-translate-y-px">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-[20px] flex-shrink-0" style="background:${s.bg};color:${s.color}">
          <i class="${s.icon}"></i>
        </div>
        <div>
          <div class="font-display text-[1.5rem] font-extrabold text-slate-900 leading-none">${s.val}</div>
          <div class="text-[12px] text-slate-500 font-semibold mt-0.5">${s.lbl}</div>
        </div>
      </div>
    `).join('');
        }

        /* ── RENDER CARDS ── */
        function renderCards(data) {
            const grid = document.getElementById('user-grid');
            if (!data || !data.length) {
                grid.innerHTML = `
        <div class="col-span-full py-16 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-3xl">
          <i class="ri-user-search-line text-[3rem] text-slate-300 block mb-3"></i>
          <h3 class="font-display text-[1.1rem] font-bold text-slate-700 mb-1.5">No users found</h3>
          <p class="text-[13px] text-slate-400">Try adjusting the filter or search keyword.</p>
        </div>`;
                return;
            }

            grid.innerHTML = data.map(u => {
                const rm = ROLE_META[u.role] || {};
                const skills = Array.isArray(u.skills) ? u.skills : [];
                return `
        <div class="user-card relative bg-white border-[1.5px] border-slate-200 rounded-[20px] p-[22px] flex flex-col transition-all duration-200 hover:border-[#10b981] hover:shadow-[0_6px_24px_rgba(15,118,110,0.12)] hover:-translate-y-0.5">
          <div class="flex justify-between items-start mb-4">
            <div class="relative">
              <img class="w-[52px] h-[52px] rounded-[14px] object-cover border-2 border-slate-100" src="${avatar(u)}" alt="${u.name}"/>
              <span class="absolute -bottom-0.5 -right-0.5 w-[13px] h-[13px] rounded-full border-2 border-white ${sDot(u.status)}"></span>
            </div>
            <span class="text-[10.5px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-[.04em] ${sBadge(u.status)}">${u.status}</span>
          </div>

          <div class="mb-3.5">
            <h3 class="font-display text-[1rem] font-bold text-slate-900 mb-1 truncate">${u.name}</h3>
            <div class="flex items-center gap-1.5 mb-1.5">
              <span class="w-[22px] h-[22px] rounded-[6px] flex items-center justify-center text-[12px] flex-shrink-0" style="background:${rm.bg||'#f1f5f9'};color:${rm.color||'#64748b'}">
                <i class="${rm.cls||'ri-user-line'}"></i>
              </span>
              <span class="text-[12.5px] font-semibold text-slate-600">${u.role}</span>
            </div>
            <div class="flex items-center gap-1 text-[11.5px] text-slate-400">
              <i class="ri-map-pin-line"></i> ${u.location || 'Unknown Location'}
            </div>
          </div>

          <div class="flex flex-wrap gap-1.5 mb-3.5 min-h-[24px]">
            ${skills.length
              ? skills.slice(0,3).map(s=>`<span class="text-[10.5px] font-bold px-2 py-0.5 rounded-[6px] bg-slate-100 text-slate-600">${s}</span>`).join('')
                + (skills.length>3 ? `<span class="text-[10.5px] font-bold px-2 py-0.5 rounded-[6px] bg-slate-50 text-slate-400">+${skills.length-3}</span>` : '')
              : '<span class="text-[11px] text-slate-300 italic">No skills listed</span>'
            }
          </div>

          <div class="grid grid-cols-2 border-t border-b border-slate-100 py-3 mb-3.5">
            <div class="text-center">
              <div class="font-display text-[1.1rem] font-extrabold text-slate-900">${u.totalOrders||0}</div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[.06em]">Orders</div>
            </div>
            <div class="text-center border-l border-slate-100">
              <div class="font-display text-[1.1rem] font-extrabold text-[#0f766e]">${u.totalEarning||'0'}</div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[.06em]">Earned</div>
            </div>
          </div>

          <div class="flex gap-2 mt-auto">
            <button onclick="openDetail('${u._uid}')" class="flex-1 py-2 border-[1.5px] border-slate-200 rounded-[10px] text-[12.5px] font-bold text-slate-500 bg-white cursor-pointer flex items-center justify-center gap-1.5 transition-all duration-150 hover:bg-[#0f766e] hover:border-[#0f766e] hover:text-white"><i class="ri-eye-line"></i> View</button>
            <button onclick="openEdit('${u._uid}')" class="flex-1 py-2 border-[1.5px] border-slate-200 rounded-[10px] text-[12.5px] font-bold text-slate-500 bg-white cursor-pointer flex items-center justify-center gap-1.5 transition-all duration-150 hover:bg-[#0f766e] hover:border-[#0f766e] hover:text-white"><i class="ri-pencil-line"></i> Edit</button>
            <button onclick="openSuspend('${u._uid}')" class="py-2 px-3 border-[1.5px] border-slate-200 rounded-[10px] text-[12.5px] font-bold text-slate-500 bg-white cursor-pointer flex items-center justify-center transition-all duration-150 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-600">
              <i class="${u.status==='Suspended'?'ri-lock-unlock-line':'ri-lock-2-line'}"></i>
            </button>
          </div>
        </div>`;
            }).join('');
        }

        /* ── FILTER & SEARCH ── */
        function refreshGrid() {
            const active = document.querySelector('.filter-tab.active');
            const f = active ? active.dataset.filter : 'all';
            const q = (document.getElementById('user-search').value || '').toLowerCase();
            let res = usersData;
            if (f !== 'all') res = res.filter(u => u.role === f);
            if (q) res = res.filter(u =>
                u.name.toLowerCase().includes(q) ||
                (u.email && u.email.toLowerCase().includes(q)) ||
                u.role.toLowerCase().includes(q) ||
                (u.location && u.location.toLowerCase().includes(q))
            );
            renderCards(res);
        }

        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.filter-tab').forEach(t => {
                    t.classList.remove('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]',
                        'shadow-teal-sm');
                    t.classList.add('border-slate-200', 'bg-white', 'text-slate-500');
                });
                tab.classList.add('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]',
                    'shadow-teal-sm');
                tab.classList.remove('border-slate-200', 'bg-white', 'text-slate-500');
                refreshGrid();
            });
        });
        document.getElementById('user-search').addEventListener('input', refreshGrid);

        /* ── MODALS ── */
        function openModal(id) {
            const el = document.getElementById(id);
            el.classList.add('open');
            el.style.opacity = '1';
            el.style.pointerEvents = 'all';
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            el.classList.remove('open');
            el.style.opacity = '0';
            el.style.pointerEvents = 'none';
        }

        document.querySelectorAll('.overlay').forEach(ov => {
            ov.addEventListener('click', e => {
                if (e.target === ov) closeModal(ov.id);
            });
        });

        /* ── DETAIL ── */
        function openDetail(uid) {
            const u = usersData.find(x => x._uid === uid);
            if (!u) return;
            const rm = ROLE_META[u.role] || {};
            const skills = Array.isArray(u.skills) ? u.skills : [];
            const isSuspended = u.status === 'Suspended';

            document.getElementById('detail-content').innerHTML = `
      <div class="h-[110px] bg-gradient-to-r from-[#0f766e] to-[#10b981] relative flex-shrink-0">
        <button onclick="closeModal('modal-detail')" class="absolute top-3.5 right-3.5 w-8 h-8 bg-white/20 border-none rounded-full flex items-center justify-center text-white text-[18px] cursor-pointer hover:bg-white/30 transition-all"><i class="ri-close-line"></i></button>
        <img class="absolute -bottom-[38px] left-[26px] w-[76px] h-[76px] rounded-[18px] object-cover border-[3px] border-white shadow-lg" src="${avatar(u)}" alt="${u.name}"/>
      </div>
      <div class="px-[26px] pt-[50px] pb-[24px] overflow-y-auto flex-1">
        <h2 class="font-display text-[1.35rem] font-extrabold text-slate-900 mb-2">${u.name}</h2>
        <div class="flex items-center gap-2 mb-3 flex-wrap">
          <span class="text-[10.5px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-[.04em] ${sBadge(u.status)}">${u.status}</span>
          <span class="flex items-center gap-1.5 text-[12.5px] font-bold text-slate-500">
            <span class="w-5 h-5 rounded-[5px] flex items-center justify-center text-[11px]" style="background:${rm.bg||'#f1f5f9'};color:${rm.color||'#64748b'}"><i class="${rm.cls||'ri-user-line'}"></i></span>
            ${u.role}
          </span>
        </div>
        <p class="text-[13px] text-slate-500 leading-[1.65] mb-5">${u.bio||'Tidak ada bio yang ditulis oleh pengguna ini.'}</p>

        <div class="grid grid-cols-3 bg-slate-50 rounded-[16px] overflow-hidden mb-5">
          <div class="py-3.5 px-3 text-center">
            <div class="font-display text-[1.25rem] font-extrabold text-slate-900">${u.totalOrders||0}</div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[.06em] mt-0.5">Orders</div>
          </div>
          <div class="py-3.5 px-3 text-center border-x border-slate-200">
            <div class="font-display text-[1.25rem] font-extrabold text-[#0f766e]">${u.totalEarning||0}</div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[.06em] mt-0.5">Earned</div>
          </div>
          <div class="py-3.5 px-3 text-center">
            <div class="font-display text-[.9rem] font-extrabold text-slate-700">${u.lastActive||'-'}</div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[.06em] mt-0.5">Last Active</div>
          </div>
        </div>

        <div class="flex flex-col gap-2.5 mb-5">
          ${[
            {icon:'ri-mail-fill', val: u.email||'-'},
            {icon:'ri-phone-fill', val: u.phone||'-'},
            {icon:'ri-map-pin-2-fill', val: u.location||'-'},
                    ].map(r => `
                        <div class="flex items-center gap-2.5 text-[13px] text-slate-600">
                            <div class="w-8 h-8 rounded-[9px] bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center text-[14px] flex-shrink-0">
                                <i class="${r.icon}"></i>
                            </div>
                            ${r.val}
                        </div>`).join('')}
                </div>

                ${skills.length ? `
            <div>
              <div class="text-[10.5px] font-bold text-slate-400 uppercase tracking-[.1em] mb-2.5">Skills</div>
              <div class="flex flex-wrap gap-1.5 mb-4">${skills.map(s=>`<span class="px-3 py-1.5 bg-[#f0fdfa] text-[#0f766e] rounded-[8px] text-[12px] font-bold">${s}</span>`).join('')}</div>
            </div>` : ''
                }

                <div class="flex gap-2.5 pt-4 border-t border-slate-100">
                    <button onclick="closeModal('modal-detail');openEdit('${u._uid}')"
                        class="flex-1 py-2.5 rounded-[11px] bg-[#0f766e] text-white font-bold text-[13px] flex items-center justify-center gap-1.5 cursor-pointer border-none shadow-teal-sm hover:bg-[#0a5e58] transition-all duration-150">
                        <i class="ri-pencil-line"></i> Edit
                    </button>
                    <button onclick="closeModal('modal-detail');openSuspend('${u._uid}')"
                        class="flex-1 py-2.5 rounded-[11px] ${isSuspended?'bg-[#0f766e]':'bg-orange-500'} text-white font-bold text-[13px] flex items-center justify-center gap-1.5 cursor-pointer border-none transition-all duration-150 hover:opacity-90">
                        <i class="${isSuspended?'ri-lock-unlock-line':'ri-lock-2-line'}"></i> ${isSuspended?'Unsuspend':'Suspend'}
                    </button>
                    <button onclick="closeModal('modal-detail');openDelete('${u._uid}')"
                        class="flex-1 py-2.5 rounded-[11px] bg-red-500 text-white font-bold text-[13px] flex items-center justify-center gap-1.5 cursor-pointer border-none shadow-[0_3px_10px_rgba(239,68,68,.25)] hover:bg-red-600 transition-all duration-150">
                        <i class="ri-delete-bin-line"></i> Hapus
                    </button>
                </div>
            </div>`;
            openModal('modal-detail');
        }

        /* ── EDIT ── */
        function openEdit(uid) {
            const u = usersData.find(x => x._uid === uid);
            if (!u) return;
            document.getElementById('edit-uid').value = uid;
            document.getElementById('edit-name').value = u.name || '';
            document.getElementById('edit-email').value = u.email || '';
            document.getElementById('edit-status').value = u.status || 'Active';
            document.getElementById('edit-location').value = u.location || '';
            document.getElementById('edit-phone').value = u.phone || '';
            document.getElementById('edit-bio').value = u.bio || '';
            const sg = document.getElementById('edit-skills-group');
            if (u.role === 'Freelancer' || u.role === 'Skomda Student') {
                sg.style.display = '';
                document.getElementById('edit-skills').value = Array.isArray(u.skills) ? u.skills.join(', ') : '';
            } else {
                sg.style.display = 'none';
            }
            openModal('modal-edit');
        }

        function submitEditUser() {
            const uid = document.getElementById('edit-uid').value;
            const idx = usersData.findIndex(u => u._uid === uid);
            if (idx === -1) return;
            const skillsRaw = document.getElementById('edit-skills').value;
            const skills = skillsRaw ? skillsRaw.split(',').map(s => s.trim()).filter(Boolean) : usersData[idx].skills;
            usersData[idx] = {
                ...usersData[idx],
                name: document.getElementById('edit-name').value.trim(),
                email: document.getElementById('edit-email').value.trim(),
                status: document.getElementById('edit-status').value,
                location: document.getElementById('edit-location').value.trim(),
                phone: document.getElementById('edit-phone').value.trim(),
                bio: document.getElementById('edit-bio').value.trim(),
                skills,
            };
            closeModal('modal-edit');
            refreshGrid();
            renderStats();
            showToast(`${usersData[idx].name} berhasil diperbarui.`, 'success');
        }

        /* ── ADD USER ── */
        function initAddRoles() {
            const roles = [{
                    val: 'Client',
                    label: 'Client',
                    desc: 'Pemberi kerja / klien',
                    icon: 'ri-briefcase-line'
                },
                {
                    val: 'Freelancer',
                    label: 'Freelancer',
                    desc: 'Penyedia jasa independen',
                    icon: 'ri-vip-crown-line'
                },
                {
                    val: 'Skomda Student',
                    label: 'Skomda Student',
                    desc: 'Siswa magang / program Skomda',
                    icon: 'ri-graduation-cap-line'
                },
            ];
            const container = document.getElementById('add-roles');
            container.innerHTML = roles.map((r, i) => `
      <div class="role-opt flex items-center gap-3 p-3 border-[1.5px] ${i===0?'border-[#0f766e] bg-[#f0fdfa]':'border-slate-200 bg-white'} rounded-[12px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:bg-[#f0fdfa]" data-val="${r.val}">
        <div class="w-[38px] h-[38px] rounded-[10px] bg-white shadow-sm flex items-center justify-center text-[17px] text-[#0f766e] flex-shrink-0"><i class="${r.icon}"></i></div>
        <div class="flex-1">
          <div class="text-[13.5px] font-bold text-slate-800">${r.label}</div>
          <div class="text-[11px] text-slate-400 mt-0.5">${r.desc}</div>
        </div>
        <div class="role-check w-5 h-5 rounded-full border-2 ${i===0?'bg-[#0f766e] border-[#0f766e]':'border-slate-300'} flex items-center justify-center text-[10px] ${i===0?'text-white':'text-transparent'} flex-shrink-0 transition-all duration-150"><i class="ri-check-line"></i></div>
      </div>
    `).join('');
            container.querySelectorAll('.role-opt').forEach(opt => {
                opt.addEventListener('click', () => {
                    container.querySelectorAll('.role-opt').forEach(o => {
                        o.classList.remove('border-[#0f766e]', 'bg-[#f0fdfa]');
                        o.classList.add('border-slate-200', 'bg-white');
                        const chk = o.querySelector('.role-check');
                        chk.classList.remove('bg-[#0f766e]', 'border-[#0f766e]', 'text-white');
                        chk.classList.add('border-slate-300', 'text-transparent');
                    });
                    opt.classList.add('border-[#0f766e]', 'bg-[#f0fdfa]');
                    opt.classList.remove('border-slate-200', 'bg-white');
                    const chk = opt.querySelector('.role-check');
                    chk.classList.add('bg-[#0f766e]', 'border-[#0f766e]', 'text-white');
                    chk.classList.remove('border-slate-300', 'text-transparent');
                    document.getElementById('add-role').value = opt.dataset.val;
                });
            });
        }

        function submitAddUser() {
            const name = document.getElementById('add-name').value.trim();
            const email = document.getElementById('add-email').value.trim();
            if (!name || !email) {
                showToast('Nama dan email wajib diisi.', 'danger');
                return;
            }
            const newUser = {
                id: Date.now(),
                _uid: 'new_' + Date.now(),
                name,
                email,
                role: document.getElementById('add-role').value,
                location: document.getElementById('add-location').value.trim(),
                phone: document.getElementById('add-phone').value.trim(),
                bio: document.getElementById('add-bio').value.trim(),
                status: 'Pending',
                skills: [],
                totalOrders: 0,
                totalEarning: '0',
                lastActive: '-',
            };
            usersData.push(newUser);
            refreshGrid();
            renderStats();
            closeModal('modal-add');
            document.getElementById('add-name').value = '';
            document.getElementById('add-email').value = '';
            document.getElementById('add-location').value = '';
            document.getElementById('add-phone').value = '';
            document.getElementById('add-bio').value = '';
            initAddRoles();
            document.getElementById('add-role').value = 'Client';
            showToast(`${name} berhasil ditambahkan.`, 'success');
        }

        document.getElementById('btn-add-user').addEventListener('click', () => openModal('modal-add'));

        /* ── SUSPEND ── */
        let suspendTarget = null;

        function openSuspend(uid) {
            const u = usersData.find(x => x._uid === uid);
            if (!u) return;
            suspendTarget = uid;
            const isSusp = u.status === 'Suspended';
            const icon = document.getElementById('suspend-icon');
            icon.innerHTML = isSusp ? '<i class="ri-lock-unlock-fill"></i>' : '<i class="ri-lock-2-fill"></i>';
            icon.className =
                `w-[72px] h-[72px] mx-auto mb-5 rounded-full flex items-center justify-center text-[2rem] ${isSusp?'bg-[#f0fdfa] text-[#0f766e]':'bg-orange-50 text-orange-500'}`;
            document.getElementById('suspend-title').textContent = isSusp ? 'Unsuspend Akun?' : 'Suspend Akun?';
            document.getElementById('suspend-text').innerHTML = isSusp ?
                `Akun <strong>${u.name}</strong> akan diaktifkan kembali.` :
                `Akun <strong>${u.name}</strong> akan ditangguhkan dan tidak bisa login sementara.`;
            const btn = document.getElementById('btn-confirm-suspend');
            btn.textContent = isSusp ? 'Ya, Aktifkan' : 'Ya, Suspend';
            btn.className =
                `flex-1 py-[11px] rounded-[11px] ${isSusp?'bg-[#0f766e] shadow-teal-sm hover:bg-[#0a5e58]':'bg-orange-500 shadow-[0_3px_10px_rgba(249,115,22,.25)] hover:bg-orange-600'} text-white font-bold text-[13px] cursor-pointer border-none transition-all duration-150`;
            openModal('modal-suspend');
        }

        document.getElementById('btn-confirm-suspend').addEventListener('click', () => {
            if (!suspendTarget) return;
            const idx = usersData.findIndex(u => u._uid === suspendTarget);
            if (idx === -1) return;
            const was = usersData[idx].status === 'Suspended';
            usersData[idx].status = was ? 'Active' : 'Suspended';
            closeModal('modal-suspend');
            refreshGrid();
            renderStats();
            showToast(was ? `${usersData[idx].name} telah diaktifkan kembali.` :
                `${usersData[idx].name} berhasil di-suspend.`, was ? 'success' : 'warn');
            suspendTarget = null;
        });

        /* ── DELETE ── */
        let deleteTarget = null;

        function openDelete(uid) {
            const u = usersData.find(x => x._uid === uid);
            if (!u) return;
            deleteTarget = uid;
            document.getElementById('delete-text').innerHTML =
                `Tindakan ini tidak dapat dibatalkan. Akun <strong>${u.name}</strong> dan semua datanya akan dihapus permanen.`;
            openModal('modal-delete');
        }

        document.getElementById('btn-confirm-delete').addEventListener('click', () => {
            if (!deleteTarget) return;
            const idx = usersData.findIndex(u => u._uid === deleteTarget);
            if (idx === -1) return;
            const name = usersData[idx].name;
            usersData.splice(idx, 1);
            closeModal('modal-delete');
            refreshGrid();
            renderStats();
            showToast(`${name} berhasil dihapus.`, 'success');
            deleteTarget = null;
        });

        /* ── TOAST ── */
        function showToast(msg, type = 'success') {
            const icons = {
                success: 'ri-check-double-line',
                danger: 'ri-close-circle-line',
                warn: 'ri-alert-line'
            };
            const colors = {
                success: 'bg-white border-[#10b981] text-emerald-800',
                danger: 'bg-white border-red-400 text-red-800',
                warn: 'bg-white border-orange-400 text-orange-800',
            };
            const el = document.createElement('div');
            el.className =
                `toast flex items-center gap-2.5 px-[18px] py-[13px] border-[1.5px] rounded-[13px] text-[13px] font-semibold max-w-[300px] shadow-lg ${colors[type]||colors.success}`;
            el.innerHTML =
                `<i class="text-[17px] flex-shrink-0 ${icons[type]||icons.success}"></i><span>${msg}</span><button onclick="dismissToast(this.parentElement)" class="ml-auto bg-transparent border-none cursor-pointer opacity-50 hover:opacity-100 text-[15px] text-inherit p-0 leading-none"><i class="ri-close-line"></i></button>`;
            document.getElementById('toast-container').appendChild(el);
            setTimeout(() => dismissToast(el), 3500);
        }

        function dismissToast(el) {
            if (!el || el.classList.contains('hide')) return;
            el.classList.add('hide');
            setTimeout(() => el.remove(), 280);
        }

        /* ── GLOBAL SEARCH ── */
        const MENUS = [{
                label: 'Dashboard',
                sub: 'Halaman utama admin',
                icon: 'ri-dashboard-fill',
                url: '{{ route('admin.dashboard') }}'
            },
            {
                label: 'Clients',
                sub: 'User › Daftar client',
                icon: 'ri-user-line',
                url: '{{ route('admin.clients.index') }}'
            },
            {
                label: 'Freelancers',
                sub: 'User › Daftar freelancer',
                icon: 'ri-user-star-line',
                url: '{{ route('admin.freelancers.index') }}'
            },
            {
                label: 'Skomda Students',
                sub: 'User › Data siswa skomda',
                icon: 'ri-user-line',
                url: '{{ route('admin.skomda-students.index') }}'
            },
            {
                label: 'Administrators',
                sub: 'Admin › Daftar admin',
                icon: 'ri-user-star-line',
                url: '{{ route('admin.admins') }}'
            },
            {
                label: 'Services',
                sub: 'Daftar layanan',
                icon: 'ri-tools-line',
                url: '{{ route('admin.services') }}'
            },
            {
                label: 'Service Categories',
                sub: 'Kategori layanan',
                icon: 'ri-layout-grid-line',
                url: '{{ route('admin.service-categories') }}'
            },
            {
                label: 'Transactions',
                sub: 'Riwayat transaksi',
                icon: 'ri-bank-card-line',
                url: '{{ route('admin.transactions') }}'
            },
            {
                label: 'Profile Admin',
                sub: 'Akun › Profil administrator',
                icon: 'ri-user-line',
                url: '{{ url('admin/profile') }}'
            },
        ];

        (() => {
            const inp = document.getElementById('search-input');
            const dd = document.getElementById('search-dropdown');
            const res = document.getElementById('search-results');
            const render = q => {
                const filt = q ? MENUS.filter(m => m.label.toLowerCase().includes(q) || m.sub.toLowerCase()
                    .includes(q)) : MENUS;
                if (!filt.length) {
                    res.innerHTML =
                        `<div class="px-4 py-5 text-center text-slate-400 text-[13px]"><i class="ri-search-2-line text-2xl mb-1 block"></i>Tidak ada hasil untuk "<strong>${q}</strong>"</div>`;
                } else {
                    res.innerHTML = filt.map(m => `
          <a href="${m.url}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors group">
            <span class="w-8 h-8 rounded-lg bg-[#f0fdfa] flex items-center justify-center text-[#0f766e] text-[15px] flex-shrink-0"><i class="${m.icon}"></i></span>
            <div class="flex-1 min-w-0">
              <p class="text-[13.5px] font-semibold text-slate-800 group-hover:text-[#0f766e]">${m.label}</p>
              <p class="text-[11px] text-slate-400 truncate">${m.sub}</p>
            </div>
            <i class="ri-arrow-right-s-line text-slate-300 group-hover:text-[#0f766e] text-[17px]"></i>
          </a>`).join('');
                }
                dd.classList.remove('hidden');
            };
            inp.addEventListener('focus', () => render(inp.value.toLowerCase()));
            inp.addEventListener('input', () => render(inp.value.toLowerCase()));
            document.addEventListener('click', e => {
                if (!document.getElementById('search-wrapper').contains(e.target)) dd.classList.add('hidden');
            });
            inp.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    dd.classList.add('hidden');
                    inp.blur();
                }
            });
        })();

        /* ── NOTIF ── */
        document.getElementById('notif-btn').addEventListener('click', () => {
            document.getElementById('notif-dot').style.display = 'none';
        });

        /* ── INIT ── */
        initAddRoles();
        renderStats();
        renderCards(usersData);
    </script>
@endsection
