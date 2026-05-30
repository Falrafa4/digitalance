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
                        class="nav-link text-slate-800 no-underline font-semibold text-sm hover:text-primary transition-colors">Beranda</a>
                </li>
                <li>
                    <a href="{{ route('home') }}#services"
                        class="nav-link text-slate-800 no-underline font-semibold text-sm hover:text-primary transition-colors">Layanan</a>
                </li>
                <li>
                    <a href="{{ route('home') }}#faq"
                        class="nav-link text-slate-800 no-underline font-semibold text-sm hover:text-primary transition-colors">Pertanyaan</a>
                </li>
            </ul>
            <a href="{{ route('login') }}"
                class="gradient-bg text-white text-center no-underline inline-block px-6 py-3 rounded-full font-semibold cursor-pointer text-sm shadow-[0_4px_14px_rgba(15,118,110,.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(15,118,110,.4)] transition-all">
                Mulai
            </a>
        </div>

        <button
            class="hamburger md:hidden flex flex-col justify-center items-center gap-[6px] bg-transparent border-none cursor-pointer w-11 h-11 p-2"
            id="hamburgerBtn" aria-label="Buka menu">
            <span class="w-6 h-0.5 bg-slate-800 rounded-full transition-all duration-300 transform" id="line1"></span>
            <span class="w-6 h-0.5 bg-slate-800 rounded-full transition-all duration-300" id="line2"></span>
            <span class="w-6 h-0.5 bg-slate-800 rounded-full transition-all duration-300 transform" id="line3"></span>
        </button>
    </div>

    <div class="mobile-menu hidden flex-col px-6 py-4 bg-white border-b border-teal-700/10" id="mobileMenu">
        <ul class="flex flex-col list-none gap-2 text-center p-0 m-0 w-full">
            <li>
                <a href="{{ route('home') }}#home"
                    class="text-slate-800 no-underline font-semibold block py-3 hover:text-primary transition-colors w-full">Beranda</a>
            </li>
            <li>
                <a href="{{ route('home') }}#services"
                    class="text-slate-800 no-underline font-semibold block py-3 hover:text-primary transition-colors w-full">Layanan</a>
            </li>
            <li>
                <a href="{{ route('home') }}#faq"
                    class="text-slate-800 no-underline font-semibold block py-3 hover:text-primary transition-colors w-full">Pertanyaan</a>
            </li>
        </ul>
        <a href="{{ route('login') }}"
            class="gradient-bg text-white text-center no-underline inline-block px-6 py-3 rounded-full font-bold cursor-pointer w-full mt-4">
            Mulai
        </a>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const line1 = document.getElementById('line1');
        const line2 = document.getElementById('line2');
        const line3 = document.getElementById('line3');

        hamburgerBtn.addEventListener('click', function () {
            // Toggle menu display
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('flex');

            // Animasi Hamburger jadi icon 'X'
            line1.classList.toggle('rotate-45');
            line1.classList.toggle('translate-y-2');
            line2.classList.toggle('opacity-0');
            line3.classList.toggle('-rotate-45');
            line3.classList.toggle('-translate-y-2');
        });
    });
</script>