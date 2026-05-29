// OPEN / CLOSE MODAL
function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('opacity-0', 'pointer-events-none');
    }
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add('opacity-0', 'pointer-events-none');
    }
}

// DETAIL ADMIN MODAL
function openAdminModal(id) {
    const card = document.querySelector(`[data-id="${id}"]`);
    if (!card) return;

    const name = card.dataset.name;
    const email = card.dataset.email;
    const status = card.dataset.status || 'Active';
    const avatar = card.querySelector('img').src;

    const box = document.getElementById('admin-modal-box');

    box.innerHTML = `
        <div class="relative">
            <!-- Gradient Header -->
            <div class="h-28 bg-gradient-to-r from-slate-800 to-slate-900 flex items-center px-8 relative">
                <div class="flex-1">
                    <h2 class="text-white font-black text-xl tracking-tight">Profil Administrator</h2>
                    <p class="text-white/70 text-[10px] font-bold uppercase tracking-[0.2em]">Admin ID: #ADM-${id}</p>
                </div>
                <button onclick="closeModal('admin-modal-overlay')" class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center hover:bg-white/30 transition">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>

            <!-- Profile Info -->
            <div class="px-8 pb-8 -mt-8 relative z-10">
                <div class="flex items-end gap-5 mb-8">
                    <div class="w-24 h-24 rounded-[28px] bg-white p-1.5 shadow-xl">
                        <img src="${avatar}" alt="${name}" class="w-full h-full rounded-[22px] object-cover">
                    </div>
                    <div class="pb-2">
                        <h3 class="text-[1.5rem] font-black text-slate-900 leading-tight">${name}</h3>
                        <div class="flex items-center gap-2 text-slate-400 font-bold text-[11px] uppercase tracking-widest mt-1">
                            <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider ${status === 'Active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-500'}">${status}</span>
                            <span class="flex items-center gap-1.5 text-[#0f766e]">
                                <i class="ri-shield-user-line"></i> Administrator
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 mb-8">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 group hover:border-[#0f766e]/30 transition-all">
                        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Email Address</span>
                        <div class="flex items-center gap-2.5">
                            <i class="ri-mail-line text-slate-400"></i>
                            <span class="text-[13px] font-bold text-slate-700">${email}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button onclick="openPasswordModal(${id})" class="flex-1 py-3.5 bg-amber-50 text-amber-600 font-bold rounded-xl text-[12px] hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center gap-2">
                        <i class="ri-lock-password-line"></i> Kata Sandi
                    </button>
                    <button onclick="openEditAdmin(${id})" class="flex-1 py-3.5 bg-slate-100 text-slate-700 font-bold rounded-xl text-[12px] hover:bg-slate-200 transition-all flex items-center justify-center gap-2">
                        <i class="ri-edit-line"></i> Sunting
                    </button>
                    <button onclick="openDeleteAdmin(${id})" class="flex-1 py-3.5 bg-red-50 text-red-600 font-bold rounded-xl text-[12px] hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-2">
                        <i class="ri-delete-bin-line"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    `;

    openModal('admin-modal-overlay');
}

// EDIT ADMIN MODAL
function openEditAdmin(id) {
    closeModal('admin-modal-overlay');
    const card = document.querySelector(`[data-id="${id}"]`);
    if (!card) return;

    const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
    const updateUrl = `/admin/admins/${id}`;

    const editBox = document.getElementById('modal-edit-admin');
    if (!editBox) return;

    editBox.innerHTML = `
        <div class="modal-box bg-white rounded-3xl w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
                <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Sunting Admin</span>
                <button onclick="closeModal('modal-edit-admin')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <form action="${updateUrl}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="PUT">

                <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="name" required value="${card.dataset.name}" class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white transition-all" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email</label>
                            <input type="email" name="email" required value="${card.dataset.email}" class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white transition-all" />
                        </div>
                    </div>
                </div>

                <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50">
                    <button type="button" onclick="closeModal('modal-edit-admin')" class="flex-1 py-3 rounded-xl bg-slate-200 text-slate-600 font-bold text-[13px] hover:bg-slate-300 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-[#0f766e] text-white font-bold text-[13px] shadow-teal-sm hover:bg-[#0a5e58] transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    `;

    openModal('modal-edit-admin');
}

