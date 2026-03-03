document.addEventListener('DOMContentLoaded', () => {
    // --- 1. KONFIGURASI STATE ---
    let currentMode = 'login';
    let currentRole = 'client';
    let skills = [];
    const MAX_SKILLS = 5;

    // --- 2. SELECTOR ELEMEN ---
    const get = (id) => document.getElementById(id);
    const authOverlay = get('authOverlay');
    const overlayToggle = get('overlayToggle');
    const toggleText = get('toggleText');
    const overlayTitle = get('overlayTitle');
    const overlayDesc = get('overlayDesc');
    const heroImage = get('heroImage');
    const loginPanel = get('loginPanel');
    const registerPanel = get('registerPanel');
    const loginForm = get('loginForm');
    const registerForm = get('registerForm');
    const roleToggleContainer = get('roleToggleContainer');
    const btnClient = get('btnClient'); 
    const btnFreelancer = get('btnFreelancer');
    const skillFieldWrapper = get('skillFieldWrapper'); 
    const tagsContainer = get('tagsContainer');
    const skillInput = get('skillInput');
    const tagLimitMsg = get('tagLimitMsg');

    const contentData = {
        login: {
            title: 'Jaringan Presisi untuk Solusi Expert',
            desc: 'Rasakan koneksi tanpa hambatan antara permintaan industri premium dan output kreatif elite.',
            btnText: 'Bergabung dengan Jaringan',
            img: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1000'
        },
        register: {
            title: 'Gerbang Premium Menuju Kesuksesan Global',
            desc: 'Buka akses ke proyek berskala tinggi dan komunitas pembangun digital kelas dunia.',
            btnText: 'Kembali ke Akses',
            img: 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&q=80&w=1000'
        }
    };

    // --- 3. TOGGLE UI LOGIC (Login/Register & Role) ---
    if (overlayToggle) {
        overlayToggle.addEventListener('click', (e) => {
            e.preventDefault();
            currentMode = (currentMode === 'login') ? 'register' : 'login';
            
            authOverlay.classList.toggle('register-mode');
            loginPanel.classList.toggle('active');
            loginPanel.classList.toggle('inactive');
            registerPanel.classList.toggle('active');
            registerPanel.classList.toggle('inactive');

            if (currentMode === 'register') updateRoleUI('client');

            const data = contentData[currentMode];
            [overlayTitle, overlayDesc, toggleText].forEach(el => { if(el) el.style.opacity = 0; });
            if (heroImage) heroImage.classList.add('fade-out');
            
            setTimeout(() => {
                if (overlayTitle) overlayTitle.textContent = data.title;
                if (overlayDesc) overlayDesc.textContent = data.desc;
                if (toggleText) toggleText.textContent = data.btnText;
                if (heroImage) {
                    heroImage.src = data.img;
                    heroImage.onload = () => heroImage.classList.remove('fade-out');
                }
                [overlayTitle, overlayDesc, toggleText].forEach(el => { if(el) el.style.opacity = 1; });
            }, 300);
        });
    }

    function updateRoleUI(role) {
        currentRole = role;
        if (!roleToggleContainer || !btnClient || !btnFreelancer) return;

        const isFreelancer = (role === 'freelancer');
        roleToggleContainer.classList.toggle('freelancer-active', isFreelancer);
        btnFreelancer.classList.toggle('active', isFreelancer);
        btnClient.classList.toggle('active', !isFreelancer);
        
        if (skillFieldWrapper) {
            skillFieldWrapper.style.display = isFreelancer ? 'block' : 'none';
            if (isFreelancer) {
                void skillFieldWrapper.offsetWidth; 
                skillFieldWrapper.style.opacity = '1';
                skillFieldWrapper.style.transform = 'translateY(0)';
            }
        }
    }

    if (btnClient) btnClient.addEventListener('click', () => updateRoleUI('client'));
    if (btnFreelancer) btnFreelancer.addEventListener('click', () => updateRoleUI('freelancer'));

    // --- 4. SISTEM TAGS INPUT ---
    if (tagsContainer && skillInput) {
        function renderTags() {
            tagsContainer.querySelectorAll('.tag-item').forEach(tag => tag.remove());
            skills.forEach(skill => {
                const tag = document.createElement('span');
                tag.classList.add('tag-item');
                tag.innerHTML = `${skill}<span class="tag-close" data-val="${skill}"><i class="fa-solid fa-xmark"></i></span>`;
                tagsContainer.prepend(tag);
            });
            if (tagLimitMsg) tagLimitMsg.textContent = `${skills.length}/${MAX_SKILLS} Keahlian`;
        }

        skillInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const val = skillInput.value.trim();
                if (val && skills.length < MAX_SKILLS && !skills.includes(val)) {
                    skills.push(val);
                    skillInput.value = '';
                    renderTags();
                }
            }
        });

        tagsContainer.addEventListener('click', (e) => {
            const closeBtn = e.target.closest('.tag-close');
            if (closeBtn) {
                skills = skills.filter(s => s !== closeBtn.dataset.val);
                renderTags();
            }
        });
    }

    // --- 5. INTEGRASI FETCH API (LOGIN) ---
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = loginForm.querySelector('input[type="email"]').value.trim();
            const pass = loginForm.querySelector('input[type="password"]').value.trim();

            if (!email || !pass) return alert('⚠️ Email & Password wajib diisi!');

            fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ email, password: pass })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(`✅ Login Berhasil sebagai ${data.role.toUpperCase()}`);
                    window.location.href = data.redirect;
                } else {
                    alert('❌ Gagal: ' + data.message);
                }
            })
            .catch(() => alert('⚠️ Masalah koneksi server.'));
        });
    }

    // --- 6. INTEGRASI FETCH API (REGISTER) ---
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = registerForm.querySelector('input[type="email"]').value.trim();
            const pass = registerForm.querySelector('input[type="password"]').value.trim();
            const name = registerForm.querySelector('input[name="name"]')?.value || "User";

            if (currentRole === 'freelancer' && skills.length === 0) {
                return alert('⚠️ Freelancer wajib punya minimal 1 keahlian!');
            }

            fetch('/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    name, email, password: pass, role: currentRole, skills: skills
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(`✅ Akun ${currentRole} berhasil dibuat!`);
                    overlayToggle.click();
                } else {
                    alert('❌ Gagal: ' + data.message);
                }
            })
            .catch(() => alert('⚠️ Gagal menghubungi server.'));
        });
    }
});