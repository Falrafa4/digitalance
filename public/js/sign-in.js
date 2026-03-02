document.addEventListener('DOMContentLoaded', () => {
    // --- KONFIGURASI STATE ---
    let currentMode = 'login';
    let currentRole = 'client';
    let skills = [];
    const MAX_SKILLS = 5;

    // --- SELECTOR ELEMEN ---
    const get = (id) => document.getElementById(id);

    const authOverlay = get('authOverlay');
    const overlayToggle = get('overlayToggle');
    const toggleText = get('toggleText');
    const overlayTitle = get('overlayTitle');
    const overlayDesc = get('overlayDesc');
    const heroImage = get('heroImage');
    
    const loginPanel = get('loginPanel');
    const registerPanel = get('registerPanel');
    
    const roleToggleContainer = get('roleToggleContainer');
    const btnClient = get('btnClient'); 
    const btnFreelancer = get('btnFreelancer');
    
    // PERBAIKAN: ID disamakan dengan HTML (skillFieldWrapper)
    const skillFieldWrapper = get('skillFieldWrapper'); 
    const tagsContainer = get('tagsContainer');
    const skillInput = get('skillInput');
    const tagLimitMsg = get('tagLimitMsg');
    const hiddenSkillsInput = get('hiddenSkillsInput');

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

    // --- 1. TOGGLE LOGIN / REGISTER ---
    if (overlayToggle) {
        overlayToggle.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentMode === 'login') {
                currentMode = 'register';
                authOverlay.classList.add('register-mode');
                loginPanel.classList.remove('active');
                loginPanel.classList.add('inactive');
                registerPanel.classList.remove('inactive');
                registerPanel.classList.add('active');
                updateRoleUI('client'); // Reset ke klien saat buka register
            } else {
                currentMode = 'login';
                authOverlay.classList.remove('register-mode');
                registerPanel.classList.remove('active');
                registerPanel.classList.add('inactive');
                loginPanel.classList.remove('inactive');
                loginPanel.classList.add('active');
            }
            
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

    // --- 2. LOGIKA TOGGLE ROLE (KLIEN / FREELANCER) ---
    function updateRoleUI(role) {
        currentRole = role;
        if (!roleToggleContainer || !btnClient || !btnFreelancer) return;

        if (role === 'freelancer') {
            roleToggleContainer.classList.add('freelancer-active');
            btnFreelancer.classList.add('active');
            btnClient.classList.remove('active');
            
            // MUNCULKAN FIELD KEAHLIAN
            if (skillFieldWrapper) {
                skillFieldWrapper.style.display = 'block';
                // Trigger reflow untuk animasi
                void skillFieldWrapper.offsetWidth; 
                skillFieldWrapper.style.opacity = '1';
                skillFieldWrapper.style.transform = 'translateY(0)';
            }
        } else {
            roleToggleContainer.classList.remove('freelancer-active');
            btnClient.classList.add('active');
            btnFreelancer.classList.remove('active');
            
            // SEMBUNYIKAN FIELD KEAHLIAN
            if (skillFieldWrapper) {
                skillFieldWrapper.style.opacity = '0';
                skillFieldWrapper.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (currentRole === 'client') {
                        skillFieldWrapper.style.display = 'none';
                    }
                }, 300);
            }
        }
    }

    if (btnClient) btnClient.addEventListener('click', () => updateRoleUI('client'));
    if (btnFreelancer) btnFreelancer.addEventListener('click', () => updateRoleUI('freelancer'));

    // --- 3. SISTEM TAGS INPUT ---
    if (tagsContainer && skillInput) {
        tagsContainer.addEventListener('click', (e) => {
            if (e.target !== skillInput) skillInput.focus();
        });

        function renderTags() {
            const currentTags = tagsContainer.querySelectorAll('.tag-item');
            currentTags.forEach(tag => tag.remove());

            skills.slice().reverse().forEach(skill => {
                const tag = document.createElement('span');
                tag.classList.add('tag-item');
                tag.innerHTML = `${skill}<span class="tag-close" data-val="${skill}"><i class="fa-solid fa-xmark"></i></span>`;
                tagsContainer.prepend(tag);
            });

            if (tagLimitMsg) {
                tagLimitMsg.textContent = `${skills.length}/${MAX_SKILLS} Keahlian`;
                if (skills.length >= MAX_SKILLS) {
                    tagLimitMsg.classList.add('limit-reached');
                    skillInput.placeholder = "Penuh";
                    skillInput.disabled = true;
                } else {
                    tagLimitMsg.classList.remove('limit-reached');
                    skillInput.placeholder = "Ketik lalu Enter...";
                    skillInput.disabled = false;
                }
            }
            if (hiddenSkillsInput) hiddenSkillsInput.value = JSON.stringify(skills);
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
            if (e.key === 'Backspace' && skillInput.value === '' && skills.length > 0) {
                skills.pop();
                renderTags();
            }
        });

        tagsContainer.addEventListener('click', (e) => {
            const closeBtn = e.target.closest('.tag-close');
            if (closeBtn) {
                const valToRemove = closeBtn.dataset.val;
                skills = skills.filter(s => s !== valToRemove);
                renderTags();
            }
        });
    }

// --- 6. LOGIKA SUBMIT (DENGAN REQUIREMENT) ---
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = loginForm.querySelector('input[type="email"]').value.trim();
            const pass = loginForm.querySelector('input[type="password"]').value.trim();

            // Requirement Check
            if (!email || !pass) {
                alert('⚠️ Protokol Gagal: Email dan Password wajib diisi!');
                return;
            }

            const target = (currentRole === 'freelancer') ? './freelancer-dashboard.html' : './client-dashboard.html';
            alert(`✅ Otorisasi Berhasil! Login sebagai ${currentRole.toUpperCase()}`);
            window.location.href = target;
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const firstName = registerForm.querySelector('input[placeholder="John"]').value.trim();
            const lastName = registerForm.querySelector('input[placeholder="Doe"]').value.trim();
            const email = registerForm.querySelector('input[type="email"]').value.trim();
            const pass = registerForm.querySelector('input[type="password"]').value.trim();

            // Requirement Check
            if (!firstName || !lastName || !email || !pass) {
                alert('⚠️ Peringatan: Semua kolom identitas wajib diisi!');
                return;
            }

            // Freelancer Requirement Check
            if (currentRole === 'freelancer' && skills.length === 0) {
                alert('⚠️ Freelancer diwajibkan menyertakan minimal 1 keahlian!');
                return;
            }

            alert(`✅ Identitas ${firstName} berhasil dibuat! Silakan lakukan otorisasi masuk.`);
            overlayToggle.click(); 
        });
    }

    updateRoleUI('client');
});