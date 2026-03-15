@php
    $user = Auth::guard('client')->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Digitalance Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            display: ['Sora', 'sans-serif'],
            sans: ['Plus Jakarta Sans', 'sans-serif'],
          },
          colors: {
            teal: {
              deep: '#0f766e',
              light: '#10B981',
            },
            orange: '#f97316',
          },
          borderRadius: {
            '2xl': '16px',
            '3xl': '24px',
          },
          keyframes: {
            fadeUp: {
              '0%': { opacity: '0', transform: 'translateY(20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            }
          },
          animation: {
            fadeUp: 'fadeUp 0.6s ease both',
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .notif-dot { display: none; }
    .has-unread .notif-dot { display: block; }

    /* Project card hover */
    .project-card { transition: all 0.25s ease; }
    .project-card:hover { transform: translateY(-5px); box-shadow: 0 24px 48px rgba(15,118,110,0.10); border-color: #5eead4; }

    /* Stat card accent line */
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 3px;
      border-radius: 24px 24px 0 0;
      background: linear-gradient(90deg, #0f766e, #10B981);
      opacity: 0;
      transition: opacity 0.3s;
    }
    .stat-card:hover::before { opacity: 1; }
    .stat-card:hover { box-shadow: 0 12px 32px rgba(15,118,110,0.08); transform: translateY(-3px); }
    .stat-card { transition: all 0.25s ease; }

    /* CTA button */
    .btn-cta { transition: all 0.3s cubic-bezier(0.175,0.885,0.32,1.275); }
    .btn-cta:hover { transform: translateY(-3px); box-shadow: 0 18px 36px rgba(15,118,110,0.35) !important; }

    /* Empty state */
    .btn-empty:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,118,110,0.2); }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    /* Fade up */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .anim-1 { animation: fadeUp 0.5s ease both 0.05s; }
    .anim-2 { animation: fadeUp 0.5s ease both 0.15s; }
    .anim-3 { animation: fadeUp 0.5s ease both 0.25s; }
    .anim-4 { animation: fadeUp 0.5s ease both 0.35s; }
  </style>
</head>
<body class="bg-slate-50 text-slate-900 h-screen overflow-hidden">

<div class="flex h-screen">

  <!-- ── SIDEBAR ── -->
  <aside class="w-[260px] min-w-[260px] flex flex-col h-screen bg-white border-r border-slate-200 px-5 py-9">
    <!-- Logo -->
    <div class="flex justify-center mb-11">
      <div class="flex items-center gap-2.5">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="32" height="32" rx="10" fill="url(#logo-gradient)" />
          <path d="M16 7L25 11.5V20.5L16 25L7 20.5V11.5L16 7Z" fill="white" />
          <defs>
            <linearGradient id="logo-gradient" x1="0" y1="0" x2="32" y2="32">
              <stop stop-color="#0f766e" />
              <stop offset="1" stop-color="#10B981" />
            </linearGradient>
          </defs>
        </svg>
        <span class="font-display text-[1.4rem] font-extrabold text-teal-deep">Digitalance</span>
      </div>
    </div>

    <!-- Nav Main -->
    <nav class="flex flex-col gap-0.5">
      <button class="nav-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold font-sans transition-all w-full text-left bg-teal-deep text-white shadow-[0_6px_18px_rgba(15,118,110,0.22)]">
        <i class="ri-dashboard-fill text-[17px]"></i> Dashboard
      </button>
      <button class="nav-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold font-sans transition-all w-full text-left text-slate-500 hover:bg-slate-100 hover:text-teal-deep">
        <i class="ri-group-line text-[17px]"></i> Find Talent
      </button>
      <button class="nav-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold font-sans transition-all w-full text-left text-slate-500 hover:bg-slate-100 hover:text-teal-deep">
        <i class="ri-briefcase-line text-[17px]"></i> My Projects
      </button>
      <button class="nav-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold font-sans transition-all w-full text-left text-slate-500 hover:bg-slate-100 hover:text-teal-deep">
        <i class="ri-message-3-line text-[17px]"></i> Messages
      </button>
      <button class="nav-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold font-sans transition-all w-full text-left text-slate-500 hover:bg-slate-100 hover:text-teal-deep">
        <i class="ri-wallet-3-line text-[17px]"></i> Payment
      </button>
    </nav>

    <!-- Nav Footer -->
    <div class="mt-auto">
      <div class="h-px bg-slate-200 my-3.5"></div>
      <nav class="flex flex-col gap-0.5">
        <button class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold font-sans transition-all w-full text-left text-slate-500 hover:bg-slate-100 hover:text-teal-deep">
          <i class="ri-user-line text-[17px]"></i> Account
        </button>
        <button class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold font-sans transition-all w-full text-left text-slate-500 hover:bg-slate-100 hover:text-teal-deep">
          <i class="ri-settings-3-line text-[17px]"></i> Settings
        </button>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
    class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold font-sans transition-all w-full text-left text-red-500 hover:bg-red-50 hover:text-red-600">
    <i class="ri-logout-box-line text-[17px]"></i> Logout
</button>
      </nav>
      <p class="text-[10.5px] text-slate-400 text-center mt-5 leading-relaxed">
        &copy; 2026 Digitalance.<br>All rights reserved.
      </p>
    </div>
  </aside>

  <!-- ── MAIN ── -->
  <main class="flex-1 min-w-0 px-10 pt-6 pb-10 overflow-y-auto bg-slate-50">

    <!-- ── HEADER ── -->
    <header class="flex items-center justify-between mb-8 anim-1">
      <!-- Search -->
      <div class="relative w-[320px]">
        <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
        <input
          type="text"
          placeholder="Search projects, talent..."
          class="w-full pl-11 pr-4 py-[11px] bg-white border border-slate-200 rounded-2xl text-sm outline-none transition-all text-slate-800 placeholder:text-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/10"
        />
      </div>

      <div class="flex items-center gap-3">
        <!-- Notif -->
        <button id="notif-btn" class="relative w-11 h-11 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-500 text-lg transition-all hover:border-teal-500 hover:text-teal-700 hover:bg-teal-50">
          <i class="ri-notification-3-line"></i>
          <span class="notif-dot absolute top-2 right-2 w-2 h-2 rounded-full border-2 border-white" style="background:#f97316"></span>
        </button>
        <!-- Divider -->
        <div class="w-px h-8 bg-slate-200"></div>
        <!-- User -->
        <div class="flex items-center gap-3 cursor-pointer group">
          <img src="https://picsum.photos/seed/user/100/100" alt="Profile" class="w-10 h-10 rounded-xl object-cover ring-2 ring-white shadow-sm" />
          <div class="flex flex-col leading-tight">
            <span class="font-bold text-sm text-slate-800 group-hover:text-teal-700 transition-colors">
        {{ $user->name }}
    </span>
            <span class="text-[11px] text-slate-400 font-medium">Client Account</span>
          </div>
          <i class="ri-arrow-down-s-line text-slate-400 text-lg"></i>
        </div>
      </div>
    </header>

    <!-- ── WELCOME + CTA ── -->
    <section class="flex justify-between items-center mb-8 anim-2">
      <div>
        <p class="text-xs font-bold text-teal-600 mb-1 tracking-widest uppercase">Welcome back</p>
        <h1 class="font-display text-4xl font-extrabold text-slate-900 tracking-tight leading-none">Hi, {{ explode(' ', trim($user->name))[0] }}! 👋</h1>
        <p class="text-slate-500 text-sm mt-2">Here's what's happening with your projects today.</p>
      </div>
      <button class="btn-cta flex items-center gap-3 pl-2 pr-6 py-2 rounded-full font-display font-bold text-white text-sm border-none cursor-pointer"
        style="background: linear-gradient(135deg, #0f766e 0%, #10B981 100%); box-shadow: 0 10px 28px rgba(15,118,110,0.28);">
        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-lg">
          <i class="ri-add-line"></i>
        </div>
        Post New Project
      </button>
    </section>

    <!-- ── STATS ── -->
   <section class="grid grid-cols-3 gap-5 mb-8 anim-3">
  <div class="stat-card relative bg-white rounded-3xl border border-slate-200 p-6 overflow-hidden">
    <div class="flex items-start justify-between mb-4">
      <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl" style="background:#ccfbf1; color:#0f766e">
        <i class="ri-folder-line"></i>
      </div>
    </div>
    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Active Projects</p>
    <p class="font-display text-[2.4rem] font-extrabold text-slate-900 leading-none">
        {{ $activeProjects }}
    </p>
  </div>

  <div class="stat-card relative bg-white rounded-3xl border border-slate-200 p-6 overflow-hidden">
    <div class="flex items-start justify-between mb-4">
      <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl" style="background:#fef3c7; color:#d97706">
        <i class="ri-money-dollar-circle-line"></i>
      </div>
      <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">All time</span>
    </div>
    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Total Spent</p>
    <p class="font-display text-[2.0rem] font-extrabold text-slate-900 leading-none">
        Rp{{ number_format($totalSpent / 1000, 1) }}K
    </p>
    </div>

  <div class="stat-card relative bg-white rounded-3xl border border-slate-200 p-6 overflow-hidden">
    <div class="flex items-start justify-between mb-4">
      <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl" style="background:#d1fae5; color:#065f46">
        <i class="ri-checkbox-circle-line"></i>
      </div>
    </div>
    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Completed</p>
    <p class="font-display text-[2.4rem] font-extrabold text-slate-900 leading-none">
        {{ $completedProjects }}
    </p>
  </div>
</section>

    <!-- ── LATEST PROJECTS ── -->
    <section class="anim-4">
      <div class="flex justify-between items-center mb-5">
        <div>
          <h2 class="font-display text-xl font-extrabold text-slate-900">Latest Projects</h2>
          <p class="text-slate-400 text-xs mt-0.5">Your 3 most recent projects</p>
        </div>
        <button class="flex items-center gap-1.5 text-sm font-bold text-teal-700 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 px-4 py-2 rounded-xl transition-all border-none cursor-pointer">
          View All <i class="ri-arrow-right-line"></i>
        </button>
      </div>
      <div id="project-grid" class="grid gap-4" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))"></div>
    </section>

  </main>
