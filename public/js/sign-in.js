document.addEventListener("DOMContentLoaded", () => {
    // STATE
    let currentMode = "login";
    let currentRole = "client";
    let skills = [];
    const MAX_SKILLS = 5;

    // VALIDATION STATE
    const validationErrors = {};

    const defaultLoginPageData = {
        serviceCategories: [],
        skomdaStudents: [],
        hasRegistrationErrors: false,
        registrationErrors: {},
        oldRole: null,
        panelShowMode: "",
    };

    const loginPageDataEl = document.getElementById("loginPageData");
    let loginPageData = defaultLoginPageData;

    if (loginPageDataEl?.type === "application/json") {
        try {
            loginPageData = {
                ...defaultLoginPageData,
                ...JSON.parse(loginPageDataEl.textContent || "{}"),
            };
        } catch (error) {
            console.error("Invalid login page data JSON", error);
        }
    } else if (loginPageDataEl) {
        loginPageData = {
            serviceCategories: JSON.parse(loginPageDataEl.dataset.serviceCategories || "[]"),
            skomdaStudents: JSON.parse(loginPageDataEl.dataset.skomdaStudents || "[]"),
            hasRegistrationErrors: String(loginPageDataEl.dataset.hasRegistrationErrors || "false") === "true",
            registrationErrors: JSON.parse(loginPageDataEl.dataset.registrationErrors || "{}"),
            oldRole: loginPageDataEl.dataset.oldRole || null,
            panelShowMode: loginPageDataEl.dataset.panelShowMode || "",
        };
    }

    // DOM ELEMENTS
    const authOverlay = document.getElementById("authOverlay");
    const loginPanel = document.getElementById("loginPanel");
    const registerPanel = document.getElementById("registerPanel");
    const registerForm = document.getElementById("registerForm");
    const roleInput = document.getElementById("roleInput");
    
    const clientFields = document.getElementById("clientFields");
    const freelancerFields = document.getElementById("freelancerFields");
    
    const studentSelectBtn = document.getElementById("studentSelectBtn");
    const selectedStudentLabel = document.getElementById("selectedStudentLabel");
    const studentIdInput = document.getElementById("studentIdInput");
    const studentDropdown = document.getElementById("studentDropdown");
    const studentSearch = document.getElementById("studentSearch");
    const studentList = document.getElementById("studentList");
    
    const skillsWrapper = document.getElementById("skillsWrapper");
    const skillsContainer = document.getElementById("skillsContainer");
    const availableSkills = document.getElementById("availableSkills");
    const skillsCountText = document.getElementById("skillsCountText");

    // DYNAMIC OVERLAY CONTENT ELEMENTS
    const overlayTitle = document.getElementById("overlayTitle");
    const overlayDesc = document.getElementById("overlayDesc");
    const heroImg1 = document.getElementById("heroImg1");
    const heroImg2 = document.getElementById("heroImg2");

    // AUTO-FILL EMAIL & COPY BUTTON
    const registerEmail = document.getElementById("registerEmail");
    const copyEmailBtn = document.getElementById("copyEmailBtn");

    // PASSWORD STRENGTH INDICATION ELEMENTS (UPGRADED FOR DETAILS)
    const registerPassword = document.getElementById("registerPassword");
    const passwordStrengthWrapper = document.getElementById("passwordStrengthWrapper");
    const strengthText = document.getElementById("strengthText");
    const bar1 = document.getElementById("strengthBar1");
    const bar2 = document.getElementById("strengthBar2");
    const bar3 = document.getElementById("strengthBar3");

    // LUPA SANDI MODAL ELEMENTS
    const forgotPasswordBtn = document.getElementById("forgotPasswordBtn");
    const forgotPasswordModal = document.getElementById("forgotPasswordModal");
    const closeForgotPasswordBtn = document.getElementById("closeForgotPasswordBtn");
    const cancelForgotPasswordBtn = document.getElementById("cancelForgotPasswordBtn");

    // CUSTOM SKILL INPUT ELEMENTS
    const customSkillInput = document.getElementById("customSkillInput");
    const addCustomSkillBtn = document.getElementById("addCustomSkillBtn");

    // ==========================================================================
    // RESPONSIVE SEGMENTED SWITCH TOGGLES (REVISI DESIGN SEGMENTED SWITCH DI ATAS)
    // ==========================================================================
    const switchLoginButtons = document.querySelectorAll(".switch-to-login-btn");
    const switchRegisterButtons = document.querySelectorAll(".switch-to-register-btn");

    const updateToggleState = (mode) => {
        if (mode === "login") {
            switchLoginButtons.forEach(btn => {
                btn.className = "flex-1 py-1.5 text-xs font-bold rounded-lg transition-all text-center bg-white text-slate-900 shadow-sm";
            });
            switchRegisterButtons.forEach(btn => {
                btn.className = "flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all text-center text-slate-500 hover:text-slate-800";
            });
        } else {
            switchLoginButtons.forEach(btn => {
                btn.className = "flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all text-center text-slate-500 hover:text-slate-800";
            });
            switchRegisterButtons.forEach(btn => {
                btn.className = "flex-1 py-1.5 text-xs font-bold rounded-lg transition-all text-center bg-white text-slate-900 shadow-sm";
            });
        }
    };

    switchLoginButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            switchToLogin();
            updateToggleState("login");
        });
    });

    switchRegisterButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            switchToRegister();
            updateToggleState("register");
        });
    });

    function switchToRegister() {
        currentMode = "register";
        
        // Desktop overlay slide
        authOverlay.classList.add("register-mode");
        
        // Switch Active Panels
        loginPanel.classList.remove("active");
        loginPanel.classList.add("inactive");
        
        registerPanel.classList.remove("inactive");
        registerPanel.classList.add("active");

        // Swap Dynamic Overlay Texts
        if (overlayTitle) overlayTitle.textContent = "Mulai Langkah Profesionalmu Di Sini.";
        if (overlayDesc) overlayDesc.textContent = "Bergabunglah bersama ribuan ekosistem digital Skomda untuk menciptakan projek besar bersama.";
        
        if (heroImg1 && heroImg2) {
            heroImg1.classList.remove("active");
            heroImg1.classList.add("opacity-0");
            heroImg2.classList.add("active");
            heroImg2.classList.remove("opacity-0");
        }
        updateToggleState("register");
    }

    function switchToLogin() {
        currentMode = "login";
        
        // Desktop overlay slide
        authOverlay.classList.remove("register-mode");
        
        // Switch Active Panels
        registerPanel.classList.remove("active");
        registerPanel.classList.add("inactive");
        
        loginPanel.classList.remove("inactive");
        loginPanel.classList.add("active");

        // Swap Dynamic Overlay Texts
        if (overlayTitle) overlayTitle.textContent = "Eksplorasi Talent Terbaik Skomda di Sini.";
        if (overlayDesc) overlayDesc.textContent = "Temukan freelancer siswa berkompeten untuk menyelesaikan projek digital Anda dengan kualitas profesional.";
        
        if (heroImg1 && heroImg2) {
            heroImg2.classList.remove("active");
            heroImg2.classList.add("opacity-0");
            heroImg1.classList.add("active");
            heroImg1.classList.remove("opacity-0");
        }
        updateToggleState("login");
    }

    // ==========================================================================
    // ROLE SELECTOR & FIELD INTERACTION (CLIENT vs FREELANCER)
    // ==========================================================================
    const roleTabs = document.querySelectorAll(".role-tab");
    
    roleTabs.forEach(tab => {
        tab.addEventListener("click", () => {
            const selectedRole = tab.dataset.role;
            if (selectedRole === currentRole) return;

            currentRole = selectedRole;
            roleInput.value = selectedRole;

            // Perubahan Desain Tab Aktif
            roleTabs.forEach(t => {
                t.classList.remove("bg-white", "text-slate-900", "shadow-sm");
                t.classList.add("text-slate-500", "hover:text-slate-800");
                const svg = t.querySelector("svg");
                if (svg) svg.classList.replace("text-slate-700", "text-slate-400");
            });
            tab.classList.remove("text-slate-500", "hover:text-slate-800");
            tab.classList.add("bg-white", "text-slate-900", "shadow-sm");
            const activeSvg = tab.querySelector("svg");
            if (activeSvg) activeSvg.classList.replace("text-slate-400", "text-slate-700");

            // Toggle Tampilan Form Input Sesuai Role
            if (selectedRole === "client") {
                clientFields.classList.remove("hidden");
                freelancerFields.classList.add("hidden");
                skillsWrapper.classList.add("hidden");
                registerForm.action = "/register-client";

                // Reset field email ketika berpindah kembali ke Client
                if (registerEmail) {
                    registerEmail.value = "";
                    registerEmail.readOnly = false;
                    registerEmail.classList.remove("bg-slate-100", "cursor-not-allowed", "text-slate-500");
                    if (copyEmailBtn) copyEmailBtn.classList.add("hidden");
                }
            } else {
                clientFields.classList.add("hidden");
                freelancerFields.classList.remove("hidden");
                skillsWrapper.classList.remove("hidden");
                registerForm.action = "/register-freelancer";
                
                // Load ulang data siswa dan kategori jika belum ada
                initFreelancerRequirements();
            }
        });
    });

    // ==========================================================================
    // SISWA SKOMDA DROPDOWN LOGIC
    // ==========================================================================
    function initFreelancerRequirements() {
        renderStudentList(loginPageData.skomdaStudents);
        renderAvailableSkills(loginPageData.serviceCategories);
    }

    if (studentSelectBtn) {
        studentSelectBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            studentDropdown.classList.toggle("hidden");
            if (!studentDropdown.classList.contains("hidden")) {
                studentSearch.focus();
            }
        });
    }

    // Pencarian Siswa secara Realtime
    if (studentSearch) {
        studentSearch.addEventListener("input", (e) => {
            const query = e.target.value.toLowerCase();
            const filtered = loginPageData.skomdaStudents.filter(student => 
                (student.name && student.name.toLowerCase().includes(query)) || 
                (student.nisn && String(student.nisn).includes(query)) ||
                (student.nis && String(student.nis).includes(query))
            );
            renderStudentList(filtered);
        });
    }

    function renderStudentList(studentsList) {
        if (!studentList) return;
        studentList.innerHTML = "";

        if (studentsList.length === 0) {
            studentList.innerHTML = `<div class="p-3 text-slate-400 text-center">Siswa tidak ditemukan</div>`;
            return;
        }

        studentsList.forEach(student => {
            const item = document.createElement("div");
            item.className = "p-3 cursor-pointer rounded-xl transition-all hover:bg-slate-50 font-medium text-slate-700 flex justify-between items-center";
            
            const nisnVal = student.nisn || student.nis || 'N/A';
            
            item.innerHTML = `
                <span>${student.name}</span>
                <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-bold">NISN: ${nisnVal}</span>
            `;
            item.addEventListener("click", () => {
                selectedStudentLabel.textContent = student.name;
                selectedStudentLabel.classList.remove("text-slate-400");
                selectedStudentLabel.classList.add("text-slate-800", "font-bold");
                studentIdInput.value = student.id;
                studentDropdown.classList.add("hidden");

                // Auto-fill email dan buat read-only tapi copiable
                if (registerEmail && student.email) {
                    registerEmail.value = student.email;
                    registerEmail.readOnly = true;
                    registerEmail.classList.add("bg-slate-100", "cursor-not-allowed", "text-slate-500");
                    if (copyEmailBtn) copyEmailBtn.classList.remove("hidden");
                }
            });
            studentList.appendChild(item);
        });
    }

    // Sembunyikan dropdown siswa jika klik di luar area
    document.addEventListener("click", (e) => {
        if (studentDropdown && !studentDropdown.contains(e.target) && e.target !== studentSelectBtn) {
            studentDropdown.classList.add("hidden");
        }
    });

    // ==========================================================================
    // TOUGH FALLBACK SALIN EMAIL (REVISI FITUR SALIN YANG TIDAK BERFUNGSI)
    // ==========================================================================
    if (copyEmailBtn && registerEmail) {
        copyEmailBtn.addEventListener("click", () => {
            const emailValue = registerEmail.value;
            if (!emailValue) return;

            // Gunakan metode textarea cadangan yang dijamin bekerja dalam modul iframe/canvas
            const textarea = document.createElement("textarea");
            textarea.value = emailValue;
            textarea.style.position = "fixed";
            textarea.style.opacity = "0";
            textarea.style.left = "-9999px";
            document.body.appendChild(textarea);
            textarea.select();
            textarea.setSelectionRange(0, 99999);

            let successfulCopy = false;
            try {
                successfulCopy = document.execCommand("copy");
            } catch (err) {
                console.error("Metode execCommand gagal:", err);
            }

            document.body.removeChild(textarea);

            // Coba API Navigator sebagai backup kedua jika execCommand ditolak
            if (!successfulCopy && navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(emailValue)
                    .then(() => handleCopySuccess())
                    .catch(err => console.error("Clipboard API gagal:", err));
            } else if (successfulCopy) {
                handleCopySuccess();
            }
        });

        function handleCopySuccess() {
            const originalText = copyEmailBtn.textContent;
            copyEmailBtn.textContent = "Tersalin!";
            copyEmailBtn.classList.add("text-emerald-700");
            
            setTimeout(() => {
                copyEmailBtn.textContent = originalText;
                copyEmailBtn.classList.remove("text-emerald-700");
            }, 2000);
        }
    }

    // ==========================================================================
    // DETAIL VERIFIKASI SANDI INTERAKTIF (TANPA EMOJI)
    // ==========================================================================
    if (registerPassword) {
        registerPassword.addEventListener("input", (e) => {
            const val = e.target.value;
            if (!val) {
                passwordStrengthWrapper.classList.add("hidden");
                return;
            }
            passwordStrengthWrapper.classList.remove("hidden");

            // Evaluasi Kriteria Individual
            const metLength = val.length >= 8;
            const metCase = /[A-Z]/.test(val) && /[a-z]/.test(val);
            const metNumber = /[0-9]/.test(val);
            const metSpecial = /[^A-Za-z0-9]/.test(val);

            // Update UI Checklist secara Real-Time
            updateRequirementUI("req-length", metLength);
            updateRequirementUI("req-case", metCase);
            updateRequirementUI("req-number", metNumber);
            updateRequirementUI("req-special", metSpecial);

            // Hitung skor total yang dipenuhi (Skor 0-4)
            let score = 0;
            if (metLength) score++;
            if (metCase) score++;
            if (metNumber) score++;
            if (metSpecial) score++;

            // Reset seluruh bar kekuatan ke kondisi default
            bar1.className = "h-full w-1/3 transition-all rounded-full bg-slate-200";
            bar2.className = "h-full w-1/3 transition-all rounded-full bg-slate-200";
            bar3.className = "h-full w-1/3 transition-all rounded-full bg-slate-200";

            if (val.length < 6) {
                strengthText.textContent = "Sandi terlalu pendek (Min. 8 Karakter)";
                strengthText.className = "text-[10px] font-bold text-red-500";
                bar1.classList.add("bg-red-500");
            } else if (score <= 1) {
                strengthText.textContent = "Kekuatan Sandi: Lemah";
                strengthText.className = "text-[10px] font-bold text-red-500";
                bar1.classList.add("bg-red-500");
            } else if (score === 2 || score === 3) {
                strengthText.textContent = "Kekuatan Sandi: Sedang";
                strengthText.className = "text-[10px] font-bold text-amber-500";
                bar1.classList.add("bg-amber-500");
                bar2.classList.add("bg-amber-500");
            } else if (score === 4) {
                strengthText.textContent = "Kekuatan Sandi: Sangat Kuat (Sandi Aman)";
                strengthText.className = "text-[10px] font-bold text-emerald-500";
                bar1.classList.add("bg-emerald-500");
                bar2.classList.add("bg-emerald-500");
                bar3.classList.add("bg-emerald-500");
            }
        });
    }

    // Fungsi utilitas untuk mengupdate status visual tiap kriteria password
    function updateRequirementUI(elementId, isMet) {
        const reqEl = document.getElementById(elementId);
        if (!reqEl) return;
        const iconSpan = reqEl.querySelector(".icon");
        
        if (isMet) {
            reqEl.classList.remove("text-slate-400");
            reqEl.classList.add("text-emerald-600", "font-bold");
            if (iconSpan) {
                iconSpan.innerHTML = "✓";
                iconSpan.className = "icon text-emerald-600 font-extrabold";
            }
        } else {
            reqEl.classList.remove("text-emerald-600", "font-bold");
            reqEl.classList.add("text-slate-400");
            if (iconSpan) {
                iconSpan.innerHTML = "●";
                iconSpan.className = "icon text-slate-300";
            }
        }
    }

    // ==========================================================================
    // SKILLS SELECTION / TAGS LOGIC (Maksimal 5)
    // ==========================================================================
    function renderAvailableSkills(categories) {
        if (!availableSkills) return;
        availableSkills.innerHTML = "";

        categories.forEach(category => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = "px-2 py-1 text-[10px] font-semibold rounded-lg bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-all m-0.5 flex items-center gap-1";
            btn.innerHTML = `
                <svg width="10" height="10" class="w-2.5 h-2.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                ${category}
            `;
            
            btn.addEventListener("click", () => {
                addSkill(category);
            });
            availableSkills.appendChild(btn);
        });
    }

    function addSkill(skillName) {
        const cleanedSkillName = skillName.trim();
        if (!cleanedSkillName) return;

        // Cegah duplikasi
        if (skills.some(s => s.toLowerCase() === cleanedSkillName.toLowerCase())) {
            return;
        }

        if (skills.length >= MAX_SKILLS) {
            alert("Maksimal keahlian yang dapat dipilih adalah 5.");
            return;
        }

        skills.push(cleanedSkillName);
        renderSkillsTags();
    }

    function removeSkill(skillName) {
        skills = skills.filter(s => s !== skillName);
        renderSkillsTags();
    }

    function renderSkillsTags() {
        if (!skillsContainer) return;
        skillsContainer.innerHTML = "";

        // Hapus input hidden skill yang lama di form
        const oldHiddenInputs = registerForm.querySelectorAll("input[name='skills[]']");
        oldHiddenInputs.forEach(el => el.remove());

        // Update counter teks
        if (skillsCountText) {
            skillsCountText.textContent = `${skills.length}/${MAX_SKILLS} Terpilih`;
        }

        if (skills.length === 0) {
            skillsContainer.innerHTML = `<span class="text-[11px] text-slate-400 my-auto">Pilih keahlian di bawah...</span>`;
            return;
        }

        skills.forEach(skill => {
            // Tampilkan Tag visual
            const tag = document.createElement("div");
            tag.className = "tag-item";
            tag.innerHTML = `
                <span>${skill}</span>
                <button type="button" class="remove-tag">
                    <svg width="12" height="12" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            tag.querySelector(".remove-tag").addEventListener("click", () => {
                removeSkill(skill);
            });
            skillsContainer.appendChild(tag);

            // Sisipkan input hidden ke form register agar terkirim ke Laravel
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "skills[]";
            hiddenInput.value = skill;
            registerForm.appendChild(hiddenInput);
        });
    }

    // ==========================================================================
    // HANDLER KEAHLIAN KUSTOM MANUAL
    // ==========================================================================
    if (addCustomSkillBtn && customSkillInput) {
        const handleAddCustomSkill = () => {
            const val = customSkillInput.value.trim();
            if (val) {
                if (skills.length >= MAX_SKILLS) {
                    alert("Maksimal keahlian yang dapat dipilih adalah 5.");
                    return;
                }
                addSkill(val);
                customSkillInput.value = "";
                customSkillInput.focus();
            }
        };

        addCustomSkillBtn.addEventListener("click", handleAddCustomSkill);

        customSkillInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                handleAddCustomSkill();
            }
        });
    }

    // ==========================================================================
    // MODAL LUPA SANDI 
    // ==========================================================================
    if (forgotPasswordBtn && forgotPasswordModal) {
        forgotPasswordBtn.addEventListener("click", (e) => {
            e.preventDefault();
            forgotPasswordModal.classList.remove("hidden");
        });
    }

    const hideForgotPasswordModal = () => {
        if (forgotPasswordModal) {
            forgotPasswordModal.classList.add("hidden");
        }
    };

    if (closeForgotPasswordBtn) {
        closeForgotPasswordBtn.addEventListener("click", hideForgotPasswordModal);
    }

    if (cancelForgotPasswordBtn) {
        cancelForgotPasswordBtn.addEventListener("click", hideForgotPasswordModal);
    }

    // Tutup modal jika area background diklik
    if (forgotPasswordModal) {
        forgotPasswordModal.addEventListener("click", (e) => {
            if (e.target === forgotPasswordModal) {
                hideForgotPasswordModal();
            }
        });
    }

    // ==========================================================================
    // INITIAL LOAD / SESSION OLD VALUES
    // ==========================================================================
    if (loginPageData.panelShowMode === "register") {
        switchToRegister();
    } else {
        switchToLogin();
    }

    if (loginPageData.oldRole === "freelancer") {
        const freelancerTab = document.querySelector(".role-tab[data-role='freelancer']");
        if (freelancerTab) freelancerTab.click();
    }
});