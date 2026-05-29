@extends('layouts.dashboard')
@section('title', 'Manajemen Pengguna | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/clients.css') }}">
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Manajemen Pengguna</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola seluruh pengguna platform: Client, Freelancer, dan Siswa Skomda.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="ri-group-line"></i>
                </div>
                <div>
<div class="text-[1.2rem] font-black text-slate-900 leading-none">{{ $users->total() }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Pengguna</div>
                </div>
            </div>
            <button id="btn-add-user" class="px-6 py-3.5 bg-[#0f766e] text-white font-black text-[13px] rounded-2xl shadow-teal-md hover:bg-[#0a5e58] transition-all flex items-center gap-2">
                <i class="ri-user-add-line"></i> Tambah User
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 mb-8 flex-wrap animate-fadeUp-2">
        <div class="flex gap-2 flex-wrap">
             <a href="{{ route('admin.clients.index', ['role' => 'all']) }}" 
               class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ $role === 'all' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500 hover:border-[#0f766e]' }}">
                Semua User
            </a>
            @foreach(['Client', 'Freelancer', 'Siswa Skomda'] as $r)
                <a href="{{ route('admin.clients.index', ['role' => $r]) }}" 
                   class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ $role === $r ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500 hover:border-[#0f766e]' }}">
                    {{ $r }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.clients.index') }}" method="GET" class="relative">
            <input type="hidden" name="role" value="{{ $role }}">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[15px]"></i>
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau email..." 
                   class="pl-10 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[14px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
        </form>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-200 overflow-hidden animate-fadeUp-3 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Detail Pengguna</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Role</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Bergabung</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($user->profile_photo)
                                        <img
                                            src="{{ asset('storage/' . $user->profile_photo) }}"
                                            class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-sm"
                                            alt="{{ $user->name }}"
                                        >
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-sm group-hover:bg-[#0f766e] group-hover:text-white transition-all">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="text-[13.5px] font-black text-slate-900">{{ $user->name }}</span>
                                        <span class="text-[11px] text-slate-400 font-bold tracking-tight">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-ui.status-badge :status="$user->role" />
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[12px] text-slate-500 font-bold uppercase tracking-widest">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="window.openUserDetail('{{ $user->role }}', {{ $user->id }})" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all" title="View Detail">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button onclick="window.openUserModal('{{ $user->role }}', {{ $user->id }})" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-[#0f766e] hover:text-white transition-all" title="Edit User">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    @if($user->role === 'Client')
                                        <form id="delete-user-{{ $user->id }}" action="{{ route('admin.clients.destroy', $user->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="window.confirmDeleteUser({{ $user->id }}, '{{ $user->name }}')" class="w-9 h-9 rounded-xl bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-ui.empty-state icon="ri-user-search-line" title="No Users Found" description="Tidak ada pengguna yang ditemukan dengan kriteria ini." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-10 pagination-container">
        {{ $users->links() }}
    </div>
@endsection

@section('modals')
    <!-- Edit User Modal (Dynamic based on role) -->
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-user-overlay">
        <div class="bg-white rounded-[32px] w-full max-w-[620px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-user-box">
             <!-- Content filled by JS -->
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-password-overlay">
        <div class="bg-white rounded-[32px] w-full max-w-[450px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-password-box">
             <!-- Content filled by JS -->
        </div>
    </div>

    <!-- Add User Modal (Dynamic Roles) -->
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-add-overlay">
        <div class="bg-white rounded-[32px] w-full max-w-[620px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-add-box">
             <div class="p-7">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-[1.5rem] font-black text-slate-900">Tambah User Baru</h2>
                        <p class="text-slate-400 text-sm font-medium mt-1">Daftarkan pengguna baru ke platform.</p>
                    </div>
                    <button onclick="window.closeAddModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-8">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">Pilih Peran User</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" onclick="window.setAddRole('Client')" id="role-btn-Client" class="role-selector py-3 rounded-2xl border-2 border-slate-100 font-black text-[11px] uppercase tracking-wider text-slate-400 hover:border-[#0f766e] hover:text-[#0f766e] transition-all active">Client</button>
                        <button type="button" onclick="window.setAddRole('Freelancer')" id="role-btn-Freelancer" class="role-selector py-3 rounded-2xl border-2 border-slate-100 font-black text-[11px] uppercase tracking-wider text-slate-400 hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Freelancer</button>
                        <button type="button" onclick="window.setAddRole('Skomda Student')" id="role-btn-Student" class="role-selector py-3 rounded-2xl border-2 border-slate-100 font-black text-[11px] uppercase tracking-wider text-slate-400 hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Siswa</button>
                    </div>
                </div>

                <form id="add-user-form" method="POST" action="{{ route('admin.clients.store') }}">
                    @csrf
                    <input type="hidden" name="role" id="add-role-input" value="Client">

                    <div id="fields-common" class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4">
                        <!-- Client & Common Fields -->
                        <div class="field-group" id="group-name">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="e.g. Budi Santoso">
                        </div>

                        <div class="field-group" id="group-email">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Alamat Email</label>
                            <input type="email" name="email" id="input-email" value="{{ old('email') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="name@example.com">
                        </div>

                        <div class="field-group" id="group-phone">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="+62...">
                        </div>

                        <div class="field-group" id="group-password">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Kata Sandi</label>
                            <input type="password" name="password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="Min. 8 characters">
                        </div>

                        <!-- Freelancer specific: Select Student -->
                        <div class="field-group hidden" id="group-student">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Pilih Siswa <span class="text-red-400">*</span></label>
                            <div class="relative" id="student-combobox">
                                <input type="text" id="student-search-input" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="Cari nama atau NIS..." autocomplete="off">
                                <input type="hidden" name="student_id" id="student-id-input">
                                <div id="student-search-list" class="hidden absolute z-[120] mt-2 w-full max-h-56 overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-200/60 p-1.5"></div>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-1.5">Pilih siswa dari daftar agar ID siswa tersimpan.</p>
                        </div>

                        <div class="field-group hidden md:col-span-2" id="group-bio">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Bio</label>
                            <textarea name="bio" value="{{ old('bio') }}" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700 resize-none" placeholder="Freelancer bio..."></textarea>
                        </div>

                        <!-- Student specific fields - Split into 2 columns -->
                        <div class="hidden md:col-span-2" id="group-student-fields">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Informasi Siswa</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-3.5">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" id="input-student-name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="Nama Lengkap">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">NIS <span class="text-red-400">*</span></label>
                                    <input type="text" name="nis" value="{{ old('nis') }}" id="input-student-nis" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="123456789">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Kelas <span class="text-red-400">*</span></label>
                                    <input type="text" name="class" value="{{ old('class') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="XI SIJA 1">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Jurusan <span class="text-red-400">*</span></label>
<select name="major" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700 appearance-none">
    <option value="SIJA" {{ old('major') == 'SIJA' ? 'selected' : '' }}>SIJA</option>
    <option value="TJAT" {{ old('major') == 'TJAT' ? 'selected' : '' }}>TJAT</option>
</select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nomor Telepon <span class="text-red-400">*</span></label>
<input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="+62...">
                                </div>
                                <div class="md:col-span-2 mt-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email <span class="text-red-400">*</span></label>
                                    <div class="flex items-center bg-teal-50 border border-teal-200 rounded-xl overflow-hidden">
                                        <input type="text" name="email_prefix" id="input-email-prefix" class="flex-1 px-4 py-2.5 bg-white border border-slate-200 rounded-l-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="username" oninput="updateStudentEmail()">
                                        <span class="px-4 py-2.5 bg-teal-100 text-[#0f766e] font-bold text-[11px] whitespace-nowrap border-l border-teal-200">@student.smktelkom-sda.sch.id</span>
                                    </div>
                                    <p class="text-[9px] text-slate-400 mt-1.5">Email otomatis: @student.smktelkom-sda.sch.id</p>
                                    <input type="hidden" name="email" id="input-email-full">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-10">
                        <button type="button" onclick="window.closeAddModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl hover:bg-slate-200 transition-all">Cancel</button>
                        <button type="submit" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl shadow-xl shadow-teal-sm hover:bg-[#0a5e58] transition-all">Buat Akun</button>
                    </div>
                </form>
             </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__USERS_DATA__ = @json($users->items());
        window.__SKOMDA_STUDENTS__ = @json($skomdaAll);

        window.openUserModal = function(role, id) {
            const u = window.__USERS_DATA__.find(x => x.id == id && x.role == role);
            if (!u) return;

            const box = document.getElementById('modal-user-box');

            if (role === 'Client') {
                box.innerHTML = `
                    <div class="p-7">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-[1.5rem] font-black text-slate-900">Edit Klien</h2>
                            <button onclick="window.closeUserModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>
                        <form action="/admin/clients/${id}" method="POST">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4 mb-10">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                                    <input type="text" name="name" value="${u.name || ''}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Alamat Email</label>
                                    <input type="email" name="email" value="${u.email || ''}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nomor Telepon</label>
                                    <input type="text" name="phone" value="${u.phone || ''}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700">
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <button type="button" onclick="window.closeUserModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl">Batal</button>
                                <button type="submit" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl shadow-xl shadow-teal-sm hover:bg-[#0a5e58] transition-all">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                `;
            } else if (role === 'Freelancer') {
                box.innerHTML = `
                    <div class="p-7">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-[1.5rem] font-black text-slate-900">Edit Freelancer</h2>
                            <button onclick="window.closeUserModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>
                        <form action="/admin/freelancers/${id}" method="POST">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4 mb-10">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama</label>
                                    <input type="text" value="${u.name || ''}" class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-500" disabled>
                                    <p class="text-[9px] text-slate-400 mt-1">Nama diatur oleh data siswa SKOMDA</p>
                                </div>
                                <div class="md:col-start-1 md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Bio</label>
                                    <textarea name="bio" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700 resize-none" placeholder="Bio freelancer...">${u.bio || ''}</textarea>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <button type="button" onclick="window.closeUserModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl">Batal</button>
                                <button type="submit" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl shadow-xl shadow-teal-sm hover:bg-[#0a5e58] transition-all">Update Account</button>
                            </div>
                        </form>
                    </div>
                `;
            } else if (role === 'Skomda Student') {
                box.innerHTML = `
                    <div class="p-7">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-[1.5rem] font-black text-slate-900">Edit Siswa</h2>
                            <button onclick="window.closeUserModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>
                        <form action="/admin/skomda-students/${id}" method="POST">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-3.5 mb-8">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                                    <input type="text" name="name" value="${u.name || ''}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">NIS</label>
                                    <input type="text" value="${u.nis || ''}" class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-500" disabled>
                                    <p class="text-[9px] text-slate-400 mt-1">NIS tidak dapat diubah</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Kelas</label>
                                    <input type="text" value="${u.class || ''}" class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-500" disabled>
                                    <p class="text-[9px] text-slate-400 mt-1">Kelas tidak dapat diubah</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Jurusan</label>
                                    <select name="major" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700 appearance-none">
                                        <option value="SIJA" ${u.major === 'SIJA' ? 'selected' : ''}>SIJA</option>
                                        <option value="TJAT" ${u.major === 'TJAT' ? 'selected' : ''}>TJAT</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email</label>
                                    <input type="text" value="${u.email || ''}" class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-500" disabled>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Telepon</label>
                                    <input type="text" name="phone" value="${u.phone || ''}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700">
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <button type="button" onclick="window.closeUserModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl">Batal</button>
                                <button type="submit" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl shadow-xl shadow-teal-sm hover:bg-[#0a5e58] transition-all">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                `;
            }

            window.openModal('modal-user-overlay');
        };

        window.closeUserModal = () => window.closeModal('modal-user-overlay');
        window.openAddModal = function() {
            window.resetStudentCombobox?.();
            window.openModal('modal-add-overlay');
        };
        window.closeAddModal = () => window.closeModal('modal-add-overlay');

        window.openUserDetail = function(role, id) {
            const u = window.__USERS_DATA__.find(x => x.id == id && x.role == role);
            if (!u) return;

            const box = document.getElementById('modal-user-box');
            let gradientClass = 'from-[#0f766e] to-[#10b981]';
            if (role === 'Client') gradientClass = 'from-blue-600 to-indigo-600';
            if (role === 'Skomda Student') gradientClass = 'from-slate-600 to-slate-800';

            const joinedDate = new Date(u.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

            box.innerHTML = `
                <div class="relative">
                    <!-- Gradient Header -->
                    <div class="h-28 bg-gradient-to-r ${gradientClass} flex items-center px-8 relative">
                        <div class="flex-1">
                            <h2 class="text-white font-black text-xl tracking-tight">${role === 'Client' ? 'Profil Klien' : (role === 'Freelancer' ? 'Profil Freelancer' : 'Profil Siswa SKOMDA')}</h2>
                            <p class="text-white/70 text-[10px] font-bold uppercase tracking-[0.2em]">ID Pengguna: #UID-${u.id}</p>
                        </div>
                        <button onclick="window.closeUserModal()" class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center hover:bg-white/30 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>

                    <!-- Profile Info -->
                    <div class="px-8 pb-8 -mt-8 relative z-10">
                        <div class="flex items-end gap-5 mb-8">
                            <div class="w-24 h-24 rounded-[28px] bg-white p-1.5 shadow-xl">
                                ${u.profile_photo ? `
                                    <img
                                        src="{{ asset('storage') }}/${u.profile_photo}"
                                        class="w-full h-full rounded-[22px] object-cover"
                                        alt="${u.name}"
                                    >
                                ` : `
                                    <div class="w-full h-full rounded-[22px] bg-slate-100 flex items-center justify-center text-slate-400 font-black text-3xl">
                                        ${u.name ? u.name.charAt(0) : '?'}
                                    </div>
                                `}
                            </div>
                            <div class="pb-2">
                                <h3 class="text-[1.5rem] font-black text-slate-900 leading-tight">${u.name}</h3>
                                <div class="flex items-center gap-2 text-slate-400 font-bold text-[11px] uppercase tracking-widest mt-1">
                                    <i class="ri-calendar-line text-[#0f766e]"></i> Joined ${joinedDate}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 group hover:border-[#0f766e]/30 transition-all">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Alamat Email</span>
                                <span class="text-[13px] font-bold text-slate-700 block truncate">${u.email}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 group hover:border-[#0f766e]/30 transition-all">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nomor Telepon</span>
                                <span class="text-[13px] font-bold text-slate-700 block truncate">${u.phone || 'Tidak tersedia'}</span>
                            </div>
                            
                            ${role === 'Skomda Student' ? `
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 group hover:border-[#0f766e]/30 transition-all">
                                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">NIS / ID Siswa</span>
                                    <span class="text-[13px] font-bold text-slate-700 block truncate">${u.nis || '-'}</span>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 group hover:border-[#0f766e]/30 transition-all">
                                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kelas & Jurusan</span>
                                    <span class="text-[13px] font-bold text-slate-700 block truncate">${u.class || ''} ${u.major || ''}</span>
                                </div>
                            ` : ''}
                        </div>

                        ${role === 'Freelancer' && u.bio ? `
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 mb-8">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5">Bio Freelancer</span>
                                <p class="text-[13px] text-slate-600 leading-relaxed font-medium">${u.bio}</p>
                            </div>
                        ` : ''}

                        <div class="flex gap-3">
                            <button onclick="window.openPasswordModal('${role}', ${u.id})" class="flex-1 py-3.5 bg-amber-50 text-amber-600 font-bold rounded-xl text-[12px] hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="ri-lock-password-line"></i> Ubah Kata Sandi
                            </button>
                            <button onclick="window.openUserModal('${role}', ${u.id})" class="flex-1 py-3.5 bg-slate-900 text-white font-bold rounded-xl text-[12px] hover:bg-slate-800 transition-all flex items-center justify-center gap-2">
                                <i class="ri-edit-line"></i> Edit Detail
                            </button>
                        </div>
                    </div>
                </div>
            `;
            window.openModal('modal-user-overlay');
        };

        window.openPasswordModal = function(role, id) {
            const u = window.__USERS_DATA__.find(x => x.id == id && x.role == role);
            if (!u) return;

            const box = document.getElementById('modal-password-box');
            let actionUrl = '';
            let roleLabel = role;

            if (role === 'Client') {
                actionUrl = `/admin/clients/${id}/password`;
                roleLabel = 'Client';
            } else if (role === 'Freelancer') {
                actionUrl = `/admin/freelancers/${id}/password`;
                roleLabel = 'Freelancer';
            } else {
                actionUrl = `/admin/skomda-students/${id}/password`;
                roleLabel = 'Skomda Student';
            }

            box.innerHTML = `
                <div class="p-8">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-[1.5rem] font-black text-slate-900">Ubah Kata Sandi</h2>
                        <button onclick="window.closePasswordModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                    <form action="${actionUrl}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <div class="space-y-5 mb-6">
                            <div class="bg-slate-50 p-4 rounded-2xl">
                                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pengguna</p>
                                <p class="font-bold text-slate-800">${u.name || 'N/A'}</p>
                                <p class="text-[12px] text-slate-400">${u.email || ''}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Kata Sandi Baru</label>
                                <input type="password" name="password" required minlength="8" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="Minimal 8 karakter">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Konfirmasi Kata Sandi</label>
                                <input type="password" name="password_confirmation" required minlength="8" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="Masukkan ulang kata sandi baru">
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <button type="button" onclick="window.closePasswordModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl">Batal</button>
                            <button type="submit" class="flex-1 py-4 bg-amber-500 text-white font-bold rounded-2xl shadow-xl hover:bg-amber-600 transition-all">Ubah Kata Sandi</button>
                        </div>
                    </form>
                </div>
            `;
            window.openModal('modal-password-overlay');
        };

        window.closePasswordModal = () => window.closeModal('modal-password-overlay');

        const studentCombobox = document.getElementById('student-combobox');
        const studentSearchInput = document.getElementById('student-search-input');
        const studentIdInput = document.getElementById('student-id-input');
        const studentSearchList = document.getElementById('student-search-list');
        const skomdaStudents = (window.__SKOMDA_STUDENTS__ || []).map(student => ({
            id: String(student.id),
            name: student.name || '',
            nis: student.nis || '',
        }));

        const closeStudentList = function() {
            studentSearchList?.classList.add('hidden');
        };

        const selectStudent = function(student) {
            if (! studentSearchInput || ! studentIdInput) return;

            studentSearchInput.value = `${student.name} (${student.nis})`;
            studentIdInput.value = student.id;
            closeStudentList();
        };

        const renderStudentList = function(query = '') {
            if (! studentSearchList) return;

            const normalizedQuery = query.trim().toLowerCase();
            const matches = skomdaStudents
                .filter(student => {
                    if (! normalizedQuery) return true;

                    return student.name.toLowerCase().includes(normalizedQuery)
                        || student.nis.toLowerCase().includes(normalizedQuery);
                })
                .slice(0, 8);

            studentSearchList.innerHTML = '';

            if (matches.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-3 text-[12px] font-bold text-slate-400';
                empty.textContent = 'Siswa tidak ditemukan';
                studentSearchList.appendChild(empty);
            } else {
                matches.forEach(student => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'w-full px-4 py-3 rounded-xl text-left hover:bg-teal-50 focus:bg-teal-50 focus:outline-none transition-colors';
                    item.addEventListener('click', () => selectStudent(student));

                    const name = document.createElement('div');
                    name.className = 'text-[12.5px] font-black text-slate-800';
                    name.textContent = student.name;

                    const nis = document.createElement('div');
                    nis.className = 'text-[10px] font-bold text-slate-400 mt-0.5';
                    nis.textContent = `NIS ${student.nis}`;

                    item.append(name, nis);
                    studentSearchList.appendChild(item);
                });
            }

            studentSearchList.classList.remove('hidden');
        };

        window.resetStudentCombobox = function() {
            if (studentSearchInput) studentSearchInput.value = '';
            if (studentIdInput) studentIdInput.value = '';
            closeStudentList();
        };

        studentSearchInput?.addEventListener('focus', () => {
            if (studentSearchInput.disabled) return;
            renderStudentList(studentSearchInput.value);
        });

        studentSearchInput?.addEventListener('input', () => {
            if (studentIdInput) studentIdInput.value = '';
            renderStudentList(studentSearchInput.value);
        });

        studentSearchInput?.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closeStudentList();
                studentSearchInput.blur();
            }
        });

        document.addEventListener('click', event => {
            if (! studentCombobox?.contains(event.target)) {
                closeStudentList();
            }
        });

        window.setAddRole = function(role) {
            // Update UI
            document.querySelectorAll('.role-selector').forEach(btn => {
                btn.classList.remove('active', 'border-[#0f766e]', 'text-[#0f766e]');
                btn.classList.add('border-slate-100', 'text-slate-400');
            });

            const activeBtn = document.getElementById(role === 'Skomda Student' ? 'role-btn-Student' : `role-btn-${role}`);
            activeBtn.classList.add('active', 'border-[#0f766e]', 'text-[#0f766e]');
            activeBtn.classList.remove('border-slate-100', 'text-slate-400');

            // Update Form
            const form = document.getElementById('add-user-form');
            const roleInput = document.getElementById('add-role-input');
            roleInput.value = role;

            // Get all field groups
            const groupName = document.getElementById('group-name');
            const groupEmail = document.getElementById('group-email');
            const groupPhone = document.getElementById('group-phone');
            const groupPassword = document.getElementById('group-password');
            const groupStudent = document.getElementById('group-student');
            const groupBio = document.getElementById('group-bio');
            const groupStudentFields = document.getElementById('group-student-fields');

            const setGroupActive = function(group, isActive) {
                if (! group) return;

                group.classList.toggle('hidden', ! isActive);
                group.querySelectorAll('input, select, textarea').forEach(field => {
                    field.disabled = ! isActive;
                });
            };

            // Hidden form fields are still submitted by the browser unless disabled.
            // This modal has duplicate names across roles, so disable inactive groups.
            [groupName, groupEmail, groupPhone, groupPassword, groupStudent, groupBio, groupStudentFields].forEach(group => {
                setGroupActive(group, false);
            });

            if (role === 'Client') {
                form.action = "{{ route('admin.clients.store') }}";
                window.resetStudentCombobox?.();
                setGroupActive(groupName, true);
                setGroupActive(groupEmail, true);
                setGroupActive(groupPhone, true);
                setGroupActive(groupPassword, true);
            } else if (role === 'Freelancer') {
                form.action = "{{ route('admin.freelancers.store') }}";
                window.resetStudentCombobox?.();
                setGroupActive(groupPassword, true);
                setGroupActive(groupStudent, true);
                setGroupActive(groupBio, true);
            } else if (role === 'Skomda Student') {
                form.action = "{{ route('admin.skomda-students.store') }}";
                window.resetStudentCombobox?.();
                setGroupActive(groupStudentFields, true);
            }
        };

        // Update student email with domain
        window.updateStudentEmail = function() {
            const prefix = document.getElementById('input-email-prefix')?.value || '';
            const emailFull = document.getElementById('input-email-full');
            if (emailFull) {
                emailFull.value = prefix + '@student.smktelkom-sda.sch.id';
            }
        };

        // Delete Confirmation
        window.confirmDeleteUser = async function(id, name) {
            if (await window.customConfirm(`Yakin ingin menghapus ${name} secara permanen? Data yang berkaitan akan ikut terhapus.`)) {
                document.getElementById(`delete-user-${id}`).submit();
            }
        };

        // Initialize Role Selectors
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('btn-add-user')?.addEventListener('click', window.openAddModal);
            // Default Role: Client
            window.setAddRole('Client');
        });
    </script>
@endsection
