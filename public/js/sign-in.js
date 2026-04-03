document.addEventListener('DOMContentLoaded', () => {

  console.log('🚀 sign-in.js berhasil di-load');

  // STATE
  let currentMode = 'login';
  let currentRole = 'client';
  let skills = [];
  const MAX_SKILLS = 5;

  // ELEMENTS
  const authOverlay     = document.getElementById('authOverlay');
  const overlayToggle   = document.getElementById('overlayToggle');
  const toggleText      = document.getElementById('toggleText');
  const overlayTitle    = document.getElementById('overlayTitle');
  const overlayDesc     = document.getElementById('overlayDesc');
  const heroImage       = document.getElementById('heroImage');
  const loginPanel      = document.getElementById('loginPanel');
  const registerPanel   = document.getElementById('registerPanel');
  const btnClient       = document.getElementById('btnClient');
  const btnFreelancer   = document.getElementById('btnFreelancer');
  const roleSlider      = document.getElementById('roleSlider');
  const clientFields    = document.getElementById('clientFields');
  const freelancerFields = document.getElementById('freelancerFields');
  const tagsContainer   = document.getElementById('tagsContainer');
  const skillInput      = document.getElementById('skillInput');
  const tagLimitMsg     = document.getElementById('tagLimitMsg');
  const hiddenSkills    = document.getElementById('hiddenSkillsInput');
  const registerForm    = document.getElementById('registerForm');

  // CONTENT
  const content = {
    login: {
      title:   'Jaringan Presisi untuk Solusi Expert',
      desc:    'Rasakan koneksi tanpa hambatan antara permintaan industri premium dan output kreatif elite.',
      btnText: 'Bergabung dengan Jaringan',
      img:     'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1000',
    },
    register: {
      title:   'Gerbang Premium Menuju Kesuksesan Global',
      desc:    'Buka akses ke proyek berskala tinggi dan komunitas pembangun digital kelas dunia.',
      btnText: 'Kembali ke Akses',
      img:     'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&q=80&w=1000',
    },
  };

  // HELPERS
  function showPanel(el) {
    el.classList.remove('panel-hidden');
    el.classList.add('panel-visible');
  }

  function hidePanel(el) {
    el.classList.remove('panel-visible');
    el.classList.add('panel-hidden');
  }

  // TOGGLE LOGIN / REGISTER
  function toggleMode() {
    currentMode = currentMode === 'login' ? 'register' : 'login';
    const isRegister = currentMode === 'register';

    authOverlay.classList.toggle('register-mode', isRegister);

    if (isRegister) {
      hidePanel(loginPanel);
      showPanel(registerPanel);
    } else {
      hidePanel(registerPanel);
      showPanel(loginPanel);
    }

    if (isRegister) updateRole('client');

    const data = content[currentMode];
    overlayTitle.style.opacity = '0';
    overlayDesc.style.opacity  = '0';
    toggleText.style.opacity   = '0';
    heroImage.classList.add('fade-out');

    setTimeout(() => {
      overlayTitle.textContent = data.title;
      overlayDesc.textContent  = data.desc;
      toggleText.textContent   = data.btnText;
      heroImage.src = data.img;
      heroImage.classList.remove('fade-out');

      overlayTitle.style.opacity = '1';
      overlayDesc.style.opacity  = '1';
      toggleText.style.opacity   = '1';
    }, 300);
  }

  // ROLE TOGGLE
  function updateRole(role) {
    currentRole = role;
    const isFreelancer = role === 'freelancer';

    roleSlider.style.transform = isFreelancer ? 'translateX(100%)' : 'translateX(0%)';

    btnClient.style.color = isFreelancer ? '#94A3B8' : '#0F766E';
    btnClient.style.fontWeight = isFreelancer ? '600' : '800';
    btnFreelancer.style.color = isFreelancer ? '#0F766E' : '#94A3B8';
    btnFreelancer.style.fontWeight = isFreelancer ? '800' : '600';

    clientFields.classList.toggle('hidden', isFreelancer);
    freelancerFields.classList.toggle('hidden', !isFreelancer);

    if (registerForm) {
      registerForm.action = isFreelancer
        ? registerForm.dataset.actionFreelancer
        : registerForm.dataset.actionClient;
    }
  }

  // EVENT LISTENERS
  overlayToggle.addEventListener('click', (e) => {
    e.preventDefault();
    toggleMode();
  });

  btnClient.addEventListener('click', () => updateRole('client'));
  btnFreelancer.addEventListener('click', () => updateRole('freelancer'));

  // SKILLS TAG
  if (tagsContainer && skillInput) {
    function renderTags() {
      tagsContainer.querySelectorAll('.tag-item').forEach(t => t.remove());

      skills.forEach(skill => {
        const tag = document.createElement('span');
        tag.className = 'tag-item inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg text-xs font-medium';
        tag.innerHTML = `${skill}<span class="tag-close cursor-pointer ml-1" data-val="${skill}"><i class="fa-solid fa-xmark text-[10px]"></i></span>`;
        tagsContainer.insertBefore(tag, skillInput);
      });

      tagLimitMsg.textContent = `${skills.length}/${MAX_SKILLS} Keahlian`;
      tagLimitMsg.style.color = skills.length >= MAX_SKILLS ? '#ef4444' : '#94A3B8';
      skillInput.placeholder = skills.length >= MAX_SKILLS ? 'Penuh' : 'Ketik lalu Enter...';
      skillInput.disabled = skills.length >= MAX_SKILLS;
      if (hiddenSkills) hiddenSkills.value = JSON.stringify(skills);
    }

    skillInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const val = skillInput.value.trim();
        if (val && skills.length < MAX_SKILLS && !skills.includes(val)) {
          skills.push(val);
          skillInput.value = '';
          renderTags();
        } else if (skills.includes(val)) {
          skillInput.value = '';
        }
      }
      if (e.key === 'Backspace' && skillInput.value === '' && skills.length > 0) {
        skills.pop();
        renderTags();
      }
    });

    tagsContainer.addEventListener('click', (e) => {
      const close = e.target.closest('.tag-close');
      if (close) {
        skills = skills.filter(s => s !== close.dataset.val);
        renderTags();
      } else {
        skillInput.focus();
      }
    });
  }

  // INIT
  updateRole('client');
  showPanel(loginPanel);
  hidePanel(registerPanel);
  authOverlay.classList.remove('register-mode');

});
