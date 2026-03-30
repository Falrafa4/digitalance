<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @yield('styles')

    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ["Sora", "sans-serif"],
                        body: ["Plus Jakarta Sans", "sans-serif"],
                    },
                    colors: {
                        primary: "#0f766e",
                        secondary: "#10b981",
                        accent: "#f97316",
                    },
                },
            },
        };
    </script>


</head>

<body class="bg-slate-100 text-slate-900 overflow-x-hidden">
    @yield('additional-header')

    <!-- NAVBAR -->
    <x-navbar />

    @yield('content')
    
    <!-- FOOTER -->
    <x-footer />

    <script>
        (() => {
            "use strict";

            const $ = (sel, ctx = document) => ctx.querySelector(sel);
            const $$ = (sel, ctx = document) => [
                ...ctx.querySelectorAll(sel),
            ];

            /* ── 1. MOBILE MENU ─────────────────────────── */
            const hamburger = $("#hamburgerBtn");
            const mobileMenu = $("#mobileMenu");

            function closeMobileMenu() {
                hamburger.classList.remove("open");
                mobileMenu.classList.remove("open");
            }

            hamburger?.addEventListener("click", () => {
                hamburger.classList.toggle("open");
                mobileMenu.classList.toggle("open");
            });

            /* ── 2. SMOOTH SCROLL ───────────────────────── */
            document.addEventListener("click", (e) => {
                const a = e.target.closest('a[href^="#"]');
                if (!a) return;
                const id = a.getAttribute("href");
                if (id === "#") return;
                const target = $(id);
                if (!target) return;
                e.preventDefault();
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
                closeMobileMenu();
            });

            /* ── 3. NAVBAR: scroll shadow + active link ─── */
            const navbar = $("#navbar");
            const sections = $$("section[id]");
            const navLinks = $$(".nav-link");

            function updateNav() {
                navbar.classList.toggle("scrolled", window.scrollY > 8);
                const mid = window.scrollY + window.innerHeight * 0.45;
                let current = "";
                sections.forEach((s) => {
                    if (s.offsetTop <= mid) current = s.id;
                });
                navLinks.forEach((link) =>
                    link.classList.toggle(
                        "active-link",
                        link.getAttribute("href") === `#${current}`,
                    ),
                );
            }
            window.addEventListener("scroll", updateNav, {
                passive: true
            });
            updateNav();

            /* ── 4. ROLE TOGGLE ─────────────────────────── */
            // Active state lives in CSS (.role-btn.active) — NOT Tailwind JIT
            const roleBtns = $$(".role-btn");
            const heroSearch = $("#heroSearch");
            const phMap = {
                client: "Cari jasa: Web Design, Video Editing…",
                freelancer: "Cari projek: Redesign Landing Page…",
            };

            roleBtns.forEach((btn) =>
                btn.addEventListener("click", () => {
                    roleBtns.forEach((b) => b.classList.remove("active"));
                    btn.classList.add("active");
                    if (heroSearch)
                        heroSearch.placeholder =
                        phMap[btn.dataset.role] ?? "";
                }),
            );

            // Search tags fill the input
            $$(".search-tag").forEach((tag) =>
                tag.addEventListener("click", () => {
                    if (heroSearch) {
                        heroSearch.value = tag.textContent
                            .trim()
                            .replace("#", "");
                        heroSearch.focus();
                    }
                }),
            );

            /* ── 5. ACCORDION ───────────────────────────── */
            $$(".accordion-item").forEach((item) => {
                const hdr = $(".accordion-header", item);
                if (!hdr) return;
                hdr.addEventListener("click", () => {
                    const was = item.classList.contains("active");
                    $$(".accordion-item").forEach((i) =>
                        i.classList.remove("active"),
                    );
                    if (!was) item.classList.add("active");
                });
            });

            /* ── 6. HOW IT WORKS TABS ───────────────────── */
            // Use el.style instead of classList for Tailwind-incompatible dynamic values
            const switchBtns = $$(".switch-btn");
            const flowContents = $$(".flow-content");

            function setSwitch(active) {
                switchBtns.forEach((b) => {
                    const on = b === active;
                    b.style.background = on ? "#10b981" : "transparent";
                    b.style.color = on ? "#fff" : "#94a3b8";
                    b.style.boxShadow = on ?
                        "0 6px 20px rgba(16,185,129,.25)" :
                        "none";
                });
            }

            switchBtns.forEach((btn) =>
                btn.addEventListener("click", () => {
                    setSwitch(btn);
                    flowContents.forEach((f) => {
                        f.classList.remove("active");
                        if (f.id === btn.dataset.target)
                            f.classList.add("active");
                    });
                }),
            );

            // Init
            const initBtn = switchBtns.find(
                (b) => b.dataset.target === "client-flow",
            );
            if (initBtn) setSwitch(initBtn);

            /* ── 7. FAQ ─────────────────────────────────── */
            $$(".faq-item").forEach((item) => {
                const q = $(".faq-question", item);
                if (!q) return;
                q.addEventListener("click", () =>
                    item.classList.toggle("active"),
                );
            });

            /* ── 8. TYPING EFFECT ───────────────────────── */
            const typingEl = $("#typingTarget");
            if (typingEl) {
                const phrases = [
                    "Freelance Solutions",
                    "Creative Talents",
                    "Digital Projects",
                    "Future Careers",
                ];
                let pi = 0,
                    ci = 0,
                    del = false;

                function tick() {
                    const cur = phrases[pi];
                    if (del) {
                        typingEl.textContent = cur.slice(0, --ci);
                        if (ci === 0) {
                            del = false;
                            pi = (pi + 1) % phrases.length;
                            setTimeout(tick, 380);
                            return;
                        }
                        setTimeout(tick, 46);
                    } else {
                        typingEl.textContent = cur.slice(0, ++ci);
                        if (ci === cur.length) {
                            del = true;
                            setTimeout(tick, 2100);
                            return;
                        }
                        setTimeout(tick, 80);
                    }
                }
                setTimeout(tick, 1000);
            }

            /* ── 9. SCROLL REVEAL ───────────────────────── */
            const revealObs = new IntersectionObserver(
                (entries) => {
                    entries.forEach((e) => {
                        if (!e.isIntersecting) return;
                        e.target.classList.add("visible");
                        revealObs.unobserve(e.target);
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: "0px 0px -50px 0px"
                },
            );

            $$(".reveal").forEach((el) => revealObs.observe(el));

            /* ── 10. ANIMATED COUNTER ───────────────────── */
            const counterObs = new IntersectionObserver(
                (entries) => {
                    entries.forEach((e) => {
                        if (!e.isIntersecting) return;
                        counterObs.unobserve(e.target);
                        runCounter(e.target);
                    });
                }, {
                    threshold: 0.6
                },
            );

            $$(".stat-number").forEach((el) => counterObs.observe(el));

            function runCounter(el) {
                const target = parseFloat(el.dataset.target);
                const suffix = el.dataset.suffix || "";
                const prefix = el.dataset.prefix || "";
                const dec = parseInt(el.dataset.decimal || "0");
                const dur = 1500;
                const t0 = performance.now();

                (function step(now) {
                    const p = Math.min((now - t0) / dur, 1);
                    const v = target * (1 - Math.pow(1 - p, 3)); // ease-out cubic
                    el.textContent = prefix + v.toFixed(dec) + suffix;
                    if (p < 1) requestAnimationFrame(step);
                })(t0);
            }

            /* ── 11. PROGRESS RING ──────────────────────── */
            const ring = $("#progressRing");
            if (ring) {
                new IntersectionObserver(
                    (entries) => {
                        entries.forEach((e) => {
                            if (!e.isIntersecting) return;
                            ring.classList.add("animated");
                        });
                    }, {
                        threshold: 0.5
                    },
                ).observe(ring);
            }

            /* ── 12. SUBTLE PARALLAX (hero blobs only) ──── */
            const blob1 = $(".blob-1");
            const blob2 = $(".blob-2");
            window.addEventListener(
                "scroll",
                () => {
                    const y = window.scrollY;
                    if (blob1)
                        blob1.style.transform = `translateY(${y * 0.06}px)`;
                    if (blob2)
                        blob2.style.transform = `translateY(${y * 0.04}px)`;
                }, {
                    passive: true
                },
            );
        })();
    </script>
</body>

</html>
