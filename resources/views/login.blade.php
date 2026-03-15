@extends('layouts.app')

@section('title', 'Login - Digitalance')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('additional-header')
  <!-- Grain Overlay -->
  <div class="grain-overlay"></div>

  <!-- Background Mesh -->
  <div class="fixed inset-0 -z-10"
      style="background: radial-gradient(at 0% 0%, rgba(16,185,129,0.05) 0px, transparent 50%), radial-gradient(at 100% 100%, rgba(249,115,22,0.05) 0px, transparent 50%);">
  </div>
@endsection

@section('content')
  <!-- ===================== MAIN CONTENT ===================== -->
  <main class="flex-1 flex items-center justify-center p-4 overflow-hidden">

      <!-- Auth Container -->
      <div id="authContainer" class="w-full relative overflow-hidden flex rounded-[28px] border border-slate-100"
          style="max-width:960px; height:min(75vh,660px); min-height:500px; box-shadow: 0 24px 48px -10px rgba(15,23,42,0.12); background:white;">

          <!-- ===== OVERLAY (LEFT PANEL - IMAGE) ===== -->
          <div class="auth-overlay" id="authOverlay">
              <div class="relative h-full w-full">

                  <!-- Hero Image (full bleed) -->
                  <div class="hero-image-container absolute inset-0 overflow-hidden">
                      <img id="heroImage"
                          src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1000"
                          alt="Professional workspace" class="hero-image w-full h-full object-cover" />
                      <div class="image-gradient z-10"></div>
                  </div>

                  <!-- Overlay Text (bottom left) -->
                  <div class="absolute inset-0 flex flex-col justify-end p-8 z-20">
                      <!-- Elite Badge -->
                      <div
                          class="inline-flex items-center gap-2 mb-4 w-fit
              text-emerald-400 font-extrabold text-[0.65rem] uppercase tracking-[0.15em]
              bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-full border border-white/20">
                          <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                              <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                          </svg>
                          <span>Standar Elite</span>
                      </div>

                      <h2 id="overlayTitle"
                          class="overlay-text-item font-display text-white font-extrabold leading-[1.1] mb-3"
                          style="font-size:1.7rem;">
                          Jaringan Presisi untuk Solusi Expert
                      </h2>
                      <p id="overlayDesc"
                          class="overlay-text-item text-white/80 text-[0.85rem] leading-relaxed mb-6 max-w-sm">
                          Rasakan koneksi tanpa hambatan antara permintaan industri premium dan output kreatif
                          elite.
                      </p>

                      <button id="overlayToggle"
                          class="inline-flex items-center gap-2.5 text-white font-bold text-[0.82rem]
              bg-white/15 border border-white/20 backdrop-blur-sm px-6 py-3 rounded-xl
              cursor-pointer transition-all duration-300 hover:bg-white/25 uppercase tracking-wide w-fit">
                          <span id="toggleText">Bergabung dengan Jaringan</span>
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2">
                              <line x1="5" y1="12" x2="19" y2="12"></line>
                              <polyline points="12 5 19 12 12 19"></polyline>
                          </svg>
                      </button>
                  </div>

              </div>
          </div>
          <!-- END OVERLAY -->

          <!-- ===== FORMS (RIGHT SIDE) ===== -->
          <div class="relative w-full h-full">

              <!-- ---- LOGIN PANEL ---- -->
              <div class="form-panel login-panel active" id="loginPanel">
                  <div class="px-10 py-6">

                      <!-- Header -->
                      <div class="mb-5">
                          <div
                              class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-[#0F766E] mb-3">
                              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                  stroke-width="2">
                                  <circle cx="12" cy="12" r="10"></circle>
                                  <line x1="2" y1="12" x2="22" y2="12"></line>
                                  <path
                                      d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                                  </path>
                              </svg>
                          </div>
                          <h2 class="font-display text-[1.7rem] font-extrabold text-slate-900 mb-1">Portal Akses
                          </h2>
                          <p class="text-slate-500 text-[0.82rem]">Autentikasi diperlukan untuk mengakses
                              ekosistem.</p>
                      </div>

                      <!-- Form -->
                      @if ($errors->any())
                          <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200">
                              @foreach ($errors->all() as $error)
                                  <p class="text-[0.75rem] text-red-600 font-bold flex items-center gap-2">
                                      <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
                                  </p>
                              @endforeach
                          </div>
                      @endif
                      <form id="loginForm" method="POST" action="{{ route('login-process') }}"
                          class="flex flex-col gap-3">
                          @csrf

                          <!-- Email -->
                          <div>
                              <label
                                  class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Alamat
                                  Email</label>
                              <div class="relative">
                                  <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                      width="14" height="14" viewBox="0 0 24 24" fill="none"
                                      stroke="currentColor" stroke-width="2">
                                      <path
                                          d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                      </path>
                                      <polyline points="22,6 12,13 2,6"></polyline>
                                  </svg>
                                  <input type="email" name="email" placeholder="nama@domain.com"
                                      class="input-field w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[0.88rem] text-slate-900 transition-all duration-200" />
                              </div>
                          </div>

                          <!-- Password -->
                          <div>
                              <label
                                  class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Kata
                                  Sandi</label>
                              <div class="relative">
                                  <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                      width="14" height="14" viewBox="0 0 24 24" fill="none"
                                      stroke="currentColor" stroke-width="2">
                                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2">
                                      </rect>
                                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                  </svg>
                                  <input type="password" name="password" placeholder="••••••••"
                                      class="input-field w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[0.88rem] text-slate-900 transition-all duration-200" />
                              </div>
                              <div class="flex justify-end mt-1">
                                  <button type="button"
                                      class="text-[0.68rem] text-[#0F766E] font-extrabold bg-none border-none cursor-pointer">Lupa
                                      Kunci?</button>
                              </div>
                          </div>

                          <!-- Submit -->
                          <button type="submit"
                              class="w-full py-3 bg-slate-900 text-white font-bold rounded-[12px] text-[0.88rem] cursor-pointer mt-0.5 transition-all duration-300 hover:bg-black hover:-translate-y-0.5 flex items-center justify-center gap-2">
                              Otorisasi Sesi
                              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                  stroke="currentColor" stroke-width="2">
                                  <polyline points="9 18 15 12 9 6"></polyline>
                              </svg>
                          </button>

                          <!-- Divider -->
                          <div class="flex items-center gap-3 py-0.5">
                              <div class="flex-1 border-t border-slate-100"></div>
                              <span
                                  class="text-[0.56rem] font-black text-slate-300 uppercase tracking-widest">Sinkronisasi
                                  Eksternal</span>
                              <div class="flex-1 border-t border-slate-100"></div>
                          </div>

                          <!-- Social Buttons -->
                          <div class="flex gap-3">
                              <button type="button"
                                  class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-slate-50 border border-slate-100 rounded-xl cursor-pointer font-bold text-[0.78rem] text-slate-500 transition-all duration-300 hover:bg-slate-100">
                                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                      stroke="currentColor" stroke-width="2">
                                      <circle cx="12" cy="12" r="10"></circle>
                                      <circle cx="12" cy="12" r="4"></circle>
                                      <line x1="21.17" y1="8" x2="12" y2="8">
                                      </line>
                                      <line x1="3.95" y1="6.06" x2="8.54" y2="14">
                                      </line>
                                      <line x1="10.88" y1="21.94" x2="15.46" y2="14">
                                      </line>
                                  </svg>
                                  Google
                              </button>
                              <button type="button"
                                  class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-slate-50 border border-slate-100 rounded-xl cursor-pointer font-bold text-[0.78rem] text-slate-500 transition-all duration-300 hover:bg-slate-100">
                                  <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                      <path
                                          d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                                  </svg>
                                  GitHub
                              </button>
                          </div>

                      </form>
                  </div>
              </div>
              <!-- END LOGIN PANEL -->

              <!-- ---- REGISTER PANEL ---- -->
              <div class="form-panel register-panel" id="registerPanel">
                  <!-- Scrollable inner so submit is always reachable -->
                  <div class="px-10 py-4 overflow-y-auto"
                      style="max-height:100%; scrollbar-width:thin; scrollbar-color:#e2e8f0 transparent;">

                      <!-- Header -->
                      <div class="mb-4">
                          <div
                              class="inline-flex items-center gap-1.5 mb-1.5 text-[#0F766E] text-[0.6rem] font-extrabold uppercase tracking-widest">
                              <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                  stroke="currentColor" stroke-width="2">
                                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                  <path d="m9 12 2 2 4-4"></path>
                              </svg>
                              <span>Protokol Aman</span>
                          </div>
                          <h2 class="font-display text-[1.7rem] font-extrabold text-slate-900 mb-0.5">Buat
                              Identitas</h2>
                          <p class="text-slate-500 text-[0.82rem]">Bergabunglah dengan jaringan elite profesional
                              digital.</p>
                      </div>

                      <!-- Role Toggle -->
                      <div class="role-toggle relative inline-flex p-1 bg-slate-100 rounded-full border border-slate-200 mb-4"
                          id="roleToggleContainer">
                          <div class="role-slider"></div>
                          <button type="button" id="btnClient" data-role="client"
                              class="role-btn relative z-10 px-5 py-1.5 min-w-[90px] border-none bg-none text-[0.78rem] font-extrabold text-[#0F766E] cursor-pointer transition-colors duration-300 rounded-full">
                              Klien
                          </button>
                          <button type="button" id="btnFreelancer" data-role="freelancer"
                              class="role-btn relative z-10 px-5 py-1.5 min-w-[90px] border-none bg-none text-[0.78rem] font-semibold text-slate-400 cursor-pointer transition-colors duration-300 rounded-full">
                              Freelancer
                          </button>
                      </div>

                      <!-- Form -->
                      <form id="registerForm" method="POST" action="{{ route('register-process') }}"
                          class="flex flex-col gap-2.5">
                          @csrf

                          <div class="grid grid-cols-2 gap-2.5">
                              <div>
                                  <label
                                      class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Nama
                                      Depan</label>
                                  <div class="relative">
                                      <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                          width="13" height="13" viewBox="0 0 24 24" fill="none"
                                          stroke="currentColor" stroke-width="2">
                                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                          <circle cx="12" cy="7" r="4"></circle>
                                      </svg>
                                      <input type="text" name="name" placeholder="John" required
                                          class="input-field w-full pl-10 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-[0.86rem] text-slate-900 transition-all duration-200" />
                                  </div>
                              </div>
                              <div>
                                  <label
                                      class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Telepon
                                      (WA)</label>
                                  <div class="relative">
                                      <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                          width="13" height="13" viewBox="0 0 24 24" fill="none"
                                          stroke="currentColor" stroke-width="2">
                                          <path
                                              d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                          </path>
                                      </svg>
                                      <input type="text" name="phone" placeholder="0812..." required
                                          class="input-field w-full pl-10 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-[0.86rem] text-slate-900 transition-all duration-200" />
                                  </div>
                              </div>
                          </div>

                          <div>
                              <label
                                  class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Email</label>
                              <div class="relative">
                                  <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                      width="13" height="13" viewBox="0 0 24 24" fill="none"
                                      stroke="currentColor" stroke-width="2">
                                      <path
                                          d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                      </path>
                                      <polyline points="22,6 12,13 2,6"></polyline>
                                  </svg>
                                  <input type="email" name="email" placeholder="john@digitalance.io" required
                                      class="input-field w-full pl-10 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-[0.86rem] text-slate-900 transition-all duration-200" />
                              </div>
                          </div>

                          <div class="skill-wrapper-anim" id="skillFieldWrapper">
                              <div class="skill-inner-content">
                                  <label
                                      class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Keahlian</label>
                                  <div id="tagsContainer"
                                      class="bg-white border border-slate-200 rounded-xl px-2 py-1.5 flex flex-wrap gap-1.5 min-h-[38px] cursor-text transition-all duration-200 focus-within:border-[#0F766E]">
                                      <input id="skillInput" type="text"
                                          class="border-none bg-transparent outline-none flex-1 min-w-[80px] text-[0.86rem] py-0.5"
                                          placeholder="Ketik lalu Enter..." />
                                  </div>
                                  <input type="hidden" id="hiddenSkillsInput" name="skills" value="[]" />
                                  <p id="tagLimitMsg" class="text-[0.6rem] text-slate-400 text-right mt-1">0/5
                                      Keahlian</p>
                              </div>
                          </div>

                          <div>
                              <label
                                  class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Kode
                                  Sandi</label>
                              <div class="relative">
                                  <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                      width="13" height="13" viewBox="0 0 24 24" fill="none"
                                      stroke="currentColor" stroke-width="2">
                                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2">
                                      </rect>
                                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                  </svg>
                                  <input type="password" name="password" placeholder="••••••••" required
                                      class="input-field w-full pl-10 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-[0.86rem] text-slate-900 transition-all duration-200" />
                              </div>
                          </div>

                          <button type="submit"
                              class="w-full py-3 bg-slate-900 text-white font-bold rounded-[12px] text-[0.88rem] cursor-pointer mt-1.5 mb-1 transition-all duration-300 hover:bg-black hover:-translate-y-0.5 flex items-center justify-center gap-2 flex-shrink-0">
                              Inisialisasi Akun
                              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                  stroke="currentColor" stroke-width="2">
                                  <polyline points="9 18 15 12 9 6"></polyline>
                              </svg>
                          </button>
                      </form>
                  </div>
              </div>
              <!-- END REGISTER PANEL -->

          </div>
          <!-- END FORMS -->

      </div>
      <!-- END AUTH CONTAINER -->

  </main>
@endsection
