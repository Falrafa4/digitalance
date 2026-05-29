(() => {
  const page = window.__CLIENTS_PAGE__ || {};
  const usersData = (page.users || []).map(u => ({ 
    ...u, 
    _uid: (u.role === 'Client' ? 'c_' : (u.role === 'Freelancer' ? 'f_' : 's_')) + u.id 
  }));

  const $ = (id) => document.getElementById(id);

  function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
  }

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

  function openModal(id) {
    const el = $(id);
    if (el) {
      el.classList.remove('opacity-0', 'pointer-events-none');
    }
  }

  function closeModal(id) {
    const el = $(id);
    if (el) {
      el.classList.add('opacity-0', 'pointer-events-none');
    }
  }

  window.openModal = openModal;
  window.closeModal = closeModal;

  // --- DETAIL ---
  window.openDetail = function(uid) {
    const u = usersData.find(x => x._uid === uid);
    if (!u) return;
    
    // Fill detail modal here if needed, or redirect to a show page
    // For now we'll just open edit since user mostly wants to manage
    window.openEdit(uid);
  };

  // --- EDIT ---
  window.openEdit = function(uid) {
    const u = usersData.find(x => x._uid === uid);
    if (!u) return;

    $('edit-uid').value = uid;
    $('edit-name').value = u.name || '';
    $('edit-email').value = u.email || '';
    $('edit-status').value = u.status || 'Active';
    $('edit-phone').value = u.phone || '';

    openModal('modal-edit');
  };

  window.submitEditUser = async function() {
    const uid = $('edit-uid')?.value;
    const u = usersData.find(x => x._uid === uid);
    if (!u) return;

    const payload = {
      name: $('edit-name').value.trim(),
      email: $('edit-email').value.trim(),
      status: $('edit-status').value,
      phone: $('edit-phone').value.trim(),
    };

    let url = `/admin/clients/${u.id}`;
    if (u.role === 'Freelancer') url = `/admin/freelancers/${u.id}`;
    if (u.role === 'Skomda Student') {
        url = `/admin/skomda-students/${u.id}`;
        // Fallback for missing fields in SkomdaStudent update
        payload.nis = u.nis || '0';
        payload.class = u.class || '-';
        payload.major = u.major || 'SIJA';
    }

    try {
      await apiRequest(url, { method: 'PUT', body: payload });
      closeModal('modal-edit');
      if (window.showToast) window.showToast('Pengguna berhasil diperbarui', 'success');
      setTimeout(() => window.location.reload(), 1000);
    } catch (err) {
      if (window.showToast) window.showToast(err.message, 'danger');
    }
  };

  // --- DELETE ---
  let deleteTargetUid = null;
  window.openDelete = function(uid) {
    const u = usersData.find(x => x._uid === uid);
    if (!u) return;
    deleteTargetUid = uid;
    $('delete-text').innerHTML = `Yakin ingin menghapus <strong>${window.DigitalanceUtils?.escapeHtml(u.name) || u.name}</strong>?`;
    openModal('modal-delete');
  };

  $('btn-confirm-delete').onclick = async function() {
    const u = usersData.find(x => x._uid === deleteTargetUid);
    if (!u) return;

    let url = `/admin/clients/${u.id}`;
    if (u.role === 'Freelancer') url = `/admin/freelancers/${u.id}`;
    if (u.role === 'Skomda Student') url = `/admin/skomda-students/${u.id}`;

    try {
      await apiRequest(url, { method: 'DELETE' });
      closeModal('modal-delete');
      if (window.showToast) window.showToast('Pengguna berhasil dihapus', 'success');
      setTimeout(() => window.location.reload(), 1000);
    } catch (err) {
      if (window.showToast) window.showToast(err.message, 'danger');
    }
  };

  // --- ADD USER ---
  window.submitAddUser = async function() {
      // Basic implementation for adding client as example
      const role = $('add-role').value;
      const payload = {
          name: $('add-name').value,
          email: $('add-email').value,
          password: $('add-password').value,
          password_confirmation: $('add-password-confirmation').value,
          role: role
      };

      let url = '/admin/clients';
      if (role === 'Freelancer') {
          url = '/admin/freelancers';
          payload.student_id = $('add-student-id').value;
          payload.status = 'Pending';
      } else if (role === 'Skomda Student') {
          url = '/admin/skomda-students';
          payload.nis = $('add-nis').value;
          payload.class = $('add-class').value;
          payload.major = $('add-major').value;
      } else {
          payload.phone = $('add-phone').value;
      }

      try {
          await apiRequest(url, { method: 'POST', body: payload });
          closeModal('modal-add');
          if (window.showToast) window.showToast('Pengguna berhasil ditambahkan', 'success');
          setTimeout(() => window.location.reload(), 1000);
      } catch (err) {
          if (window.showToast) window.showToast(err.message, 'danger');
      }
  };

  // Role picker logic
  function initAddRoles() {
    const container = $('add-roles');
    if (!container) return;
    const opts = container.querySelectorAll('.role-opt');
    opts.forEach(opt => {
        opt.onclick = () => {
            opts.forEach(o => o.classList.remove('border-[#0f766e]', 'bg-[#f0fdfa]'));
            opt.classList.add('border-[#0f766e]', 'bg-[#f0fdfa]');
            $('add-role').value = opt.dataset.val;
            
            // Toggle visibility
            $('add-client-group').style.display = opt.dataset.val === 'Client' ? '' : 'none';
            $('add-skomda-group').style.display = opt.dataset.val === 'Skomda Student' ? '' : 'none';
            $('add-freelancer-group').style.display = opt.dataset.val === 'Freelancer' ? '' : 'none';
        };
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
      initAddRoles();
      $('btn-add-user').onclick = () => openModal('modal-add');
      
      document.querySelectorAll('.overlay').forEach(ov => {
          ov.onclick = (e) => { if(e.target === ov) closeModal(ov.id); };
      });
  });
})();