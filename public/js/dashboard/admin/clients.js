(() => {
  const page = window.__CLIENTS_PAGE__ || {};
  const clientsData = page.clientsData || {};
  const freelancersData = page.freelancersData || {};
  const skomdaData = page.skomdaData || {};

  let usersData = [
    ...((clientsData?.data ?? []).map(u => ({ ...u, _uid: 'c_' + u.id, role: 'Client' }))),
    ...((freelancersData?.data ?? []).map(u => ({ ...u, _uid: 'f_' + u.id, role: 'Freelancer' }))),
    ...((skomdaData?.data ?? []).map(u => ({ ...u, _uid: 's_' + u.id, role: 'Skomda Student' }))),
  ];

  let perPage = 12;
  let currentPage = 1;

  // STATUS_BADGE
  const STATUS_BADGE = {
    Active: 'bg-emerald-100 text-emerald-800',
    Approved: 'bg-emerald-100 text-emerald-800',
    Pending: 'bg-orange-100 text-orange-800',
    Inactive: 'bg-slate-100 text-slate-600',
    Suspended: 'bg-red-100 text-red-800',
    Rejected: 'bg-red-100 text-red-800',
  };

  // STATUS_DOT
  const STATUS_DOT = {
    Active: 'bg-emerald-400',
    Approved: 'bg-emerald-400',
    Pending: 'bg-orange-400',
    Inactive: 'bg-slate-300',
    Suspended: 'bg-red-500',
    Rejected: 'bg-red-500',
  };

  // ROLE_META
  const ROLE_META = {
    'Client': { cls: 'ri-briefcase-4-fill', bg: '#eef2ff', color: '#6366f1' },
    'Freelancer': { cls: 'ri-vip-crown-fill', bg: '#fff7ed', color: '#f97316' },
    'Skomda Student': { cls: 'ri-graduation-cap-fill', bg: '#f0fdfa', color: '#0f766e' },
  };

  const $ = (id) => document.getElementById(id);

  function avatar(u) {
    return u.avatar ||
      `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name || 'U')}&background=f1f5f9&color=0f766e&size=128`;
  }
  function sBadge(s) { return STATUS_BADGE[s] || 'bg-slate-100 text-slate-600'; }
  function sDot(s) { return STATUS_DOT[s] || 'bg-slate-300'; }

  // CSRF_TOKEN
  function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) throw new Error('CSRF meta tag tidak ditemukan. Pastikan <meta name="csrf-token" ...> ada di layout.');
    return token;
  }

  // API_REQUEST
  async function apiRequest(url, { method = 'POST', body = null } = {}) {
    const headers = {
      'X-CSRF-TOKEN': getCsrfToken(),
      'Accept': 'application/json',
    };

    let payload = body;
    if (body && typeof body === 'object' && !(body instanceof FormData)) {
      headers['Content-Type'] = 'application/json';
      payload = JSON.stringify(body);
    }

    const res = await fetch(url, { method, headers, body: payload });

    let data = null;
    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      try { data = await res.json(); } catch (e) {}
    }

    if (!res.ok) throw new Error(data?.message || `Request gagal (${res.status}).`);
    return data;
  }

  function deleteUrlFor(u) {
    if (u.role === 'Client') return `/admin/clients/${u.id}`;
    if (u.role === 'Freelancer') return `/admin/freelancers/${u.id}`;
    if (u.role === 'Skomda Student') return `/admin/skomda-students/${u.id}`;
    return `/admin/clients/${u.id}`;
  }

  // TOAST
  function showToast(msg, type = 'success') {
    const icons = { success: 'ri-check-double-line', danger: 'ri-close-circle-line', warn: 'ri-alert-line' };
    const colors = {
      success: 'bg-white border-[#10b981] text-emerald-800',
      danger: 'bg-white border-red-400 text-red-800',
      warn: 'bg-white border-orange-400 text-orange-800',
    };

    const el = document.createElement('div');
    el.className =
      `toast flex items-center gap-2.5 px-[18px] py-[13px] border-[1.5px] rounded-[13px] text-[13px] font-semibold max-w-[320px] shadow-lg ${colors[type] || colors.success}`;
    el.innerHTML =
      `<i class="text-[17px] flex-shrink-0 ${icons[type] || icons.success}"></i>
       <span>${msg}</span>
       <button type="button" class="ml-auto bg-transparent border-none cursor-pointer opacity-50 hover:opacity-100 text-[15px] text-inherit p-0 leading-none">
         <i class="ri-close-line"></i>
       </button>`;

    const c = $('toast-container');
    if (!c) return;

    el.querySelector('button')?.addEventListener('click', () => dismissToast(el));
    c.appendChild(el);
    setTimeout(() => dismissToast(el), 3500);
  }
  window.showToast = showToast;

  function dismissToast(el) {
    if (!el || el.classList.contains('hide')) return;
    el.classList.add('hide');
    setTimeout(() => el.remove(), 280);
  }

  // MODAL
  function openModal(id) {
    const el = $(id);
    if (!el) return;
    el.classList.add('open');
    el.style.opacity = '1';
    el.style.pointerEvents = 'all';
  }

  function closeModal(id) {
    const el = $(id);
    if (!el) return;
    el.classList.remove('open');
    el.style.opacity = '0';
    el.style.pointerEvents = 'none';
  }

  window.openModal = openModal;
  window.closeModal = closeModal;

  document.querySelectorAll('.overlay').forEach(ov => {
    ov.addEventListener('click', e => { if (e.target === ov) closeModal(ov.id); });
  });

  // PAGINATION
  function setMeta(totalShown, totalAll) {
    const meta = $('pagination-meta');
    if (meta) {
      if (totalAll === 0) meta.textContent = `Showing 0–0 of 0`;
      else meta.textContent = `Showing 1–${totalShown} of ${totalAll}`;
    }
    const controls = $('pagination-controls');
    if (controls) controls.innerHTML = '';
  }

  function getFilteredData() {
    const active = document.querySelector('.filter-tab.active');
    const f = active ? active.dataset.filter : 'all';
    const q = ($('user-search')?.value || '').toLowerCase();

    let res = usersData;
    if (f !== 'all') res = res.filter(u => u.role === f);

    if (q) {
      res = res.filter(u =>
        (u.name && u.name.toLowerCase().includes(q)) ||
        (u.email && u.email.toLowerCase().includes(q)) ||
        (u.role && u.role.toLowerCase().includes(q)) ||
        (u.location && u.location.toLowerCase().includes(q))
      );
    }
    return res;
  }

  // RENDER_STATS
  function renderStats() {
    const total = (clientsData.total || 0) + (freelancersData.total || 0) + (skomdaData.total || 0);
    const cl = clientsData.total || 0;
    const fr = freelancersData.total || 0;
    const sk = skomdaData.total || 0;

    const el = $('stats-row');
    if (!el) return;

    el.innerHTML = [
      { icon: 'ri-group-fill', bg: '#f0fdfa', color: '#0f766e', val: total, lbl: 'Total Users' },
      { icon: 'ri-briefcase-4-fill', bg: '#eef2ff', color: '#6366f1', val: cl, lbl: 'Clients' },
      { icon: 'ri-vip-crown-fill', bg: '#fff7ed', color: '#f97316', val: fr, lbl: 'Freelancers' },
      { icon: 'ri-graduation-cap-fill', bg: '#f0fdfa', color: '#0d9488', val: sk, lbl: 'Skomda Students' },
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

  // RENDER_CARDS
  function renderCards(data) {
    const grid = $('user-grid');
    if (!grid) return;

    if (!data || !data.length) {
      grid.innerHTML = `
        <div class="col-span-full py-16 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-3xl">
          <i class="ri-user-search-line text-[3rem] text-slate-300 block mb-3"></i>
          <h3 class="font-display text-[1.1rem] font-bold text-slate-700 mb-1.5">No users found</h3>
          <p class="text-[13px] text-slate-400">Try adjusting the filter or search keyword.</p>
        </div>`;
      setMeta(0, 0);
      return;
    }

    const start = (currentPage - 1) * perPage;
    const end = start + perPage;
    const paginated = data.slice(start, end);

    setMeta(paginated.length, data.length);

    grid.innerHTML = paginated.map(u => {
      const rm = ROLE_META[u.role] || {};
      const skills = Array.isArray(u.skills) ? u.skills : [];

      return `
        <div class="user-cardx group relative bg-white border border-slate-200 rounded-[22px] p-[18px] flex flex-col transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-[0_10px_28px_rgba(2,6,23,0.08)] overflow-hidden">
          <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="flex items-center gap-3">
              <div class="relative">
                <img class="w-[54px] h-[54px] rounded-[16px] object-cover border-2 border-white shadow-sm" src="${avatar(u)}" alt="${u.name}"/>
                <span class="absolute -bottom-0.5 -right-0.5 w-[13px] h-[13px] rounded-full border-2 border-white ${sDot(u.status)}"></span>
              </div>
              <div class="min-w-0">
                <h3 class="font-display text-[1rem] font-extrabold text-slate-900 truncate leading-tight">${u.name}</h3>
                <div class="flex items-center gap-1.5 mt-1">
                  <span class="w-[22px] h-[22px] rounded-[7px] flex items-center justify-center text-[12px] flex-shrink-0"
                    style="background:${rm.bg||'#f1f5f9'};color:${rm.color||'#64748b'}">
                    <i class="${rm.cls||'ri-user-line'}"></i>
                  </span>
                  <span class="text-[12.5px] font-semibold text-slate-600">${u.role}</span>
                </div>
              </div>
            </div>

            <span class="text-[10.5px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-[.05em] ${sBadge(u.status)}">${u.status}</span>
          </div>

          <div class="text-[11.5px] text-slate-400 flex items-center gap-1 mb-3 relative z-10">
            <i class="ri-map-pin-line"></i>
            <span class="truncate">${u.location || 'Unknown Location'}</span>
          </div>

          <div class="flex flex-wrap gap-1.5 mb-3 min-h-[26px] relative z-10">
            ${skills.length
              ? skills.slice(0,3).map(s=>`<span class="chip">${s}</span>`).join('')
                + (skills.length>3 ? `<span class="chip chip-muted">+${skills.length-3}</span>` : '')
              : '<span class="text-[11px] text-slate-300 italic">No skills listed</span>'
            }
          </div>

          <div class="flex gap-2 mt-auto relative z-10">
            <button type="button" onclick="openDetail('${u._uid}')" class="flex-1 btn-soft">
              <i class="ri-eye-line"></i> View
            </button>

            <button type="button" onclick="openEdit('${u._uid}')" class="flex-1 btn-soft btn-soft-primary">
              <i class="ri-pencil-line"></i> Edit
            </button>

            <button type="button" onclick="openDelete('${u._uid}')" class="btn-soft btn-soft-danger" title="Delete">
              <i class="ri-delete-bin-line"></i>
            </button>
          </div>
        </div>
      `;
    }).join('');
  }

  function refreshGrid() {
    renderCards(getFilteredData());
  }

  // DETAIL
  function openDetail(uid) {
    const u = usersData.find(x => x._uid === uid);
    if (!u) return;

    const rm = ROLE_META[u.role] || {};
    const skills = Array.isArray(u.skills) ? u.skills : [];

    $('detail-content').innerHTML = `
      <div class="h-[110px] bg-gradient-to-r from-[#0f766e] to-[#10b981] relative flex-shrink-0">
        <button type="button" onclick="closeModal('modal-detail')"
          class="absolute top-3.5 right-3.5 w-8 h-8 bg-white/20 border-none rounded-full flex items-center justify-center text-white text-[18px] cursor-pointer hover:bg-white/30 transition-all">
          <i class="ri-close-line"></i>
        </button>
        <img class="absolute -bottom-[38px] left-[26px] w-[76px] h-[76px] rounded-[18px] object-cover border-[3px] border-white shadow-lg"
          src="${avatar(u)}" alt="${u.name}"/>
      </div>

      <div class="px-[26px] pt-[50px] pb-[24px] overflow-y-auto flex-1">
        <h2 class="font-display text-[1.35rem] font-extrabold text-slate-900 mb-2">${u.name}</h2>
        <div class="flex items-center gap-2 mb-3 flex-wrap">
          <span class="text-[10.5px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-[.04em] ${sBadge(u.status)}">${u.status}</span>
          <span class="flex items-center gap-1.5 text-[12.5px] font-bold text-slate-500">
            <span class="w-5 h-5 rounded-[5px] flex items-center justify-center text-[11px]" style="background:${rm.bg||'#f1f5f9'};color:${rm.color||'#64748b'}">
              <i class="${rm.cls||'ri-user-line'}"></i>
            </span>
            ${u.role}
          </span>
        </div>

        <p class="text-[13px] text-slate-500 leading-[1.65] mb-5">${u.bio||'Tidak ada bio yang ditulis oleh pengguna ini.'}</p>

        ${skills.length ? `
          <div>
            <div class="text-[10.5px] font-bold text-slate-400 uppercase tracking-[.1em] mb-2.5">Skills</div>
            <div class="flex flex-wrap gap-1.5 mb-4">
              ${skills.map(s=>`<span class="px-3 py-1.5 bg-[#f0fdfa] text-[#0f766e] rounded-[8px] text-[12px] font-bold">${s}</span>`).join('')}
            </div>
          </div>` : ''
        }

        <div class="flex gap-2.5 pt-4 border-t border-slate-100">
          <button type="button" onclick="closeModal('modal-detail');openEdit('${u._uid}')"
            class="flex-1 py-2.5 rounded-[11px] bg-[#0f766e] text-white font-bold text-[13px] flex items-center justify-center gap-1.5 cursor-pointer border-none shadow-teal-sm hover:bg-[#0a5e58] transition-all duration-150">
            <i class="ri-pencil-line"></i> Edit
          </button>

          <button type="button" onclick="closeModal('modal-detail');openDelete('${u._uid}')"
            class="flex-1 py-2.5 rounded-[11px] bg-red-500 text-white font-bold text-[13px] flex items-center justify-center gap-1.5 cursor-pointer border-none shadow-[0_3px_10px_rgba(239,68,68,.25)] hover:bg-red-600 transition-all duration-150">
            <i class="ri-delete-bin-line"></i> Delete
          </button>
        </div>
      </div>
    `;

    openModal('modal-detail');
  }
  window.openDetail = openDetail;

  // EDIT
  function openEdit(uid) {
    const u = usersData.find(x => x._uid === uid);
    if (!u) return;

    $('edit-uid').value = uid;
    $('edit-name').value = u.name || '';
    $('edit-email').value = u.email || '';
    $('edit-status').value = u.status || 'Active';
    $('edit-location').value = u.location || '';
    $('edit-phone').value = u.phone || '';
    $('edit-bio').value = u.bio || '';

    const sg = $('edit-skills-group');
    if (sg) sg.style.display = 'none';

    openModal('modal-edit');
  }
  window.openEdit = openEdit;

  async function submitEditUser() {
    const uid = $('edit-uid').value;
    const u = usersData.find(x => x._uid === uid);
    if (!u) return;

    if (u.role !== 'Client') {
      showToast('Edit hanya tersedia untuk Client (endpoint update role lain belum ada).', 'danger');
      return;
    }

    const payload = {
      name: $('edit-name').value.trim(),
      email: $('edit-email').value.trim(),
      phone: $('edit-phone').value.trim(),
    };

    try {
      await apiRequest(`/admin/clients/${u.id}`, { method: 'PUT', body: payload });
      closeModal('modal-edit');
      window.location.reload();
    } catch (err) {
      showToast(err?.message || 'Gagal update client.', 'danger');
    }
  }
  window.submitEditUser = submitEditUser;

  // DELETE
  let deleteTargetUid = null;

  function openDelete(uid) {
    const u = usersData.find(x => x._uid === uid);
    if (!u) return;

    deleteTargetUid = uid;
    $('delete-text').innerHTML =
      `Tindakan ini tidak dapat dibatalkan. Akun <strong>${u.name}</strong> dan semua datanya akan dihapus permanen.`;

    openModal('modal-delete');
  }
  window.openDelete = openDelete;

  async function confirmDelete() {
    if (!deleteTargetUid) return;

    const u = usersData.find(x => x._uid === deleteTargetUid);
    if (!u) return;

    try {
      await apiRequest(deleteUrlFor(u), { method: 'DELETE' });
      closeModal('modal-delete');
      window.location.reload();
    } catch (err) {
      showToast(err?.message || 'Gagal hapus user.', 'danger');
    }
  }

  // ADD_USER
  function updateAddFormByRole() {
    const role = $('add-role')?.value || 'Client';

    const clientGroup = $('add-client-group');
    const pwGroup = $('add-password-group');
    const skGroup = $('add-skomda-group');
    const frGroup = $('add-freelancer-group');

    if (clientGroup) clientGroup.style.display = 'none';
    if (pwGroup) pwGroup.style.display = 'none';
    if (skGroup) skGroup.style.display = 'none';
    if (frGroup) frGroup.style.display = 'none';

    if (role === 'Client') {
      if (clientGroup) clientGroup.style.display = '';
      if (pwGroup) pwGroup.style.display = '';
    } else if (role === 'Skomda Student') {
      if (skGroup) skGroup.style.display = '';
    } else if (role === 'Freelancer') {
      if (frGroup) frGroup.style.display = '';
      if (pwGroup) pwGroup.style.display = '';
    }
  }

  async function submitAddUser() {
    const role = $('add-role')?.value || 'Client';

    const name = $('add-name')?.value.trim() || '';
    const email = $('add-email')?.value.trim() || '';
    const phone = $('add-phone')?.value.trim() || '';

    const pw = $('add-password')?.value || '';
    const pwc = $('add-password-confirmation')?.value || '';

    try {
      if (role === 'Client') {
        if (!name || !email || !phone) return showToast('Nama, email, dan phone wajib diisi.', 'danger');
        if (!pw) return showToast('Password wajib diisi.', 'danger');
        if (pw !== pwc) return showToast('Konfirmasi password tidak sama.', 'danger');

        await apiRequest('/admin/clients', {
          method: 'POST',
          body: { name, email, phone, password: pw, password_confirmation: pwc }
        });

        closeModal('modal-add');
        window.location.reload();
        return;
      }

      if (role === 'Skomda Student') {
        const nis = $('add-nis')?.value.trim() || '';
        const cls = $('add-class')?.value.trim() || '';
        const major = $('add-major')?.value || 'SIJA';

        if (!nis || !name || !email || !cls || !major) {
          return showToast('NIS, Nama, Email, Class, Major wajib diisi.', 'danger');
        }

        await apiRequest('/admin/skomda-students', {
          method: 'POST',
          body: { nis, name, email, class: cls, major }
        });

        closeModal('modal-add');
        window.location.reload();
        return;
      }

      if (role === 'Freelancer') {
        const studentIdRaw = $('add-student-id')?.value || '';
        const student_id = parseInt(studentIdRaw, 10);

        if (!student_id) return showToast('Student ID wajib diisi untuk Freelancer.', 'danger');
        if (!pw) return showToast('Password wajib diisi.', 'danger');
        if (pw !== pwc) return showToast('Konfirmasi password tidak sama.', 'danger');

        await apiRequest('/admin/freelancers', {
          method: 'POST',
          body: {
            student_id,
            bio: $('add-bio')?.value.trim() || null,
            password: pw,
            password_confirmation: pwc,
            status: 'Pending',
          }
        });

        closeModal('modal-add');
        window.location.reload();
        return;
      }

      showToast('Role tidak dikenali.', 'danger');
    } catch (err) {
      showToast(err?.message || 'Gagal menambah user.', 'danger');
    }
  }
  window.submitAddUser = submitAddUser;

  // ADD_ROLES_PICKER
  function initAddRoles() {
    const roles = [
      { val: 'Client', label: 'Client', desc: 'Pemberi kerja / klien', icon: 'ri-briefcase-line' },
      { val: 'Freelancer', label: 'Freelancer', desc: 'Penyedia jasa independen', icon: 'ri-vip-crown-line' },
      { val: 'Skomda Student', label: 'Skomda Student', desc: 'Siswa magang / program Skomda', icon: 'ri-graduation-cap-line' },
    ];

    const container = $('add-roles');
    if (!container) return;

    container.innerHTML = roles.map((r, i) => `
      <div class="role-opt flex items-center gap-3 p-3 border-[1.5px] ${i===0?'border-[#0f766e] bg-[#f0fdfa]':'border-slate-200 bg-white'} rounded-[12px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:bg-[#f0fdfa]" data-val="${r.val}">
        <div class="w-[38px] h-[38px] rounded-[10px] bg-white shadow-sm flex items-center justify-center text-[17px] text-[#0f766e] flex-shrink-0">
          <i class="${r.icon}"></i>
        </div>
        <div class="flex-1">
          <div class="text-[13.5px] font-bold text-slate-800">${r.label}</div>
          <div class="text-[11px] text-slate-400 mt-0.5">${r.desc}</div>
        </div>
        <div class="role-check w-5 h-5 rounded-full border-2 ${i===0?'bg-[#0f766e] border-[#0f766e]':'border-slate-300'} flex items-center justify-center text-[10px] ${i===0?'text-white':'text-transparent'} flex-shrink-0 transition-all duration-150">
          <i class="ri-check-line"></i>
        </div>
      </div>
    `).join('');

    container.querySelectorAll('.role-opt').forEach((opt, idx) => {
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

        $('add-role').value = opt.dataset.val;
        updateAddFormByRole();
      });
    });
  }

  // INIT
  function init() {
    document.querySelectorAll('.filter-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.filter-tab').forEach(t => {
          t.classList.remove('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
          t.classList.add('border-slate-200', 'bg-white', 'text-slate-500');
        });
        tab.classList.add('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
        tab.classList.remove('border-slate-200', 'bg-white', 'text-slate-500');
        refreshGrid();
      });
    });

    $('user-search')?.addEventListener('input', refreshGrid);
    const perPageSelect = $('per-page');
    if (perPageSelect) {
      perPageSelect.addEventListener('change', function () {
        perPage = parseInt(this.value);
        currentPage = 1;
        refreshGrid();
      });
    }

    $('btn-add-user')?.addEventListener('click', () => {
      updateAddFormByRole();
      openModal('modal-add');
    });

    $('btn-confirm-delete')?.addEventListener('click', confirmDelete);

    initAddRoles();
    updateAddFormByRole();
    renderStats();
    refreshGrid();
  }

  document.addEventListener('DOMContentLoaded', init);
})();
