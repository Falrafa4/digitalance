@php
    $user = Auth::user(); 
@endphp

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | Digitalance</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'sans-serif'],
            display: ['Sora', 'sans-serif'],
          },
          colors: {
            primary: '#0F766E',
            'primary-light': '#10B981',
            teal: { deep: '#0f766e' },
            orange: '#f97316',
          },
          borderRadius: {
            '2xl': '16px',
            '3xl': '24px',
          },
          keyframes: {
            fadeUp: {
              from: { opacity: '0', transform: 'translateY(16px)' },
              to:   { opacity: '1', transform: 'translateY(0)' },
            },
          },
          animation: {
            fadeUp: 'fadeUp 0.6s ease both',
            'fadeUp-delay-1': 'fadeUp 0.6s 0.1s ease both',
            'fadeUp-delay-2': 'fadeUp 0.6s 0.2s ease both',
            'fadeUp-delay-3': 'fadeUp 0.6s 0.3s ease both',
          },
          boxShadow: {
            'teal-md': '0 6px 18px rgba(15,118,110,0.22)',
            'teal-lg': '0 10px 25px rgba(15,118,110,0.25)',
            'teal-xl': '0 15px 30px rgba(15,118,110,0.35)',
            'green-sm': '0 4px 12px rgba(16,185,129,0.3)',
            'red-sm':   '0 4px 12px rgba(239,68,68,0.3)',
          },
        },
      },
    };
  </script>

  <style>
    body { overflow: hidden; }
    .nav-scroll { overflow-y: auto; }
    .nav-scroll::-webkit-scrollbar { width: 4px; }
    .nav-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }

    .notif-dot { display: none; }
    .has-unread .notif-dot { display: block; }

    .card-approved { border-color: #10B981 !important; background: #f0fdf9 !important; }
    .card-rejected { border-color: #ef4444 !important; background: #fef2f2 !important; }

    #toast-container { position: fixed; top: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
    .toast {
      display: flex; align-items: center; gap: 12px;
      padding: 14px 20px; border-radius: 14px;
      font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13.5px; font-weight: 600;
      box-shadow: 0 8px 30px rgba(0,0,0,0.12);
      pointer-events: auto;
      animation: toastIn 0.35s cubic-bezier(0.175,0.885,0.32,1.275) both;
      max-width: 320px;
    }
    .toast.toast-success { background: #ffffff; border: 1.5px solid #10B981; color: #065f46; }
    .toast.toast-danger  { background: #ffffff; border: 1.5px solid #ef4444; color: #991b1b; }
    .toast .toast-icon { font-size: 18px; flex-shrink: 0; }
    .toast .toast-close { margin-left: auto; cursor: pointer; opacity: 0.5; font-size: 16px; background: none; border: none; color: inherit; padding: 0; line-height: 1; }
    .toast .toast-close:hover { opacity: 1; }
    .toast.toast-hide { animation: toastOut 0.3s ease forwards; }

    @keyframes toastIn {
      from { opacity: 0; transform: translateX(60px) scale(0.95); }
      to   { opacity: 1; transform: translateX(0) scale(1); }
    }
    @keyframes toastOut {
      from { opacity: 1; transform: translateX(0) scale(1); }
      to   { opacity: 0; transform: translateX(60px) scale(0.95); }
    }
  </style>
</head>

<body class="bg-slate-50 text-slate-900 font-sans h-screen overflow-hidden">
<div class="flex h-screen">

  <!-- ── SIDEBAR ── -->
  <aside class="w-[260px] min-w-[260px] bg-white border-r border-slate-200 flex flex-col px-5 py-9 h-screen">

    <!-- Logo -->
    <div class="flex justify-center mb-11">
      <div class="flex items-center gap-2.5">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="32" height="32" rx="10" fill="url(#logo-gradient)" />
          <path d="M16 7L25 11.5V20.5L16 25L7 20.5V11.5L16 7Z" fill="white" />
          <defs>
            <linearGradient id="logo-gradient" x1="0" y1="0" x2="32" y2="32">
              <stop stop-color="#0F766E" />
              <stop offset="1" stop-color="#10B981" />
            </linearGradient>
          </defs>
        </svg>
        <span class="font-display text-[1.4rem] font-extrabold text-[#0f766e]">Digitalance</span>
      </div>
    </div>

    <!-- Nav main -->
    <nav class="nav-scroll flex flex-col gap-0.5 flex-1 overflow-y-auto">

      {{-- Dashboard --}}
      <a href="{{ route('admin.dashboard') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.dashboard') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-dashboard-fill text-[17px]"></i> Dashboard
      </a>

      {{-- User --}}
      <a href="{{ route('admin.clients.index') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.clients.*','admin.freelancers.*','admin.skomda-students.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-user-line text-[17px]"></i> User
      </a>

      {{-- Admin --}}
      <a href="{{ route('admin.super') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.super*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-user-star-line text-[17px]"></i> Admin
      </a>

      {{-- Orders — belum ada route --}}
      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-file-list-3-line text-[17px]"></i> Orders</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

      {{-- Reviews — belum ada route --}}
      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-star-line text-[17px]"></i> Reviews</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

      {{-- Services --}}
      <a href="{{ route('admin.services') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.services.*','admin.service-categories.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-tools-line text-[17px]"></i> Services
      </a>

      {{-- Transactions --}}
      <a href="{{ route('admin.transactions') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.transactions.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-bank-card-line text-[17px]"></i> Transactions
      </a>

      {{-- Portofolios — belum ada route --}}
      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-folder-user-line text-[17px]"></i> Portofolios</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

      {{-- Offers — belum ada route --}}
      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-price-tag-3-line text-[17px]"></i> Offers</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

      {{-- Working — belum ada route --}}
      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-hammer-line text-[17px]"></i> Working</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

      {{-- Negotiations — belum ada route --}}
      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-discuss-line text-[17px]"></i> Negotiations</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

    </nav>

    <!-- Sidebar footer -->
    <div class="mt-auto">
      <div class="h-px bg-slate-200 my-3.5"></div>
      <nav class="flex flex-col gap-0.5">
        <a href="{{ url('admin/profile') }}"
           class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-500 font-semibold text-[13.5px] transition-all duration-200 hover:bg-slate-100 hover:text-[#0f766e]">
          <i class="ri-user-line text-[17px]"></i> Account
        </a>
        <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
          <span class="flex items-center gap-[11px]"><i class="ri-settings-3-line text-[17px]"></i> Settings</span>
          <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
        </span>
        <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">@csrf</form>
        <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] text-red-500 font-semibold text-[13.5px] w-full text-left transition-all duration-200 hover:bg-red-50 hover:text-red-600 border-none bg-transparent cursor-pointer">
          <i class="ri-logout-box-line text-[17px]"></i> Logout
        </button>
      </nav>
      <p class="text-[10.5px] text-slate-400 text-center mt-5 leading-relaxed">
        &copy; 2026 Digitalance.<br />All rights reserved.
      </p>
    </div>
  </aside>

  <!-- ── MAIN ── -->
  <main class="flex-1 px-11 py-7 overflow-y-auto min-w-0">

    <!-- Header -->
    <header class="flex items-center justify-between mb-9">
      <div class="relative w-[340px]" id="search-wrapper">
        <i class="ri-search-line absolute left-[15px] top-[13px] text-slate-400 text-[17px] pointer-events-none z-10"></i>
        <input
          id="search-input"
          type="text"
          placeholder="Search menu, halaman..."
          autocomplete="off"
          class="w-full py-[11px] pl-[42px] pr-4 bg-white border-[1.5px] border-slate-200 rounded-[13px] text-[13.5px] font-sans outline-none transition-all duration-200 text-slate-900 placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_3px_14px_rgba(15,118,110,0.1)]"
        />
        <!-- Dropdown -->
        <div id="search-dropdown"
             class="absolute top-[calc(100%+6px)] left-0 w-full bg-white border border-slate-200 rounded-[13px] shadow-lg z-50 overflow-hidden hidden">
          <div id="search-results" class="flex flex-col py-1.5 max-h-[320px] overflow-y-auto"></div>
        </div>
      </div>

      <div class="flex items-center gap-3.5">
        <button id="notif-btn" class="w-11 h-11 rounded-xl border-[1.5px] border-slate-200 bg-white cursor-pointer relative flex items-center justify-center text-slate-500 text-[19px] transition-all duration-200 hover:border-[#0f766e] hover:text-[#0f766e]">
          <i class="ri-notification-3-line"></i>
          <span class="notif-dot absolute top-[9px] right-[9px] w-2 h-2 bg-orange-500 border-2 border-white rounded-full"></span>
        </button>

        <div class="flex items-center gap-[11px] cursor-pointer">
          <img class="w-[42px] h-[42px] rounded-xl object-cover" src="https://picsum.photos/seed/admin/100/100" alt="Admin Profile" />
          <div class="flex flex-col">
            <span class="font-bold text-[13.5px] text-slate-800">{{ Auth::user()->name }}</span>
            <span class="text-[11px] text-slate-500">System Manager</span>
          </div>
        </div>
      </div>
    </header>

    <!-- Welcome -->
    <section class="flex justify-between items-end mb-8 animate-fadeUp">
      <div>
        <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Hi, {{ Auth::user()->name }}! 👋</h1>
        <p class="text-slate-500 text-[0.95rem] mt-1">Here's what's happening with your work today.</p>
      </div>
    </section>

    <!-- Stats -->
    <section class="grid grid-cols-4 gap-6 mb-10">

      {{-- Total Users --}}
      <div class="bg-white px-8 py-7 rounded-3xl border border-slate-200 transition-all duration-300 hover:border-[#10B981] animate-fadeUp-delay-1">
        <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Total Users</span>
        @isset($totalUsers)
          <span class="font-display text-[2rem] font-extrabold text-slate-900">{{ number_format($totalUsers, 0, ',', '.') }}</span>
        @else
          <span class="font-display text-[1.2rem] font-bold text-slate-300">—</span>
        @endisset
      </div>

      {{-- Active Projects --}}
      <div class="bg-white px-8 py-7 rounded-3xl border border-slate-200 transition-all duration-300 hover:border-[#10B981] animate-fadeUp-delay-1" style="animation-delay:0.1s">
        <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Active Projects</span>
        @isset($activeProjects)
          <span class="font-display text-[2rem] font-extrabold text-slate-900">{{ number_format($activeProjects, 0, ',', '.') }}</span>
        @else
          <span class="font-display text-[1.2rem] font-bold text-slate-300">—</span>
        @endisset
      </div>

      {{-- Platform Revenue --}}
      <div class="bg-white px-8 py-7 rounded-3xl border border-slate-200 transition-all duration-300 hover:border-[#10B981] animate-fadeUp-delay-1" style="animation-delay:0.2s">
        <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Platform Revenue</span>
        @isset($totalRevenue)
          <div class="flex items-baseline gap-1">
            <span class="text-[1.1rem] font-bold text-[#0f766e]">Rp</span>
            <span class="font-display text-[2rem] font-extrabold text-slate-900">{{ number_format($totalRevenue, 0, ',', '.') }}</span>
          </div>
        @else
          <span class="font-display text-[1.2rem] font-bold text-slate-300">—</span>
        @endisset
      </div>

      {{-- Open Disputes --}}
      <div class="bg-white px-8 py-7 rounded-3xl border border-slate-200 transition-all duration-300 hover:border-[#10B981] animate-fadeUp-delay-1" style="animation-delay:0.3s">
        <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Open Disputes</span>
        @isset($openDisputes)
          <span class="font-display text-[2rem] font-extrabold text-red-500">{{ number_format($openDisputes, 0, ',', '.') }}</span>
        @else
          <span class="font-display text-[1.2rem] font-bold text-slate-300">—</span>
        @endisset
      </div>

    </section>

    <!-- Pending Verifications + System Alerts -->
    <div class="grid gap-6 mb-10" style="grid-template-columns: 2fr 1fr;">

      <!-- Pending Verifications -->
      <section>
        <div class="flex justify-between items-center mb-5">
          <h2 class="font-display text-[1.3rem] font-bold">Pending Verifications</h2>
        </div>

        <div id="verification-container" class="flex flex-col gap-3">
          @forelse($pendingVerifications as $v)
            <div class="approval-card bg-white border border-slate-200 rounded-2xl p-[18px_20px] animate-fadeUp" data-id="{{ $v->id }}">
              <div class="flex items-start gap-3.5 mb-4">
                <img class="w-11 h-11 rounded-xl object-cover border"
                     src="https://ui-avatars.com/api/?name={{ urlencode($v->user->name ?? 'F') }}&background=0f766e&color=fff" />
                <div class="flex-1">
                  <span class="font-bold text-[14px] text-slate-900 user-name">{{ $v->user->name ?? 'User' }}</span>
                  <p class="text-[12px] text-slate-500">{{ $v->category ?? 'Freelancer' }}</p>
                </div>
              </div>

              <div class="approval-actions flex gap-2.5 pt-3.5 border-t border-slate-200">
                <button onclick="handleApprove('{{ $v->id }}')" class="flex-1 py-2 rounded-lg bg-emerald-100 text-emerald-900 text-xs font-bold">
                  Approve
                </button>
                <button onclick="handleReject('{{ $v->id }}')" class="flex-1 py-2 rounded-lg bg-red-100 text-red-900 text-xs font-bold">
                  Reject
                </button>
              </div>
            </div>
          @empty
            <div class="text-center py-12 px-5 bg-white border-2 border-dashed border-slate-200 rounded-3xl animate-fadeUp">
              <div class="flex items-center justify-center text-[44px] text-slate-300 mb-3">
                <i class="ri-inbox-archive-line"></i>
              </div>
              <h3 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">No Pending Requests</h3>
              <p class="text-slate-400 text-[13.5px]">Semua pengajuan sudah bersih!</p>
            </div>
          @endforelse
        </div>
      </section>

      <!-- System Alerts -->
      <section>
        <div class="flex justify-between items-center mb-5">
          <h2 class="font-display text-[1.3rem] font-bold">System Alerts</h2>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 flex flex-col gap-4">

          @php $hasAnyAlert = false; @endphp

          @if(isset($openDisputes) && $openDisputes > 0)
            @php $hasAnyAlert = true; @endphp
            <div class="flex gap-3 items-start pb-4 border-b border-slate-200">
              <i class="ri-error-warning-line text-orange-500 text-[20px] flex-shrink-0 mt-0.5"></i>
              <div>
                <h4 class="text-[14px] text-slate-900 mb-1 font-semibold">Open Disputes</h4>
                <p class="text-[12px] text-slate-500 leading-relaxed">
                  Ada <strong>{{ $openDisputes }}</strong> dispute yang belum diselesaikan.
                </p>
              </div>
            </div>
          @endif

          @if(isset($pendingVerifications) && count($pendingVerifications) >= 5)
            @php $hasAnyAlert = true; @endphp
            <div class="flex gap-3 items-start pb-4 border-b border-slate-200">
              <i class="ri-user-received-line text-orange-400 text-[20px] flex-shrink-0 mt-0.5"></i>
              <div>
                <h4 class="text-[14px] text-slate-900 mb-1 font-semibold">Antrian Verifikasi Penuh</h4>
                <p class="text-[12px] text-slate-500 leading-relaxed">
                  Ada <strong>{{ count($pendingVerifications) }}</strong> pengajuan menunggu persetujuan.
                </p>
              </div>
            </div>
          @endif

          @if(!$hasAnyAlert)
            <div class="flex gap-3 items-start">
              <i class="ri-shield-check-line text-[#10B981] text-[20px] flex-shrink-0 mt-0.5"></i>
              <div>
                <h4 class="text-[14px] text-slate-900 mb-1 font-semibold">Semua Sistem Normal</h4>
                <p class="text-[12px] text-slate-500 leading-relaxed">Tidak ada masalah terdeteksi.</p>
              </div>
            </div>
          @endif

        </div>
      </section>

    </div>{{-- END grid Pending + Alerts --}}

    <!-- Admin content area -->
    <section id="admin-content" class="mt-10 animate-fadeUp-delay-2"></section>

  </main>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script>
  const hasUnreadMessages = {{ ((isset($pendingVerifications) && count($pendingVerifications) > 0) || (isset($openDisputes) && $openDisputes > 0)) ? 'true' : 'false' }};

  function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const icon = type === 'success' ? 'ri-check-double-line' : 'ri-close-circle-line';
    const toast = document.createElement('div');
    toast.className = `toast toast-${type === 'success' ? 'success' : 'danger'}`;
    toast.innerHTML = `
      <i class="toast-icon ${icon}"></i>
      <span>${message}</span>
      <button class="toast-close" onclick="dismissToast(this.parentElement)"><i class="ri-close-line"></i></button>`;
    container.appendChild(toast);
    setTimeout(() => dismissToast(toast), 3500);
  }

  function dismissToast(toast) {
    if (!toast || toast.classList.contains('toast-hide')) return;
    toast.classList.add('toast-hide');
    setTimeout(() => toast.remove(), 300);
  }

  async function handleApprove(id) {
    const card = document.querySelector(`.approval-card[data-id="${id}"]`);
    if (!card) return;

    const name = card.querySelector('.user-name')?.textContent?.trim() ?? 'Freelancer';

    try {
      const response = await fetch(`{{ url('/admin/verify-freelancer') }}/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });

      if (!response.ok) throw new Error('DB Update Failed');

      card.classList.add('card-approved');
      showToast(`${name} berhasil diverifikasi!`, 'success');

      setTimeout(() => {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => {
          card.remove();
          const container = document.getElementById('verification-container');
          if (container && container.querySelectorAll('.approval-card').length === 0) {
            container.innerHTML = `
              <div class="text-center py-10 bg-white border-2 border-dashed border-slate-200 rounded-2xl animate-fadeUp">
                <p class="text-slate-400 font-medium">Semua pengajuan sudah diproses! ✅</p>
              </div>`;
          }
        }, 300);
      }, 800);

    } catch (error) {
      showToast("Gagal memverifikasi. Coba lagi.", "danger");
    }
  }

  async function handleReject(id) {
    const card = document.querySelector(`.approval-card[data-id="${id}"]`);
    if (!card) return;

    if (!confirm('Yakin ingin menolak verifikasi ini?')) return;

    try {
      const response = await fetch(`{{ url('/admin/reject-freelancer') }}/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      });

      if (!response.ok) throw new Error('Gagal update database');

      card.classList.add('card-rejected');
      showToast("Verifikasi ditolak.", "danger");

      setTimeout(() => {
        card.style.opacity = '0';
        card.style.transform = 'translateX(-30px)';
        setTimeout(() => card.remove(), 400);
      }, 800);

    } catch (error) {
      showToast("Gagal menolak verifikasi.", "danger");
    }
  }

  function initAdminDashboard() {
    const notifBtn = document.getElementById('notif-btn');
    if (!notifBtn) return;
    notifBtn.classList.toggle('has-unread', hasUnreadMessages);
    notifBtn.addEventListener('click', () => {
      notifBtn.classList.remove('has-unread');
    });
  }

  // ─── SEARCH ───
  const searchMenus = [
    { label: 'Dashboard',       sub: 'Halaman utama admin',           icon: 'ri-dashboard-fill',      url: '{{ route("admin.dashboard") }}',        available: true  },
    { label: 'Clients',         sub: 'User › Daftar client',          icon: 'ri-user-line',           url: '{{ route("admin.clients.index") }}',          available: true  },
    { label: 'Freelancers',     sub: 'User › Daftar freelancer',      icon: 'ri-user-star-line',      url: '{{ route("admin.freelancers.index") }}',      available: true  },
    { label: 'Skomda Students', sub: 'User › Data siswa skomda',      icon: 'ri-user-line',           url: '{{ route("admin.skomda-students.index") }}',  available: true  },
    { label: 'Administrators',  sub: 'Admin › Daftar admin',          icon: 'ri-user-star-line',      url: '{{ route("admin.super") }}',   available: true  },
    { label: 'Services',        sub: 'Daftar layanan',                 icon: 'ri-tools-line',          url: '{{ route("admin.services") }}',              available: true  },
    { label: 'Service Categories', sub: 'Kategori layanan',           icon: 'ri-layout-grid-line',    url: '{{ route("admin.service-categories") }}',    available: true  },
    { label: 'Transactions',    sub: 'Riwayat transaksi',              icon: 'ri-bank-card-line',      url: '{{ route("admin.transactions") }}',     available: true  },
    { label: 'Orders',          sub: 'Belum tersedia',                 icon: 'ri-file-list-3-line',    url: null,                                    available: false },
    { label: 'Reviews',         sub: 'Belum tersedia',                 icon: 'ri-star-line',           url: null,                                    available: false },
    { label: 'Portofolios',     sub: 'Belum tersedia',                 icon: 'ri-folder-user-line',    url: null,                                    available: false },
    { label: 'Offers',          sub: 'Belum tersedia',                 icon: 'ri-price-tag-3-line',    url: null,                                    available: false },
    { label: 'Working',         sub: 'Belum tersedia',                 icon: 'ri-hammer-line',         url: null,                                    available: false },
    { label: 'Negotiations',    sub: 'Belum tersedia',                 icon: 'ri-discuss-line',        url: null,                                    available: false },
    { label: 'Profile Admin',   sub: 'Akun › Profil administrator',   icon: 'ri-user-line',           url: '{{ url("admin/profile") }}',            available: true  },
  ];

  const searchInput    = document.getElementById('search-input');
  const searchDropdown = document.getElementById('search-dropdown');
  const searchResults  = document.getElementById('search-results');

  function renderSearch(query) {
    const q = query.trim().toLowerCase();
    const filtered = q === ''
      ? searchMenus
      : searchMenus.filter(m =>
          m.label.toLowerCase().includes(q) ||
          m.sub.toLowerCase().includes(q)
        );

    if (filtered.length === 0) {
      searchResults.innerHTML = `
        <div class="px-4 py-5 text-center text-slate-400 text-[13px]">
          <i class="ri-search-2-line text-2xl mb-1 block"></i>
          Tidak ada hasil untuk "<strong>${query}</strong>"
        </div>`;
    } else {
      searchResults.innerHTML = filtered.map(m => `
        ${m.available
          ? `<a href="${m.url}"
               class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors cursor-pointer group">
               <span class="w-8 h-8 rounded-lg bg-[#f0fdf9] flex items-center justify-center text-[#0f766e] text-[15px] flex-shrink-0">
                 <i class="${m.icon}"></i>
               </span>
               <div class="flex-1 min-w-0">
                 <p class="text-[13.5px] font-semibold text-slate-800 group-hover:text-[#0f766e]">${m.label}</p>
                 <p class="text-[11px] text-slate-400 truncate">${m.sub}</p>
               </div>
               <i class="ri-arrow-right-s-line text-slate-300 group-hover:text-[#0f766e] text-[17px]"></i>
             </a>`
          : `<div class="flex items-center gap-3 px-4 py-2.5 opacity-40 cursor-not-allowed">
               <span class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-[15px] flex-shrink-0">
                 <i class="${m.icon}"></i>
               </span>
               <div class="flex-1 min-w-0">
                 <p class="text-[13.5px] font-semibold text-slate-500">${m.label}</p>
                 <p class="text-[11px] text-slate-400 truncate">${m.sub}</p>
               </div>
               <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-0.5 rounded-full font-medium">Soon</span>
             </div>`
        }
      `).join('');
    }
    searchDropdown.classList.remove('hidden');
  }

  searchInput.addEventListener('focus', () => renderSearch(searchInput.value));
  searchInput.addEventListener('input', () => renderSearch(searchInput.value));

  document.addEventListener('click', (e) => {
    if (!document.getElementById('search-wrapper').contains(e.target)) {
      searchDropdown.classList.add('hidden');
    }
  });

  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      searchDropdown.classList.add('hidden');
      searchInput.blur();
    }
  });

  document.addEventListener('DOMContentLoaded', initAdminDashboard);
</script>
</body>
</html>