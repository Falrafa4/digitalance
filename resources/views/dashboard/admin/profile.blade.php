@php
    $user = $user ?? auth()->guard('administrator')->user();
    $role = $role ?? 'Admin';
@endphp

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profile | Digitalance</title>

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
          },
          boxShadow: {
            'teal-md': '0 6px 18px rgba(15,118,110,0.22)',
            'teal-lg': '0 10px 25px rgba(15,118,110,0.25)',
          },
        },
      },
    };
  </script>

  <style>
    body { overflow: hidden; }
    .nav-scroll::-webkit-scrollbar { width: 4px; }
    .nav-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
    .notif-dot { display: none; }
    .has-unread .notif-dot { display: block; }

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

    /* Password toggle eye */
    .pwd-wrap { position: relative; }
    .pwd-wrap input { padding-right: 44px; }
    .pwd-toggle {
      position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer;
      color: #94a3b8; font-size: 17px; line-height: 1;
      transition: color 0.2s;
    }
    .pwd-toggle:hover { color: #0f766e; }
  </style>
</head>

<body class="bg-slate-50 text-slate-900 font-sans h-screen overflow-hidden">
<div class="flex h-screen">

  <!-- ── SIDEBAR ── -->
  <aside class="w-[260px] min-w-[260px] bg-white border-r border-slate-200 flex flex-col px-5 py-9 h-screen">

    <!-- Logo -->
    <div class="flex justify-center mb-11">
      <div class="flex items-center gap-2.5">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
          <rect width="32" height="32" rx="10" fill="url(#logo-g)" />
          <path d="M16 7L25 11.5V20.5L16 25L7 20.5V11.5L16 7Z" fill="white" />
          <defs>
            <linearGradient id="logo-g" x1="0" y1="0" x2="32" y2="32">
              <stop stop-color="#0F766E" />
              <stop offset="1" stop-color="#10B981" />
            </linearGradient>
          </defs>
        </svg>
        <span class="font-display text-[1.4rem] font-extrabold text-[#0f766e]">Digitalance</span>
      </div>
    </div>

    <!-- Nav -->
    <nav class="nav-scroll flex flex-col gap-0.5 flex-1 overflow-y-auto">

      <a href="{{ route('admin.dashboard') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.dashboard') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-dashboard-fill text-[17px]"></i> Dashboard
      </a>

      <a href="{{ route('admin.clients.index') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.clients.*','admin.freelancers.*','admin.skomda-students.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-user-line text-[17px]"></i> User
      </a>

      <a href="{{ route('admin.admins') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.admins*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-user-star-line text-[17px]"></i> Admin
      </a>

      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-file-list-3-line text-[17px]"></i> Orders</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-star-line text-[17px]"></i> Reviews</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

      <a href="{{ route('admin.services') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.services*','admin.service-categories*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-tools-line text-[17px]"></i> Services
      </a>

      <a href="{{ route('admin.transactions') }}"
         class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.transactions*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-bank-card-line text-[17px]"></i> Transactions
      </a>

      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-folder-user-line text-[17px]"></i> Portofolios</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-price-tag-3-line text-[17px]"></i> Offers</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

      <span class="flex items-center justify-between gap-[11px] px-[14px] py-[11px] rounded-[11px] text-slate-300 font-semibold text-[13.5px] cursor-not-allowed select-none">
        <span class="flex items-center gap-[11px]"><i class="ri-hammer-line text-[17px]"></i> Working</span>
        <span class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full font-bold">Soon</span>
      </span>

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
           class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200 bg-[#0f766e] text-white shadow-teal-md">
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
      <div class="relative w-[340px]">
        <i class="ri-search-line absolute left-[15px] top-[13px] text-slate-400 text-[17px] pointer-events-none"></i>
        <input
          type="text"
          placeholder="Search menu, halaman..."
          class="w-full py-[11px] pl-[42px] pr-4 bg-white border-[1.5px] border-slate-200 rounded-[13px] text-[13.5px] outline-none transition-all duration-200 text-slate-900 placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_3px_14px_rgba(15,118,110,0.1)]"
        />
      </div>

      <div class="flex items-center gap-3.5">
        <button id="notif-btn" class="w-11 h-11 rounded-xl border-[1.5px] border-slate-200 bg-white cursor-pointer relative flex items-center justify-center text-slate-500 text-[19px] transition-all duration-200 hover:border-[#0f766e] hover:text-[#0f766e]">
          <i class="ri-notification-3-line"></i>
          <span class="notif-dot absolute top-[9px] right-[9px] w-2 h-2 bg-orange-500 border-2 border-white rounded-full"></span>
        </button>
        <div class="flex items-center gap-[11px] cursor-pointer">
          <img class="w-[42px] h-[42px] rounded-xl object-cover"
               src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0f766e&color=fff"
               alt="{{ $user->name }}" />
          <div class="flex flex-col">
            <span class="font-bold text-[13.5px] text-slate-800">{{ $user->name }}</span>
            <span class="text-[11px] text-slate-500">{{ $role }}</span>
          </div>
        </div>
      </div>
    </header>

    <!-- Page Title -->
    <section class="flex justify-between items-end mb-8 animate-fadeUp">
      <div>
        <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">My Profile</h1>
        <p class="text-slate-500 text-[0.95rem] mt-1">Kelola informasi akun administrator kamu.</p>
      </div>
    </section>

    <!-- Flash Messages -->
    @if(session('success'))
      <div id="flash-success" class="flex items-center gap-3 mb-6 px-5 py-4 bg-white border-[1.5px] border-[#10B981] rounded-2xl text-[13.5px] font-semibold text-emerald-700 animate-fadeUp">
        <i class="ri-check-double-line text-[18px] text-[#10B981]"></i>
        {{ session('success') }}
        <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-slate-400 hover:text-slate-600 text-[16px] bg-transparent border-none cursor-pointer"><i class="ri-close-line"></i></button>
      </div>
    @endif

    @if(session('error'))
      <div id="flash-error" class="flex items-center gap-3 mb-6 px-5 py-4 bg-white border-[1.5px] border-red-400 rounded-2xl text-[13.5px] font-semibold text-red-700 animate-fadeUp">
        <i class="ri-error-warning-line text-[18px] text-red-500"></i>
        {{ session('error') }}
        <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-slate-400 hover:text-slate-600 text-[16px] bg-transparent border-none cursor-pointer"><i class="ri-close-line"></i></button>
      </div>
    @endif

    @if($errors->any())
      <div id="flash-validation" class="flex items-start gap-3 mb-6 px-5 py-4 bg-white border-[1.5px] border-red-400 rounded-2xl text-[13.5px] font-semibold text-red-700 animate-fadeUp">
        <i class="ri-error-warning-line text-[18px] text-red-500 mt-0.5 flex-shrink-0"></i>
        <ul class="list-disc list-inside flex flex-col gap-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button onclick="document.getElementById('flash-validation').remove()" class="ml-auto text-slate-400 hover:text-slate-600 text-[16px] bg-transparent border-none cursor-pointer flex-shrink-0"><i class="ri-close-line"></i></button>
      </div>
    @endif

    <!-- Content Grid -->
    <div class="grid gap-6 animate-fadeUp-delay-1" style="grid-template-columns: 1fr 340px;">

      <!-- LEFT: Edit Form Card -->
      <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden">

        <!-- Hero Banner -->
        <div class="h-[120px] bg-gradient-to-r from-[#0a5e58] via-[#0f766e] to-[#10B981] relative flex-shrink-0">
          <div class="absolute inset-0 opacity-10"
               style="background-image: radial-gradient(circle at 75% 30%, white 0%, transparent 45%), radial-gradient(circle at 20% 70%, white 0%, transparent 40%)"></div>
        </div>

        <!-- Avatar + Name row -->
        <div class="px-8 pb-0 pt-0 -mt-10 flex items-end gap-5 mb-6">
          <div class="relative flex-shrink-0">
            <img
              id="avatar-preview"
              src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0f766e&color=fff&size=160"
              alt="{{ $user->name }}"
              class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-lg" />
            <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full bg-green-400 border-2 border-white"></span>
          </div>
          <div class="mb-2">
            <h2 class="font-display text-[1.4rem] font-bold text-slate-900">{{ $user->name }}</h2>
            <div class="flex items-center gap-2 mt-1">
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#ccfbf1] text-[#0f766e] border border-[#0f766e]/10">
                <i class="ri-shield-user-line"></i> {{ $role }}
              </span>
              <span class="text-[12px] text-slate-400">{{ $user->email }}</span>
            </div>
          </div>
        </div>

        <!-- Divider -->
        <div class="h-px bg-slate-100 mx-8 mb-6"></div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.profile.update') }}" class="px-8 pb-8" id="profile-form">
          @csrf
          @method('PUT')

          <h3 class="font-display text-[1.1rem] font-bold text-slate-900 mb-5">Edit Informasi</h3>

          <div class="grid grid-cols-2 gap-4 mb-4">
            <!-- Name -->
            <div class="flex flex-col gap-1.5">
              <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[0.5px]">Full Name</label>
              <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                required
                placeholder="Full name"
                class="py-[10px] px-[13px] border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:shadow-[0_3px_14px_rgba(15,118,110,0.1)] text-slate-900 placeholder:text-slate-400" />
            </div>

            <!-- Email -->
            <div class="flex flex-col gap-1.5">
              <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[0.5px]">Email Address</label>
              <input
                type="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                required
                placeholder="Email address"
                class="py-[10px] px-[13px] border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:shadow-[0_3px_14px_rgba(15,118,110,0.1)] text-slate-900 placeholder:text-slate-400" />
            </div>
          </div>

          <!-- Role (readonly) -->
          <div class="flex flex-col gap-1.5 mb-6">
            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[0.5px]">Role</label>
            <input
              type="text"
              value="{{ $role }}"
              disabled
              class="py-[10px] px-[13px] border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans text-slate-400 bg-slate-50 cursor-not-allowed" />
          </div>

          <!-- Password Section -->
          <div class="border-t border-slate-100 pt-6 mb-6">
            <h3 class="font-display text-[1.1rem] font-bold text-slate-900 mb-1">Ganti Password</h3>
            <p class="text-[12.5px] text-slate-400 mb-5">Kosongkan jika tidak ingin mengubah password.</p>

            <!-- Current Password -->
            <div class="flex flex-col gap-1.5 mb-4">
              <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[0.5px]">Password Saat Ini</label>
              <div class="pwd-wrap">
                <input
                  type="password"
                  name="current_password"
                  id="current_password"
                  placeholder="Masukkan password saat ini"
                  class="w-full py-[10px] px-[13px] border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:shadow-[0_3px_14px_rgba(15,118,110,0.1)] text-slate-900 placeholder:text-slate-400" />
                <button type="button" class="pwd-toggle" onclick="togglePwd('current_password', this)">
                  <i class="ri-eye-off-line"></i>
                </button>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <!-- New Password -->
              <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[0.5px]">Password Baru</label>
                <div class="pwd-wrap">
                  <input
                    type="password"
                    name="password"
                    id="new_password"
                    placeholder="Min. 8 karakter"
                    class="w-full py-[10px] px-[13px] border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:shadow-[0_3px_14px_rgba(15,118,110,0.1)] text-slate-900 placeholder:text-slate-400" />
                  <button type="button" class="pwd-toggle" onclick="togglePwd('new_password', this)">
                    <i class="ri-eye-off-line"></i>
                  </button>
                </div>
              </div>

              <!-- Confirm Password -->
              <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[0.5px]">Konfirmasi Password</label>
                <div class="pwd-wrap">
                  <input
                    type="password"
                    name="password_confirmation"
                    id="confirm_password"
                    placeholder="Ulangi password baru"
                    class="w-full py-[10px] px-[13px] border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] font-sans outline-none transition-all duration-200 focus:border-[#0f766e] focus:shadow-[0_3px_14px_rgba(15,118,110,0.1)] text-slate-900 placeholder:text-slate-400" />
                  <button type="button" class="pwd-toggle" onclick="togglePwd('confirm_password', this)">
                    <i class="ri-eye-off-line"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3">
            <button
              type="button"
              onclick="document.getElementById('profile-form').reset()"
              class="px-5 py-[10px] rounded-[11px] border-[1.5px] border-slate-200 bg-white text-slate-500 font-semibold text-[13.5px] hover:border-slate-300 hover:text-slate-700 transition-all duration-200 cursor-pointer">
              <i class="ri-refresh-line"></i> Reset
            </button>
            <button
              type="submit"
              class="flex items-center gap-2 px-6 py-[10px] rounded-[11px] bg-[#0f766e] text-white font-semibold text-[13.5px] hover:bg-[#0a5e58] shadow-teal-md hover:shadow-teal-lg transition-all duration-200 cursor-pointer">
              <i class="ri-save-line"></i> Simpan Perubahan
            </button>
          </div>

        </form>
      </div>

      <!-- RIGHT: Info + Danger Zone -->
      <div class="flex flex-col gap-5">

        <!-- Account Info Card -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 animate-fadeUp-delay-1">
          <h3 class="font-display text-[1.05rem] font-bold text-slate-900 mb-5">Informasi Akun</h3>

          <div class="flex flex-col gap-0">

            <div class="flex items-center gap-3 py-3 border-b border-slate-100">
              <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-[#0f766e] text-[16px] flex-shrink-0">
                <i class="ri-fingerprint-line"></i>
              </div>
              <div>
                <p class="text-[10.5px] font-bold text-slate-400 uppercase tracking-[0.4px]">Admin ID</p>
                <p class="text-[13.5px] font-bold text-slate-800 font-mono">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3 py-3 border-b border-slate-100">
              <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-[#0f766e] text-[16px] flex-shrink-0">
                <i class="ri-calendar-line"></i>
              </div>
              <div>
                <p class="text-[10.5px] font-bold text-slate-400 uppercase tracking-[0.4px]">Bergabung</p>
                <p class="text-[13.5px] font-bold text-slate-800">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3 py-3 border-b border-slate-100">
              <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-[#0f766e] text-[16px] flex-shrink-0">
                <i class="ri-time-line"></i>
              </div>
              <div>
                <p class="text-[10.5px] font-bold text-slate-400 uppercase tracking-[0.4px]">Terakhir Diperbarui</p>
                <p class="text-[13.5px] font-bold text-slate-800">{{ $user->updated_at ? $user->updated_at->format('d M Y, H:i') : '-' }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3 py-3">
              <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-[#0f766e] text-[16px] flex-shrink-0">
                <i class="ri-shield-check-line"></i>
              </div>
              <div>
                <p class="text-[10.5px] font-bold text-slate-400 uppercase tracking-[0.4px]">Status</p>
                <div class="flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                  <p class="text-[13.5px] font-bold text-slate-800">Active</p>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-red-50 rounded-3xl border border-red-200 p-6">
          <h3 class="font-display text-[1.05rem] font-bold text-red-600 mb-2 flex items-center gap-2">
            <i class="ri-error-warning-line"></i> Danger Zone
          </h3>
          <p class="text-[12.5px] text-slate-500 mb-4 leading-relaxed">
            Logout dari semua sesi aktif yang tersambung ke akun ini.
          </p>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
              type="submit"
              class="w-full flex items-center justify-center gap-2 py-[10px] rounded-[11px] bg-red-100 text-red-700 font-semibold text-[13.5px] hover:bg-red-600 hover:text-white transition-all duration-200 cursor-pointer border-none">
              <i class="ri-logout-box-line"></i> Logout dari Semua Sesi
            </button>
          </form>
        </div>

      </div>
    </div><!-- /.grid -->

  </main>
</div>

<!-- Toast -->
<div id="toast-container"></div>

<script>
  function togglePwd(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.className = 'ri-eye-line';
    } else {
      input.type = 'password';
      icon.className = 'ri-eye-off-line';
    }
  }

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

  // Auto-show toast from session
  @if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast("{{ session('success') }}", 'success'));
  @endif
  @if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast("{{ session('error') }}", 'danger'));
  @endif

  // Notif btn
  document.addEventListener('DOMContentLoaded', () => {
    const notifBtn = document.getElementById('notif-btn');
    if (notifBtn) notifBtn.addEventListener('click', () => notifBtn.classList.remove('has-unread'));
  });
</script>
</body>
</html>