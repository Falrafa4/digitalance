@extends('layouts.dashboard')
@section('title', 'User Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/clients.css') }}">
    <style>
        .role-badge { px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider; }
        .role-Client { background: #e0f2fe; color: #075985; }
        .role-Freelancer { background: #f0fdfa; color: #0f766e; }
        .role-SkomdaStudent { background: #f1f5f9; color: #475569; }
    </style>
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">User Management</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola seluruh pengguna platform: Client, Freelancer, dan Siswa Skomda.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="ri-group-line"></i>
                </div>
                <div>
                    <div class="text-[1.2rem] font-black text-slate-900 leading-none">{{ $users->total() }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Users</div>
                </div>
            </div>
            <button id="btn-add-user" class="px-6 py-3.5 bg-[#0f766e] text-white font-black text-[13px] rounded-2xl shadow-teal-md hover:bg-[#0a5e58] transition-all flex items-center gap-2">
                <i class="ri-user-add-line"></i> Add New User
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 mb-8 flex-wrap animate-fadeUp-2">
        <div class="flex gap-2 flex-wrap">
             <a href="{{ route('admin.clients.index', ['role' => 'all']) }}" 
               class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ $role === 'all' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500 hover:border-[#0f766e]' }}">
                All Users
            </a>
            @foreach(['Client', 'Freelancer', 'Skomda Student'] as $r)
                <a href="{{ route('admin.clients.index', ['role' => $r]) }}" 
                   class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ $role === $r ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500 hover:border-[#0f766e]' }}">
                    {{ $r }}s
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.clients.index') }}" method="GET" class="relative">
            <input type="hidden" name="role" value="{{ $role }}">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[15px]"></i>
            <input type="text" name="q" value="{{ $q }}" placeholder="Search name or email..." 
                   class="pl-10 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[14px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
        </form>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-200 overflow-hidden animate-fadeUp-3 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">User Details</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Role Type</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Account Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Joined Date</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-sm group-hover:bg-[#0f766e] group-hover:text-white transition-all">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[13.5px] font-black text-slate-900">{{ $user->name }}</span>
                                        <span class="text-[11px] text-slate-400 font-bold tracking-tight">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="role-badge py-1 px-2.5 rounded-lg text-[10px] font-black uppercase tracking-wider role-{{ str_replace(' ', '', $user->role) }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black uppercase py-1 px-2.5 rounded-lg {{ $user->status == 'Active' || $user->status == 'Approved' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[12px] text-slate-500 font-bold uppercase tracking-widest">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="window.openUserModal('{{ $user->role }}', {{ $user->id }})" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-[#0f766e] hover:text-white transition-all">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    @if($user->role === 'Client')
                                        <form action="{{ route('admin.clients.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus client ini secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-9 h-9 rounded-xl bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-24 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-[20px] flex items-center justify-center mx-auto mb-5 text-slate-200 text-3xl">
                                    <i class="ri-user-search-line"></i>
                                </div>
                                <h3 class="text-slate-900 font-black text-lg">No Users Found</h3>
                                <p class="text-slate-400 text-sm font-medium">Tidak ada pengguna yang ditemukan dengan kriteria ini.</p>
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
    <!-- Edit User Modal (Existing) -->
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-user-overlay">
        <div class="bg-white rounded-[32px] w-full max-w-[500px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-user-box">
             <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-[1.5rem] font-black text-slate-900">Edit Profile</h2>
                    <button onclick="window.closeUserModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
                <form id="edit-user-form" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-5 mb-10">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Full Name</label>
                            <input type="text" name="name" id="user-name" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Email Address</label>
                            <input type="email" name="email" id="user-email" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Phone Number</label>
                            <input type="text" name="phone" id="user-phone" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700">
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <button type="button" onclick="window.closeUserModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl shadow-xl shadow-teal-sm hover:bg-[#0a5e58] transition-all">Update Account</button>
                    </div>
                </form>
             </div>
        </div>
    </div>

    <!-- Add User Modal (Dynamic Roles) -->
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-add-overlay">
        <div class="bg-white rounded-[32px] w-full max-w-[520px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-add-box">
             <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-[1.5rem] font-black text-slate-900">Add New User</h2>
                        <p class="text-slate-400 text-sm font-medium mt-1">Daftarkan pengguna baru ke platform.</p>
                    </div>
                    <button onclick="window.closeAddModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-8">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">Select User Role</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" onclick="window.setAddRole('Client')" id="role-btn-Client" class="role-selector py-3 rounded-2xl border-2 border-slate-100 font-black text-[11px] uppercase tracking-wider text-slate-400 hover:border-[#0f766e] hover:text-[#0f766e] transition-all active">Client</button>
                        <button type="button" onclick="window.setAddRole('Freelancer')" id="role-btn-Freelancer" class="role-selector py-3 rounded-2xl border-2 border-slate-100 font-black text-[11px] uppercase tracking-wider text-slate-400 hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Freelancer</button>
                        <button type="button" onclick="window.setAddRole('Skomda Student')" id="role-btn-Student" class="role-selector py-3 rounded-2xl border-2 border-slate-100 font-black text-[11px] uppercase tracking-wider text-slate-400 hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Student</button>
                    </div>
                </div>

                <form id="add-user-form" method="POST" action="{{ route('admin.clients.store') }}">
                    @csrf
                    <input type="hidden" name="role" id="add-role-input" value="Client">
                    
                    <div id="fields-common" class="space-y-5">
                        <!-- Client & Student Fields -->
                        <div class="field-group" id="group-name">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                            <input type="text" name="name" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="e.g. Budi Santoso">
                        </div>
                        
                        <!-- Freelancer specific: Select Student -->
                        <div class="field-group hidden" id="group-student">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Select Skomda Student</label>
                            <select name="student_id" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700 appearance-none">
                                <option value="" selected disabled>Pilih Siswa...</option>
                                @foreach($skomdaAll as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nis }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field-group" id="group-email">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Email Address</label>
                            <input type="email" name="email" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="name@example.com">
                        </div>

                        <div class="field-group" id="group-password">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Initial Password</label>
                            <input type="password" name="password" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="Min. 8 characters">
                        </div>

                        <!-- Student specific fields -->
                        <div class="grid grid-cols-2 gap-4 hidden" id="group-student-meta">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">NIS</label>
                                <input type="text" name="nis" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="123456789">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Class</label>
                                <input type="text" name="class" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="XI SIJA 1">
                            </div>
                        </div>

                        <div class="field-group hidden" id="group-major">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Major</label>
                            <select name="major" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700 appearance-none">
                                <option value="SIJA">Sistem Informatika, Jaringan & Aplikasi (SIJA)</option>
                                <option value="TJAT">Teknik Jaringan Akses Telekomunikasi (TJAT)</option>
                            </select>
                        </div>

                        <div class="field-group" id="group-phone">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Phone Number</label>
                            <input type="text" name="phone" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700" placeholder="+62...">
                        </div>
                        
                        <div class="field-group hidden" id="group-bio">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Short Bio</label>
                            <textarea name="bio" rows="2" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-[#0f766e] transition-all font-bold text-slate-700 resize-none" placeholder="Freelancer bio..."></textarea>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-10">
                        <button type="button" onclick="window.closeAddModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl hover:bg-slate-200 transition-all">Cancel</button>
                        <button type="submit" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl shadow-xl shadow-teal-sm hover:bg-[#0a5e58] transition-all">Create Account</button>
                    </div>
                </form>
             </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__USERS_DATA__ = @json($users->items());

        window.openUserModal = function(role, id) {
            const u = window.__USERS_DATA__.find(x => x.id == id && x.role == role);
            if (!u) return;

            document.getElementById('user-name').value = u.name;
            document.getElementById('user-email').value = u.email;
            document.getElementById('user-phone').value = u.phone || '';

            const form = document.getElementById('edit-user-form');
            if (role === 'Client') {
                form.action = `/admin/clients/${id}`;
            } else if (role === 'Freelancer') {
                form.action = `/admin/freelancers/${id}`;
            } else {
                form.action = `/admin/skomda-students/${id}`;
            }

            window.openModal('modal-user-overlay');
        };

        window.closeUserModal = () => window.closeModal('modal-user-overlay');
        window.openAddModal = () => window.openModal('modal-add-overlay');
        window.closeAddModal = () => window.closeModal('modal-add-overlay');

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

            // Reset visibility
            const groups = {
                name: document.getElementById('group-name'),
                email: document.getElementById('group-email'),
                phone: document.getElementById('group-phone'),
                password: document.getElementById('group-password'),
                student: document.getElementById('group-student'),
                studentMeta: document.getElementById('group-student-meta'),
                major: document.getElementById('group-major'),
                bio: document.getElementById('group-bio')
            };

            // Toggle logic
            if (role === 'Client') {
                form.action = "{{ route('admin.clients.store') }}";
                groups.name.classList.remove('hidden');
                groups.email.classList.remove('hidden');
                groups.phone.classList.remove('hidden');
                groups.password.classList.remove('hidden');
                groups.student.classList.add('hidden');
                groups.studentMeta.classList.add('hidden');
                groups.major.classList.add('hidden');
                groups.bio.classList.add('hidden');
            } else if (role === 'Freelancer') {
                form.action = "{{ route('admin.freelancers.store') }}";
                groups.name.classList.add('hidden');
                groups.email.classList.add('hidden');
                groups.phone.classList.add('hidden');
                groups.password.classList.remove('hidden');
                groups.student.classList.remove('hidden');
                groups.studentMeta.classList.add('hidden');
                groups.major.classList.add('hidden');
                groups.bio.classList.remove('hidden');
            } else if (role === 'Skomda Student') {
                form.action = "{{ route('admin.skomda-students.store') }}";
                groups.name.classList.remove('hidden');
                groups.email.classList.remove('hidden');
                groups.phone.classList.remove('hidden');
                groups.password.classList.add('hidden'); // SkomdaStudent doesn't have password in its own table usually
                groups.student.classList.add('hidden');
                groups.studentMeta.classList.remove('hidden');
                groups.major.classList.remove('hidden');
                groups.bio.classList.add('hidden');
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