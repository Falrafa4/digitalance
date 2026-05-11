<footer class="bg-slate-100 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-10">
            <!-- KIRI: Logo, deskripsi, sosmed -->
            <div class="flex flex-col gap-4 md:max-w-sm">
                <div class="flex items-center gap-2 font-display text-2xl font-black text-slate-900 select-none">
                    <svg width="36" height="36" viewBox="0 0 32 32" fill="none">
                        <rect width="32" height="32" rx="8" fill="url(#lg2)" />
                        <path d="M16 8L24 12V20L16 24L8 20V12L16 8Z" fill="white" />
                        <defs>
                            <linearGradient id="lg2" x1="0" y1="0" x2="32" y2="32">
                                <stop offset="0%" stop-color="#0F766E" />
                                <stop offset="100%" stop-color="#10B981" />
                            </linearGradient>
                        </defs>
                    </svg>
                    Digitalance
                </div>
                <p class="text-slate-500 italic font-medium leading-relaxed">
                    Platform freelance eksklusif untuk siswa/i SKOMDA. Connecting talent dengan opportunity.
                </p>
                <div class="flex items-center gap-2 mt-1">
                    <!-- Email -->
                    <a href="mailto:digitalance@skomda.ac.id"
                        class="bg-white text-slate-500 hover:text-primary border border-slate-200 rounded-full w-10 h-10 flex items-center justify-center transition-colors"
                        aria-label="Email">
                        <!-- Icon Email -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path d="M3 8l9 6 9-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <rect width="18" height="14" x="3" y="5" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" rx="2" />
                        </svg>
                    </a>
                    <!-- LinkedIn -->
                    <a href="https://www.linkedin.com/company/your-link" target="_blank"
                        class="bg-white text-slate-500 hover:text-primary border border-slate-200 rounded-full w-10 h-10 flex items-center justify-center transition-colors"
                        aria-label="LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.76 0-5 2.24-5 5v14c0 2.76 2.24 5 5 5h14c2.76 0 5-2.24 5-5v-14c0-2.76-2.24-5-5-5zm-11 
          19h-3v-11h3v11zm-1.5-12.28c-.97 0-1.75-.79-1.75-1.77 0-.99.78-1.77 1.75-1.77s1.75.78 1.75 1.77c0 .98-.78 
          1.77-1.75 1.77zm13.5 12.28h-3v-5.6c0-1.34-.48-2.26-1.7-2.26-.93 0-1.48.63-1.72 1.24-.09.22-.11.51-.11.81v5.81h-3v-11h3v1.5c.4-.63
          1.13-1.51 2.76-1.51 2.01 0 3.5 1.31 3.5 4.13v6.88z" />
                        </svg>
                    </a>
                    <!-- Instagram -->
                    <a href="https://instagram.com/yourprofile" target="_blank"
                        class="bg-white text-slate-500 hover:text-primary border border-slate-200 rounded-full w-10 h-10 flex items-center justify-center transition-colors"
                        aria-label="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5A4.25 4.25 0 0 0 20.5 16.25v-8.5A4.25 4.25 0 0 0 16.25 3.5zm8.75 2.25a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5a.75.75 0 0 1 .75-.75zm-4 1.75A5 5 0 1 1 7.5 13.5 5 5 0 0 1 12 7.5zm0 1.5a3.5 3.5 0 1 0 3.5 3.5A3.5 3.5 0 0 0 12 9z" />
                        </svg>
                    </a>
                </div>
            </div>
            <!-- KANAN: NAV & LEGAL -->
            <div class="flex flex-row gap-16 w-full md:w-auto justify-start md:justify-end">
                <!-- Navigasi -->
                <div>
                    <h4 class="font-display font-black text-xs text-slate-900 uppercase tracking-widest mb-3">Navigasi</h4>
                    <ul class="flex flex-col gap-2">
                        <li><a href="{{ route('home') }}#home" class="text-slate-700 font-semibold text-sm hover:text-primary transition-colors">Home</a></li>
                        <li><a href="{{ route('home') }}#services" class="text-slate-700 font-semibold text-sm hover:text-primary transition-colors">Services</a></li>
                        <li><a href="{{ route('home') }}#faq" class="text-slate-700 font-semibold text-sm hover:text-primary transition-colors">FAQ</a></li>
                        <li><a href="/login" class="text-slate-700 font-semibold text-sm hover:text-primary transition-colors">Get Started</a></li>
                    </ul>
                </div>
                <!-- Legal -->
                <div>
                    <h4 class="font-display font-black text-xs text-slate-900 uppercase tracking-widest mb-3">Legal</h4>
                    <ul class="flex flex-col gap-2">
                        <li>
                            <button onclick="openPrivacyModal()" type="button" class="text-slate-700 font-semibold text-sm hover:text-primary focus:outline-none transition-colors text-left w-full">Privasi</button>
                        </li>
                        <li>
                            <button onclick="openTnCModal()" type="button" class="text-slate-700 font-semibold text-sm hover:text-primary focus:outline-none transition-colors text-left w-full">Terms of Condition</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="border-t border-slate-200 mt-10 pt-6 flex flex-col items-center justify-center gap-3">
            <span class="text-slate-400 font-semibold text-xs text-center">© {{ date('Y') }} Digitalance. All rights reserved.</span>
        </div>
    </div>

    <!-- Modal Privasi -->
    <div id="privacy-modal" class="fixed inset-0 z-[1000] hidden bg-black/40 flex items-center justify-center"
        style="backdrop-filter:blur(2px)">
        <div
            class="bg-white rounded-2xl w-[95vw] max-w-2xl p-8 shadow-xl relative overflow-y-auto max-h-[85vh] flex flex-col">
            <button type="button" onclick="closePrivacyModal()"
                class="absolute top-4 right-4 text-slate-400 hover:text-primary text-2xl outline-none"
                aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
            <h2 class="font-display text-xl font-bold text-primary mb-2">Kebijakan Privasi</h2>
            <div class="text-slate-600 text-sm leading-relaxed space-y-4">
                <p>
                    Digitalance sangat menghormati privasi pengguna. Kami mengumpulkan data minimum yang diperlukan
                    untuk keperluan operasional platform, seperti nama, email, dan riwayat transaksi. Data Anda tidak
                    akan diperjualbelikan ke pihak ketiga.
                </p>
                <p>
                    Data Anda digunakan untuk:
                <ul class="list-disc pl-5 mt-1">
                    <li>Mengelola akun dan portofolio Anda di marketplace Digitalance.</li>
                    <li>Memproses transaksi antara freelancer dan klien secara aman.</li>
                    <li>Meningkatkan kualitas layanan, serta mengirimkan update atau notifikasi terkait akun.</li>
                </ul>
                </p>
                <p>Digitalance menerapkan standar keamanan terbaik dan staff kami terlatih untuk menjaga data Anda
                    sebaik-baiknya. Anda dapat meminta penghapusan akun dan data personal dengan menghubungi kontak
                    layanan Digitalance.</p>
                <p>Seluruh aktivitas di Digitalance tunduk pada aturan perundang-undangan Indonesia. Untuk pertanyaan
                    privasi lebih lanjut, hubungi kami di email resmi portal ini.</p>
            </div>
        </div>
    </div>

    <!-- Modal Terms of Condition -->
    <div id="tnc-modal" class="fixed inset-0 z-[1000] hidden bg-black/40 flex items-center justify-center"
        style="backdrop-filter:blur(2px)">
        <div
            class="bg-white rounded-2xl w-[95vw] max-w-2xl p-8 shadow-xl relative overflow-y-auto max-h-[85vh] flex flex-col">
            <button type="button" onclick="closeTnCModal()"
                class="absolute top-4 right-4 text-slate-400 hover:text-primary text-2xl outline-none"
                aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
            <h2 class="font-display text-xl font-bold text-primary mb-2">Syarat & Ketentuan</h2>
            <div class="text-slate-600 text-sm leading-relaxed space-y-4">
                <p>
                    Dengan menggunakan Digitalance, Anda dianggap telah membaca, memahami, dan menyetujui seluruh syarat
                    berikut:
                </p>
                <ul class="list-decimal pl-5">
                    <li>Hanya siswa/i SKOMDA yang boleh mendaftar sebagai freelancer di marketplace ini.</li>
                    <li>Setiap pengguna wajib mengisi data akurat dan bertanggung jawab atas data pribadi serta hasil
                        karyanya.</li>
                    <li>Transaksi dilakukan secara aman di dalam platform, admin Digitalance bertindak sebagai perantara
                        jika terjadi sengketa transaksi.</li>
                    <li>Dilarang melakukan aktivitas curang, membagikan kontak pribadi sebelum deal, atau
                        memperjualbelikan akun.</li>
                    <li>Konten portofolio, deskripsi jasa, dan komunikasi wajib mengikuti norma, hukum, dan etika SKOMDA
                        & Indonesia.</li>
                    <li>Pelaku pelanggaran dapat dikenai sanksi mulai dari teguran hingga penghapusan akun secara
                        permanen tanpa pengembalian saldo.</li>
                </ul>
                <p>Platform menyediakan layanan transaksi freelance terbatas pada ruang lingkup yang diatur oleh
                    kebijakan sekolah dan perundang-undangan Indonesia.</p>
                <p>Digitalance dapat sewaktu-waktu mengubah syarat & ketentuan. Segala perubahan akan diinformasikan
                    melalui platform ini. Lanjutkan penggunaan berarti Anda menyetujui versi terbaru.</p>
            </div>
        </div>
    </div>

    <script>
        // Modal logic
        function openPrivacyModal() {
            document.getElementById('privacy-modal').classList.remove('hidden');
        }
        function closePrivacyModal() {
            document.getElementById('privacy-modal').classList.add('hidden');
        }
        function openTnCModal() {
            document.getElementById('tnc-modal').classList.remove('hidden');
        }
        function closeTnCModal() {
            document.getElementById('tnc-modal').classList.add('hidden');
        }
        // close modal jika klik overlay atau ESC
        window.addEventListener('keydown', e => {
            if (e.key === 'Escape') { closePrivacyModal(); closeTnCModal(); }
        });
        document.getElementById('privacy-modal').addEventListener('click', function (e) { if (e.target === this) { closePrivacyModal(); } });
        document.getElementById('tnc-modal').addEventListener('click', function (e) { if (e.target === this) { closeTnCModal(); } });
    </script>
</footer>