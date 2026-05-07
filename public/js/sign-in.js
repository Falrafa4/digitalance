document.addEventListener("DOMContentLoaded", () => {
    // STATE
    let currentMode = "login";
    let currentRole = "client";
    let skills = [];
    const MAX_SKILLS = 5;

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
});