// DELETE ADMIN MODAL
function openDeleteAdmin(id) {
    closeModal('admin-modal-overlay');
    const card = document.querySelector(`[data-id="${id}"]`);
    if (!card) return;

    window.customConfirm(`Yakin ingin menghapus admin "${card.dataset.name}" secara permanen?`).then(confirmed => {
        if (!confirmed) return;
        
        const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/admins/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    });
}

// CHANGE PASSWORD MODAL
function openPasswordModal(id) {
    const card = document.querySelector(`[data-id="${id}"]`);
    if (!card) return;

    const name = card.dataset.name;
    const email = card.dataset.email;
    const csrfToken = document.querySelector('input[name="_token"]')?.value || '';

    const content = document.getElementById('password-modal-content');
    content.innerHTML = `
        <form action="/admin/admins/${id}/password" method="POST" class="flex flex-col flex-1 overflow-hidden">
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="PUT">
            <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
                    <div class="bg-slate-50 p-4 rounded-2xl mb-5">
                    <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pengguna</p>
                    <p class="font-bold text-slate-800">${name}</p>
                    <p class="text-[12px] text-slate-400">${email}</p>
                </div>
                    <div class="space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kata Sandi Baru</label>
                            <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter"
                                class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white transition-all" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" required minlength="8" placeholder="Masukkan ulang kata sandi baru"
                                class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white transition-all" />
                        </div>
                </div>
            </div>
            <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modal-password-admin')" class="flex-1 py-3 rounded-xl bg-slate-200 text-slate-600 font-bold text-[13px] hover:bg-slate-300 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-amber-500 text-white font-bold text-[13px] hover:bg-amber-600 transition-all">Ubah Kata Sandi</button>
            </div>
        </form>
    `;

    openModal('modal-password-admin');
}

// FILTER & SEARCH
function applyFilterAndSearch() {
    const filter = document.querySelector('.filter-tab.active')?.dataset.filter ?? 'all';
    const q = (document.getElementById('user-search')?.value || '').toLowerCase();

    const cards = document.querySelectorAll('.user-card-item');

    cards.forEach(card => {
        const name = (card.dataset.name || '').toLowerCase();
        const email = (card.dataset.email || '').toLowerCase();
        const status = card.dataset.status || 'Active';

        let show = true;

        if (filter !== 'all' && status !== filter) {
            show = false;
        }

        if (q && !(name.includes(q) || email.includes(q))) {
            show = false;
        }

        card.style.display = show ? '' : 'none';
    });
}

// INITIALIZATION
function initPage() {
    const tabs = document.querySelectorAll('.filter-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]'));
            tabs.forEach(t => t.classList.add('bg-white', 'text-slate-500', 'border-slate-200'));
            
            tab.classList.remove('bg-white', 'text-slate-500', 'border-slate-200');
            tab.classList.add('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]');
            
            applyFilterAndSearch();
        });
    });

    const searchInput = document.getElementById('user-search');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilterAndSearch);
    }

    const btnAdd = document.getElementById('btn-add-admin');
    if (btnAdd) {
        btnAdd.addEventListener('click', () => openModal('modalCreate'));
    }

    const btnCloseAdd = document.getElementById('btn-close-add-admin');
    const btnCancelAdd = document.getElementById('btn-cancel-add-admin');
    
    if (btnCloseAdd) btnCloseAdd.addEventListener('click', () => closeModal('modalCreate'));
    if (btnCancelAdd) btnCancelAdd.addEventListener('click', () => closeModal('modalCreate'));

    document.querySelectorAll('.overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.add('opacity-0', 'pointer-events-none');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initPage);