</div>

<script>
  // ─── DATA ───
  const projects = {!! json_encode($projects->map(function($order) {
        return [
            'title'  => $order->service->title ?? 'Order #' . $order->id, 
            'client' => auth()->guard('client')->user()->name,
            'amount' => 'Rp' . number_format($order->agreed_price ?? 0, 0, ',', '.'), 
            'status' => ucfirst($order->status ?? 'pending') 
        ];
    })) !!};

    // Log buat lo cek di Inspect Element -> Console
    console.log("Data Proyek Berhasil Dimuat:", projects);
  const hasUnreadMessages = true;

  // ─── BADGE CONFIG ───
  const BADGE_CONFIG = {
    'In Progress': { cls: 'bg-teal-50 text-teal-700',   icon: 'ri-loader-4-line',        bar: '#0f766e' },
    'Pending':     { cls: 'bg-amber-50 text-amber-700', icon: 'ri-time-line',             bar: '#f59e0b' },
    'Completed':   { cls: 'bg-emerald-50 text-emerald-700', icon: 'ri-checkbox-circle-line', bar: '#10b981' },
  };

  const PROJECT_ICONS = {
    'Website Redesign':       'ri-layout-line',
    'Mobile App Development': 'ri-smartphone-line',
    'SEO Optimization':       'ri-bar-chart-line',
  };

  // ─── NOTIFIKASI ───
  function initNotification() {
    const notifBtn = document.getElementById('notif-btn');
    if (!notifBtn) return;
    if (hasUnreadMessages) notifBtn.classList.add('has-unread');
    notifBtn.addEventListener('click', () => notifBtn.classList.remove('has-unread'));
  }

  // ─── RENDER PROJECT CARDS ───
  function renderProjects() {
    const grid = document.getElementById('project-grid');
    if (!grid) return;

    if (!projects || projects.length === 0) {
      grid.innerHTML = `
        <div class="col-span-full flex flex-col items-center justify-center py-16 px-5 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-center mt-2.5">
          <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-5xl text-slate-400 mb-4">
            <i class="ri-folder-open-line"></i>
          </div>
          <h3 class="font-display text-[1.2rem] text-slate-900 mb-2">No projects found</h3>
          <p class="text-slate-500 text-[14px] max-w-xs leading-relaxed mb-6">It seems you don't have any active projects right now.</p>
          <button class="btn-empty bg-teal-deep text-white border-none px-6 py-3 rounded-full font-bold cursor-pointer transition-all font-display">Post New Project</button>
        </div>`;
      return;
    }

    grid.innerHTML = projects.map(p => {
      const b = BADGE_CONFIG[p.status] ?? BADGE_CONFIG['Pending'];
      const icon = PROJECT_ICONS[p.title] ?? 'ri-code-line';
      return `
      <div class="project-card bg-white rounded-3xl border border-slate-200 p-6 flex flex-col gap-4 cursor-default">
        <!-- Top row -->
        <div class="flex items-start justify-between">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl" style="background:#f0fdf4; color:#0f766e">
            <i class="${icon}"></i>
          </div>
          <span class="flex items-center gap-1.5 text-[11px] font-bold px-3 py-1.5 rounded-full ${b.cls}">
            <i class="${b.icon} text-[11px]"></i>${p.status}
          </span>
        </div>
        <!-- Title & client -->
        <div>
          <h3 class="font-display font-extrabold text-slate-900 text-[1.05rem] leading-snug mb-1">${p.title}</h3>
          <p class="text-slate-400 text-xs font-medium flex items-center gap-1.5">
            <i class="ri-building-line text-[11px]"></i>${p.client}
          </p>
        </div>
        <!-- Divider + amount -->
        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
          <div class="flex items-center gap-1.5 text-slate-400 text-xs">
            <i class="ri-wallet-3-line"></i>
            <span>Budget</span>
          </div>
          <span class="font-display font-extrabold text-slate-900 text-sm">${p.amount}</span>
        </div>
      </div>`;
    }).join('');
  }

  // ─── INIT ───
  document.addEventListener('DOMContentLoaded', () => {
    initNotification();
    renderProjects();
  });
</script>

</body>
</html>