document.addEventListener("DOMContentLoaded", () => {
    // STATE
    let currentMode = "login";
    let currentRole = "client";
    let skills = [];
    const MAX_SKILLS = 5;

    // VALIDATION STATE
    const validationErrors = {};

    // DATA KATEGORI DIAMBIL DARI BACKEND
    let availableCategories = [];
    if (window.serviceCategories && Array.isArray(window.serviceCategories)) {
        if (typeof window.serviceCategories[0] === "object") {
            availableCategories = window.serviceCategories
                .map((cat) => cat.name || cat.title || "")
                .filter(Boolean);
        } else {
            availableCategories = window.serviceCategories.filter(Boolean);
        }
    }

    // ELEMENTS
    const authOverlay = document.getElementById("authOverlay");
    const overlayToggle = document.getElementById("overlayToggle");
    const toggleText = document.getElementById("toggleText");
    const overlayTitle = document.getElementById("overlayTitle");
    const overlayDesc = document.getElementById("overlayDesc");
    const heroImage = document.getElementById("heroImage");

    const loginPanel = document.getElementById("loginPanel");
    const registerPanel = document.getElementById("registerPanel");

    const btnClient = document.getElementById("btnClient");
    const btnFreelancer = document.getElementById("btnFreelancer");
    const roleSlider = document.getElementById("roleSlider");

    const clientFields = document.getElementById("clientFields");
    const freelancerFields = document.getElementById("freelancerFields");

    const tagsContainer = document.getElementById("tagsContainer");
    const skillInput = document.getElementById("skillInput");
    const skillSuggestions = document.getElementById("skillSuggestions");
    const tagLimitMsg = document.getElementById("tagLimitMsg");
    const hiddenSkills = document.getElementById("hiddenSkillsInput");
    const registerForm = document.getElementById("registerForm");

    const registerSubmitBtn = registerForm?.querySelector('button[type="submit"]');

    const studentSelect = document.getElementById("studentSelect");
    const studentList = document.getElementById("studentList");
    const studentIdInput = document.getElementById("studentIdInput");
    const nisInput = document.getElementById("nisInput");
    const studentEmailInput = document.getElementById("studentEmail");
    const mobileToggles = document.querySelectorAll(".mobile-toggle");

    if (!authOverlay || !loginPanel || !registerPanel || !registerForm) return;

    const content = {
        login: {
            title: "Jaringan Presisi untuk Solusi Expert",
            desc: "Rasakan koneksi tanpa hambatan antara permintaan industri premium dan output kreatif elite.",
            btnText: "Bergabung dengan Jaringan",
            img: "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1000",
        },
        register: {
            title: "Gerbang Premium Menuju Kesuksesan Global",
            desc: "Buka akses ke proyek berskala tinggi dan komunitas pembangun digital kelas dunia.",
            btnText: "Kembali ke Akses",
            img: "https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&q=80&w=1000",
        },
    };

    // PASSWORD STRENGTH
    const calculateStrength = (password) => {
        let score = 0;
        if (!password) return 0;
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return Math.min(score, 5);
    };

    const getStrengthLabel = (score) => {
        if (score <= 1) return { label: 'Lemah', color: '#ef4444' };
        if (score <= 2) return { label: 'Cukup', color: '#f97316' };
        if (score <= 3) return { label: 'Sedang', color: '#eab308' };
        if (score <= 4) return { label: 'Kuat', color: '#22c55e' };
        return { label: 'Sangat Kuat', color: '#0f766e' };
    };

    const updatePasswordStrength = (input) => {
        const wrapper = input.closest('.relative')?.parentElement;
        const strengthBar = wrapper?.querySelector('.password-strength-bar');
        const strengthLabel = wrapper?.querySelector('.password-strength-label');
        const strengthReqs = wrapper?.querySelector('.password-requirements');

        if (!strengthBar || !strengthLabel) return;

        const password = input.value;
        const score = calculateStrength(password);
        const { label, color } = getStrengthLabel(score);
        const percentage = (score / 5) * 100;

        strengthBar.style.width = `${percentage}%`;
        strengthBar.style.backgroundColor = color;
        strengthLabel.textContent = password ? label : '';
        strengthLabel.style.color = color;

        // Update requirements checkmarks
        if (strengthReqs) {
            const reqs = strengthReqs.querySelectorAll('.req-item');
            reqs.forEach(req => {
                const reqType = req.dataset.req;
                let met = false;
                switch(reqType) {
                    case 'length': met = password.length >= 8; break;
                    case 'upper': met = /[A-Z]/.test(password); break;
                    case 'lower': met = /[a-z]/.test(password); break;
                    case 'number': met = /[0-9]/.test(password); break;
                    case 'special': met = /[^A-Za-z0-9]/.test(password); break;
                }
                req.classList.toggle('met', met);
            });
        }
    };

    // CLIENT-SIDE VALIDATION
    const validators = {
        name: (val) => {
            if (!val?.trim()) return 'Nama wajib diisi';
            if (val.trim().length < 2) return 'Nama minimal 2 karakter';
            return null;
        },
        email: (val) => {
            if (!val?.trim()) return 'Email wajib diisi';
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(val)) return 'Format email tidak valid';
            return null;
        },
        phone: (val) => {
            if (!val?.trim()) return 'Nomor telepon wajib diisi';
            const phoneRegex = /^[\d\s\-\+]{10,}$/;
            if (!phoneRegex.test(val.replace(/\s/g, ''))) return 'Nomor telepon tidak valid';
            return null;
        },
        password: (val) => {
            if (!val) return 'Password wajib diisi';
            if (val.length < 8) return 'Password minimal 8 karakter';
            return null;
        },
        student_id: (val) => {
            if (!val?.trim()) return 'Pilih siswa dari daftar';
            return null;
        }
    };

    const showFieldError = (input, message) => {
        if (!input) return;
        const wrapper = input.closest('div');
        let errorEl = wrapper?.querySelector('.field-error');

        if (!errorEl) {
            errorEl = document.createElement('p');
            errorEl.className = 'field-error text-xs text-red-600 mt-1.5 font-bold';
            wrapper?.appendChild(errorEl);
        }

        errorEl.textContent = message;
        input.classList.add('input-error');
        input.classList.remove('border-slate-200');
        input.classList.add('border-red-300');
    };

    const clearFieldError = (input) => {
        if (!input) return;
        const wrapper = input.closest('div');
        const errorEl = wrapper?.querySelector('.field-error');
        if (errorEl) errorEl.remove();
        input.classList.remove('input-error');
        input.classList.remove('border-red-300');
        input.classList.add('border-slate-200');
    };

    const validateField = (name, value) => {
        const validator = validators[name];
        if (!validator) return null;
        return validator(value);
    };

    const setupFieldValidation = (input, fieldName) => {
        if (!input) return;

        input.addEventListener('blur', () => {
            const error = validateField(fieldName, input.value);
            if (error) {
                showFieldError(input, error);
            } else {
                clearFieldError(input);
            }
        });

        input.addEventListener('input', () => {
            if (fieldName === 'password') {
                updatePasswordStrength(input);
            }
            const wrapper = input.closest('div');
            const errorEl = wrapper?.querySelector('.field-error');
            if (errorEl && input.value) {
                const error = validateField(fieldName, input.value);
                if (!error) clearFieldError(input);
            }
        });
    };

    const validateForm = (form) => {
        let isValid = true;
        const errors = {};

        const fields = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
        fields.forEach(field => {
            const name = field.name;
            if (!validators[name]) return;
            if (field.disabled || field.type === 'hidden') return;

            const error = validateField(fieldNameFromInput(field), field.value);
            if (error) {
                showFieldError(field, error);
                errors[name] = error;
                isValid = false;
            } else {
                clearFieldError(field);
            }
        });

        return { isValid, errors };
    };

    const fieldNameFromInput = (input) => {
        return input.name || input.id;
    };

    function showPanel(el) {
        el.classList.remove("panel-hidden");
        el.classList.add("panel-visible");
    }
    function hidePanel(el) {
        el.classList.remove("panel-visible");
        el.classList.add("panel-hidden");
    }
    function setDisabled(selector, disabled) {
        document.querySelectorAll(selector).forEach((el) => {
            el.disabled = disabled;
        });
    }
    function getStudents() {
        return Array.isArray(window.skomdaStudents) ? window.skomdaStudents : [];
    }
    function formatStudentLabel(student) {
        if (!student) return "";
        return `${student.name} (${student.nis})`;
    }
    function getStudentById(id) {
        return getStudents().find((s) => String(s.id) === String(id));
    }
    function getStudentFromInput() {
        if (!studentSelect) return null;
        const rawValue = (studentSelect.value || "").trim();
        if (!rawValue) return null;
        if (studentList) {
            const options = Array.from(studentList.options || []);
            const match = options.find((opt) => opt.value === rawValue);
            if (match && match.dataset && match.dataset.id) {
                return getStudentById(match.dataset.id) || null;
            }
        }
        const nisMatch = rawValue.match(/\(([^)]+)\)\s*$/);
        if (nisMatch && nisMatch[1]) {
            const byNis = getStudents().find(
                (s) => String(s.nis) === String(nisMatch[1])
            );
            if (byNis) return byNis;
        }
        const byNis = getStudents().find(
            (s) => String(s.nis) === String(rawValue)
        );
        if (byNis) return byNis;
        const byId = getStudents().find(
            (s) => String(s.id) === String(rawValue)
        );
        if (byId) return byId;
        const byName = getStudents().find(
            (s) => (s.name || "").toLowerCase() === rawValue.toLowerCase()
        );
        return byName || null;
    }
    function updateStudentUI() {
        if (!studentSelect) return;
        const selectedStudent = getStudentFromInput();
        if (studentIdInput) studentIdInput.value = selectedStudent ? selectedStudent.id : "";
        if (nisInput) nisInput.value = selectedStudent ? selectedStudent.nis : "";
        if (studentEmailInput) studentEmailInput.value = selectedStudent ? selectedStudent.email || "" : "";
    }
    function checkFormValidity() {
        if (!registerSubmitBtn) return;
        if (currentRole === "freelancer") {
            const ok = !!(studentIdInput && String(studentIdInput.value || "").trim());
            registerSubmitBtn.disabled = !ok;
            registerSubmitBtn.classList.toggle("opacity-50", !ok);
            registerSubmitBtn.classList.toggle("cursor-not-allowed", !ok);
        } else {
            registerSubmitBtn.disabled = false;
            registerSubmitBtn.classList.remove("opacity-50", "cursor-not-allowed");
        }
    }

    // PENTING: PANEL ERROR HANDLER
    function showCorrectPanelFromError() {
        if (window.panelShowMode === 'register') {
            currentMode = "register";
            authOverlay.classList.add("register-mode");
            hidePanel(loginPanel);
            showPanel(registerPanel);
            updateRole(window.oldRole || "client");
        } else if (window.panelShowMode === 'login') {
            currentMode = "login";
            authOverlay.classList.remove("register-mode");
            hidePanel(registerPanel);
            showPanel(loginPanel);
        }
    }

    function toggleMode() {
        currentMode = currentMode === "login" ? "register" : "login";
        const isRegister = currentMode === "register";
        authOverlay.classList.toggle("register-mode", isRegister);

        if (isRegister) {
            hidePanel(loginPanel);
            showPanel(registerPanel);
            updateRole("client");
        } else {
            hidePanel(registerPanel);
            showPanel(loginPanel);
        }

        const data = content[currentMode];
        overlayTitle.style.opacity = "0";
        overlayDesc.style.opacity = "0";
        toggleText.style.opacity = "0";
        heroImage.classList.add("fade-out");

        setTimeout(() => {
            overlayTitle.textContent = data.title;
            overlayDesc.textContent = data.desc;
            toggleText.textContent = data.btnText;
            heroImage.src = data.img;
            heroImage.classList.remove("fade-out");
            overlayTitle.style.opacity = "1";
            overlayDesc.style.opacity = "1";
            toggleText.style.opacity = "1";
        }, 300);
    }

    function updateRole(role) {
        currentRole = role;
        const isFreelancer = role === "freelancer";
        if (roleSlider) {
            roleSlider.style.transform = isFreelancer ? "translateX(100%)" : "translateX(0%)";
        }
        if (btnClient) {
            btnClient.style.color = isFreelancer ? "#94A3B8" : "#0F766E";
            btnClient.style.fontWeight = isFreelancer ? "600" : "800";
        }
        if (btnFreelancer) {
            btnFreelancer.style.color = isFreelancer ? "#0F766E" : "#94A3B8";
            btnFreelancer.style.fontWeight = isFreelancer ? "800" : "600";
        }
        if (clientFields) clientFields.classList.toggle("hidden", isFreelancer);
        if (freelancerFields)
            freelancerFields.classList.toggle("hidden", !isFreelancer);

        if (studentSelect) studentSelect.required = isFreelancer;
        document.querySelectorAll("#clientFields input").forEach((input) => {
            input.required = !isFreelancer;
        });
        setDisabled("#clientFields input", isFreelancer);
        if (registerForm)
            registerForm.action = isFreelancer
                ? registerForm.dataset.actionFreelancer
                : registerForm.dataset.actionClient;
        if (isFreelancer) updateStudentUI();
        if (!isFreelancer && studentEmailInput) studentEmailInput.value = "";

        checkFormValidity();
    }

    overlayToggle?.addEventListener("click", (e) => {
        e.preventDefault();
        toggleMode();
    });

    mobileToggles.forEach((toggle) => {
        toggle.addEventListener("click", (e) => {
            e.preventDefault();
            toggleMode();
        });
    });

    btnClient?.addEventListener("click", () => updateRole("client"));
    btnFreelancer?.addEventListener("click", () => updateRole("freelancer"));

    if (studentSelect && studentIdInput) {
        studentSelect.addEventListener("input", () => {
            updateStudentUI();
            checkFormValidity();
        });
        if (studentIdInput.value && !studentSelect.value) {
            const existingStudent = getStudentById(studentIdInput.value);
            if (existingStudent) {
                studentSelect.value = formatStudentLabel(existingStudent);
            }
        }

        updateStudentUI();
        checkFormValidity();
    }

    if (
        tagsContainer &&
        skillInput &&
        skillSuggestions &&
        tagLimitMsg &&
        hiddenSkills
    ) {
        function hideSuggestions() {
            skillSuggestions.innerHTML = "";
            skillSuggestions.classList.add("hidden");
        }

        function renderTags() {
            tagsContainer.querySelectorAll(".tag-item").forEach((t) => t.remove());
            skills.forEach((skill) => {
                const tag = document.createElement("span");
                tag.className = "tag-item inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 px-2 py-1 rounded border border-emerald-100 text-xs font-semibold";
                tag.innerHTML = `${skill}<span class="tag-close cursor-pointer ml-1 text-emerald-500 hover:text-emerald-900 flex items-center" data-val="${skill}">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </span>`;
                tagsContainer.insertBefore(tag, skillInput);
            });
            tagLimitMsg.textContent = `${skills.length}/${MAX_SKILLS} Keahlian`;
            tagLimitMsg.style.color = skills.length >= MAX_SKILLS ? "#ef4444" : "#94A3B8";
            skillInput.placeholder = skills.length >= MAX_SKILLS ? "Penuh" : "Ketik lalu Enter...";
            skillInput.disabled = skills.length >= MAX_SKILLS;
            hiddenSkills.value = JSON.stringify(skills);
            hideSuggestions();
        }

        function addSkill(val) {
            if (!val) return;
            if (skills.length >= MAX_SKILLS) return;
            if (skills.includes(val)) {
                hideSuggestions();
                skillInput.value = "";
                return;
            }
            skills.push(val);
            skillInput.value = "";
            renderTags();
        }

        function showSuggestions(val) {
            if (
                !val ||
                skills.length >= MAX_SKILLS ||
                availableCategories.length === 0
            ) {
                hideSuggestions();
                return;
            }

            const matches = availableCategories.filter(
                (cat) =>
                    cat.toLowerCase().includes(val.toLowerCase()) &&
                    !skills.includes(cat)
            );

            if (matches.length === 0) {
                hideSuggestions();
                return;
            }

            skillSuggestions.innerHTML = matches
                .map(
                    (match) =>
                        `<li class="px-4 py-2 hover:bg-emerald-50 cursor-pointer transition-colors" data-val="${match}">${match}</li>`
                )
                .join("");

            skillSuggestions.classList.remove("hidden");
        }

        skillInput.addEventListener("input", (e) => {
            showSuggestions(e.target.value.trim());
        });

        skillInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === ",") {
                e.preventDefault();
                addSkill(skillInput.value.trim());
            }
            if (
                e.key === "Backspace" &&
                skillInput.value === "" &&
                skills.length > 0
            ) {
                skills.pop();
                renderTags();
            }
        });

        skillSuggestions.addEventListener("click", (e) => {
            if (e.target.tagName.toLowerCase() === "li") {
                addSkill(e.target.dataset.val);
                skillInput.focus();
            }
        });

        tagsContainer.addEventListener("click", (e) => {
            const close = e.target.closest(".tag-close");
            if (close) {
                skills = skills.filter((s) => s !== close.dataset.val);
                renderTags();
            } else {
                skillInput.focus();
            }
        });

        document.addEventListener("click", (e) => {
            if (
                !tagsContainer.contains(e.target) &&
                !skillSuggestions.contains(e.target)
            ) {
                hideSuggestions();
            }
        });
    }

    // LOGIN FORM LOADING STATE
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const loginSubmitBtn = loginForm.querySelector('button[type="submit"]');
        loginForm.addEventListener('submit', function() {
            if (loginSubmitBtn) {
                loginSubmitBtn.disabled = true;
                loginSubmitBtn.innerHTML = '<svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> Memproses...';
                loginSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            }
        });
    }

    // REGISTER FORM LOADING STATE
    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            if (registerSubmitBtn) {
                registerSubmitBtn.disabled = true;
                registerSubmitBtn.innerHTML = '<svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> Memproses...';
                registerSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            }
        });
    }

    // INIT panel logic
    let initMode = "login";
    let initRole = "client";
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get('mode') === 'register') initMode = "register";
    if (urlParams.get('role')) initRole = urlParams.get('role');
    if (window.hasRegistrationErrors) {
        initMode = "register";
        if (window.oldRole) initRole = window.oldRole;
    }
        updateRole(initRole);

    if(window.panelShowMode){
        showCorrectPanelFromError();
    } else {
        if (initMode === "register") {
            currentMode = "register";
            authOverlay.classList.add("register-mode");
            hidePanel(loginPanel);
            showPanel(registerPanel);
            const data = content["register"];
            overlayTitle.textContent = data.title;
            overlayDesc.textContent = data.desc;
            toggleText.textContent = data.btnText;
            heroImage.src = data.img;
        } else {
            currentMode = "login";
            authOverlay.classList.remove("register-mode");
            hidePanel(registerPanel);
            showPanel(loginPanel);
        }
    }

    // SETUP PASSWORD STRENGTH & VALIDATION
    const registerPasswordField = document.getElementById('registerPasswordField');
    if (registerPasswordField) {
        registerPasswordField.addEventListener('input', () => {
            updatePasswordStrength(registerPasswordField);
        });
        registerPasswordField.addEventListener('blur', () => {
            const error = validateField('password', registerPasswordField.value);
            if (error) {
                showFieldError(registerPasswordField, error);
            }
        });
    }

    // Setup field validations
    const setupValidations = () => {
        const nameInput = document.querySelector('#clientFields input[name="name"]');
        const emailInput = document.querySelector('#clientFields input[name="email"]');
        const phoneInput = document.querySelector('#clientFields input[name="phone"]');
        const studentSelect = document.getElementById('studentSelect');

        setupFieldValidation(nameInput, 'name');
        setupFieldValidation(emailInput, 'email');
        setupFieldValidation(phoneInput, 'phone');
        setupFieldValidation(studentSelect, 'student_id');
    };

    setupValidations();

    // FORM SUBMISSION VALIDATION
    registerForm?.addEventListener('submit', (e) => {
        if (currentRole === 'client') {
            const nameInput = document.querySelector('#clientFields input[name="name"]');
            const emailInput = document.querySelector('#clientFields input[name="email"]');
            const phoneInput = document.querySelector('#clientFields input[name="phone"]');
            const passwordInput = registerPasswordField;

            let hasError = false;

            [nameInput, emailInput, phoneInput, passwordInput].forEach(input => {
                if (!input) return;
                clearFieldError(input);
                const error = validateField(input.name, input.value);
                if (error) {
                    showFieldError(input, error);
                    hasError = true;
                }
            });

            if (hasError) {
                e.preventDefault();
                registerForm.querySelector('button[type="submit"]')?.classList.remove('btn-loading');
                registerForm.querySelector('button[type="submit"]')?.removeAttribute('disabled');
            }
        } else {
            const studentIdInput = document.getElementById('studentIdInput');
            if (!studentIdInput?.value) {
                e.preventDefault();
                showFieldError(studentSelect, 'Pilih siswa dari daftar');
                registerForm.querySelector('button[type="submit"]')?.classList.remove('btn-loading');
                registerForm.querySelector('button[type="submit"]')?.removeAttribute('disabled');
            }
        }
    });
});