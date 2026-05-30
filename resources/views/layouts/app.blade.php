<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f766e',
                        secondary: '#10b981',
                        accent: '#f97316',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Sora', 'sans-serif'],
                        display: ['Sora', 'sans-serif'],
                    },
                    boxShadow: {
                        'teal-sm': '0 4px 14px 0 rgba(15, 118, 110, 0.15)',
                        'teal-md': '0 6px 20px rgba(15, 118, 110, 0.2)',
                    }
                }
            }
        }
    </script>
    <script src="{{ asset('js/utils.js') }}" defer></script>
    <script src="{{ asset('js/dashboard/shared/footer.js') }}" defer></script>
    <script src="{{ asset('js/dashboard/shared/flash.js') }}" defer></script>
    <x-fonts />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @yield('styles')
</head>


<body class="bg-slate-100 text-slate-900 overflow-x-hidden">
    {{-- Skip to main content link for accessibility --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[9999] focus:px-4 focus:py-2 focus:bg-[#0f766e] focus:text-white focus:rounded-lg focus:font-bold focus:text-sm">
        Lewati ke konten utama
    </a>

    @yield('additional-header')

    <x-flash />

    <x-navbar />

    <main id="main-content" tabindex="-1" class="focus:outline-none">
        @yield('content')
    </main>

    <x-footer />

    <script defer>
            (() => {
                "use strict";

                const $ = (sel, ctx = document) => ctx.querySelector(sel);
                const $$ = (sel, ctx = document) => [
                    ...ctx.querySelectorAll(sel),
                ];

                <!-- MOBILE MENU -->
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

                // SMOOTH SCROLL
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

                // NAVBAR SCROLL SHADOW + ACTIVE LINK
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

                // ROLE TOGGLE
                const roleBtns = $$(".role-btn");
                const heroSearch = $("#heroSearch");
                const phMap = {
                    client: "Cari jasa: desain web, edit video…",
                    freelancer: "Cari proyek: redisein halaman utama…",
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

                // ACCORDION
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

                // HOW IT WORKS TABS
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

                const initBtn = switchBtns.find(
                    (b) => b.dataset.target === "client-flow",
                );
                if (initBtn) setSwitch(initBtn);

                // FAQ
                $$(".faq-item").forEach((item) => {
                    const q = $(".faq-question", item);
                    if (!q) return;
                    q.addEventListener("click", () =>
                        item.classList.toggle("active"),
                    );
                });

                // TYPING EFFECT
                const typingEl = $("#typingTarget");
                if (typingEl) {
                    const phrases = [
                        "Solusi Freelance",
                        "Talenta Kreatif",
                        "Proyek Digital",
                        "Karier Masa Depan",
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

                // SCROLL REVEAL
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

                // ANIMATED COUNTER
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
                    const isRupiah = el.dataset.currency === "rupiah";
                    const dur = 1500;
                    const t0 = performance.now();

                    (function step(now) {
                        const p = Math.min((now - t0) / dur, 1);
                        const v = target * (1 - Math.pow(1 - p, 3));
                        if (isRupiah && window.DigitalanceUtils?.formatRupiah) {
                            el.textContent = window.DigitalanceUtils.formatRupiah(v);
                        } else {
                            el.textContent = prefix + v.toFixed(dec) + suffix;
                        }
                        if (p < 1) requestAnimationFrame(step);
                    })(t0);
                }

                // PROGRESS RING
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

                // SUBTLE PARALLAX
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
    @yield('scripts')
</body>

</html>