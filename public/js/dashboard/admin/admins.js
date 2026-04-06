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
    const phone = card.dataset.phone || '-';
    const status = card.dataset.status || 'Active';
    const bio = card.dataset.bio || 'Tidak ada deskripsi bio.';
    const avatar = card.querySelector('img').src;

    const box = document.getElementById('admin-modal-box');

    box.innerHTML = `
        <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
            <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Detail Profil Admin</span>
            <button onclick="closeModal('admin-modal-overlay')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <div class="px-[26px] py-[22px] flex-1 overflow-y-auto">
            <div class="flex items-center gap-4 mb-6">
                <img src="${avatar}" alt="${name}" class="w-16 h-16 rounded-[16px] object-cover border-2 border-slate-100">
                <div>
                    <h2 class="font-display font-bold text-[18px] text-slate-900 leading-tight">${name}</h2>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider ${status === 'Active' ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500'}">${status}</span>
                        <span class="text-[12px] font-bold text-[#0f766e] flex items-center gap-1">
                            <i class="ri-shield-user-line"></i> Administrator
                        </span>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <div class="flex flex-col gap-1.5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kontak</span>
                    <div class="flex items-center gap-2.5 text-[13.5px] text-slate-700 font-medium">
                        <i class="ri-mail-line text-slate-400 text-[16px]"></i> ${email}
                    </div>
                    <div class="flex items-center gap-2.5 text-[13.5px] text-slate-700 font-medium">
                        <i class="ri-phone-line text-slate-400 text-[16px]"></i> ${phone}
                    </div>
                </div>
                <div class="flex flex-col gap-1.5 border-t border-slate-100 pt-5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bio</span>
                    <p class="text-[13.5px] text-slate-600 leading-relaxed">${bio}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50">
            <button onclick="openEditAdmin(${id})" class="flex-1 py-[11px] rounded-xl bg-slate-200 text-slate-700 font-bold text-[13px] hover:bg-slate-300 transition-all flex items-center justify-center gap-2">
                <i class="ri-edit-line"></i> Edit
            </button>
            <button onclick="openDeleteAdmin(${id})" class="flex-1 py-[11px] rounded-xl bg-red-50 text-red-600 font-bold text-[13px] hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                <i class="ri-delete-bin-line"></i> Hapus
            </button>
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
                <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Edit Admin</span>
                <button onclick="closeModal('modal-edit-admin')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <form action="${updateUrl}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="PUT">
                
                <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="name" required value="${card.dataset.name}" class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white transition-all" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email</label>
                            <input type="email" name="email" required value="${card.dataset.email}" class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</label>
                            <select name="status" class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white transition-all">
                                <option value="Active" ${card.dataset.status === 'Active' ? 'selected' : ''}>Active</option>
                                <option value="Inactive" ${card.dataset.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                                <option value="Suspended" ${card.dataset.status === 'Suspended' ? 'selected' : ''}>Suspended</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">No. Telepon</label>
                            <input type="text" name="phone" value="${card.dataset.phone}" class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white transition-all" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Bio</label>
                        <textarea name="bio" rows="3" class="py-2.5 px-3.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white transition-all resize-none">${card.dataset.bio}</textarea>
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

    const confirmBtn = document.getElementById('btn-confirm-delete-admin');
    
    confirmBtn.onclick = function() {
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
    };

    openModal('modal-delete-admin');
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
