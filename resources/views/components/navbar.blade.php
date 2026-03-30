    <nav class="navbar sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-teal-700/10 py-4" id="navbar">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <a href="{{ route('home') }}"
                class="flex items-center gap-2.5 font-display text-2xl font-bold text-primary no-underline">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="url(#lg1)" />
                    <path d="M16 8L24 12V20L16 24L8 20V12L16 8Z" fill="white" />
                    <defs>
                        <linearGradient id="lg1" x1="0" y1="0" x2="32" y2="32">
                            <stop offset="0%" stop-color="#0F766E" />
                            <stop offset="100%" stop-color="#10B981" />
                        </linearGradient>
                    </defs>
                </svg>
                Digitalance
            </a>

            <div class="hidden md:flex items-center gap-12">
                <ul class="flex list-none gap-8 m-0 p-0">
                    <li>
                        <a href="{{ route('home') }}#home"
                            class="nav-link text-slate-800 no-underline font-semibold text-sm hover:text-primary transition-colors">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#services"
                            class="nav-link text-slate-800 no-underline font-semibold text-sm hover:text-primary transition-colors">Services</a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#faq"
                            class="nav-link text-slate-800 no-underline font-semibold text-sm hover:text-primary transition-colors">FAQ</a>
                    </li>
                </ul>
                <button onclick="location.href = 'login'"
                    class="gradient-bg text-white border-none px-6 py-3 rounded-full font-semibold cursor-pointer text-sm shadow-[0_4px_14px_rgba(15,118,110,.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(15,118,110,.4)] transition-all">
                    Get Started
                </button>
            </div>

            <button class="hamburger md:hidden flex flex-col gap-[5px] bg-transparent border-none cursor-pointer p-2"
                id="hamburgerBtn" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>

        <div class="mobile-menu flex" id="mobileMenu">
            <ul class="flex flex-col list-none gap-3 text-center p-0 m-0">
                <li>
                    <a href="#home"
                        class="text-slate-800 no-underline font-semibold block py-1 hover:text-primary transition-colors">Home</a>
                </li>
                <li>
                    <a href="#services"
                        class="text-slate-800 no-underline font-semibold block py-1 hover:text-primary transition-colors">Services</a>
                </li>
                <li>
                    <a href="#faq"
                        class="text-slate-800 no-underline font-semibold block py-1 hover:text-primary transition-colors">FAQ</a>
                </li>
            </ul>
            <button onclick="location.href = 'login'"
                class="gradient-bg text-white border-none px-6 py-3 rounded-full font-semibold cursor-pointer">
                Get Started
            </button>
        </div>
    </nav>
