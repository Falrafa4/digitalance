<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Management | Digitalance</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap"
      rel="stylesheet" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"
      rel="stylesheet" />

    <style>
/* ─── VARIABLES ─── */
:root {
  --primary: #0F766E;
  --primary-light: #10B981;
  --teal-deep: #0f766e;
  --orange: #f97316;
  --slate-900: #0f172a;
  --slate-800: #1e293b;
  --slate-500: #64748b;
  --slate-400: #94a3b8;
  --slate-200: #e2e8f0;
  --bg-light: #f8fafc;
  --white: #ffffff;
  --font-display: 'Sora', sans-serif;
  --font-sans: 'Plus Jakarta Sans', sans-serif;
  --radius-24: 24px;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  background: var(--bg-light);
  color: var(--slate-900);
  font-family: var(--font-sans);
  height: 100vh;
  overflow: hidden;
}

.app { display: flex; height: 100vh; }


/* ─── SIDEBAR ─── */
.sidebar {
  width: 260px;
  min-width: 260px;
  padding: 36px 20px;
  background: var(--white);
  border-right: 1px solid var(--slate-200);
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.sidebar-header {
  display: flex;
  justify-content: center;
  margin-bottom: 44px;
}

.logo { display: flex; align-items: center; gap: 10px; }
.logo span {
  font-family: var(--font-display);
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--teal-deep);
}

/* Nav scrollable area */
.nav-scroll {
  flex: 1;
  overflow-y: auto;
  scrollbar-width: none;
}
.nav-scroll::-webkit-scrollbar { display: none; }

.nav { display: flex; flex-direction: column; gap: 2px; }

.nav-item {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 11px 14px;
  border: none;
  background: none;
  border-radius: 11px;
  color: var(--slate-500);
  cursor: pointer;
  font-weight: 600;
  font-size: 13.5px;
  font-family: var(--font-sans);
  transition: all 0.2s;
  width: 100%;
  text-align: left;
}

.nav-item i { font-size: 17px; flex-shrink: 0; }

.nav-item.active {
  background: var(--teal-deep);
  color: var(--white);
  box-shadow: 0 6px 18px rgba(15, 118, 110, 0.22);
}

.nav-item:hover:not(.active) { background: #f1f5f9; color: var(--teal-deep); }
.nav-item.text-danger { color: #ef4444; }
.nav-item.text-danger:hover { background: #fef2f2; color: #dc2626; }

.sidebar-footer { margin-top: auto; }
.nav-divider { height: 1px; background: var(--slate-200); margin: 14px 0; }
.copyright {
  font-size: 10.5px;
  color: var(--slate-400);
  text-align: center;
  margin-top: 20px;
  line-height: 1.6;
}


/* ─── MAIN ─── */
.main {
  flex: 1;
  padding: 32px 48px;
  overflow-y: auto;
  min-width: 0;
  position: relative;
}


/* ─── HEADER ─── */
.main-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 36px;
}

.search-wrap { position: relative; width: 340px; }

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--slate-400);
  font-size: 17px;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 12px 44px;
  background: var(--white);
  border: 1.5px solid var(--slate-200);
  border-radius: 13px;
  font-size: 13.5px;
  font-family: var(--font-sans);
  outline: none;
  transition: all 0.2s;
  color: var(--slate-900);
}

.search-input::placeholder { color: var(--slate-400); }
.search-input:focus {
  border-color: var(--teal-deep);
  box-shadow: 0 3px 14px rgba(15, 118, 110, 0.1);
}

.header-right { display: flex; align-items: center; gap: 14px; }

.btn-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  border: 1.5px solid var(--slate-200);
  background: var(--white);
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--slate-500);
  font-size: 19px;
  transition: 0.2s;
}

.btn-icon:hover { border-color: var(--teal-deep); color: var(--teal-deep); }

/* Dot notif */
.notif-dot {
  position: absolute;
  top: 9px;
  right: 9px;
  width: 8px;
  height: 8px;
  background: var(--orange);
  border: 2px solid var(--white);
  border-radius: 50%;
  display: none;
}
.btn-icon.has-unread .notif-dot { display: block; }

.user-card { display: flex; align-items: center; gap: 11px; cursor: pointer; }
.user-avatar { width: 42px; height: 42px; border-radius: 11px; object-fit: cover; }
.user-info { display: flex; flex-direction: column; }
.user-name { font-weight: 700; font-size: 13.5px; color: var(--slate-800); }
.user-role { font-size: 11px; color: var(--slate-500); }


/* ─── WELCOME ─── */
.welcome {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 32px;
}

.welcome h1 {
  font-family: var(--font-display);
  font-size: 2.1rem;
  font-weight: 800;
  color: var(--slate-900);
}
.welcome p { color: var(--slate-500); font-size: 0.95rem; margin-top: 4px; }


/* ─── STATS ─── */
.stats-grid {
  display: grid;
  gap: 24px;
  margin-bottom: 40px;
}

.stat-card {
  background: var(--white);
  padding: 28px 32px;
  border-radius: var(--radius-24);
  border: 1px solid var(--slate-200);
  transition: 0.3s;
}
.stat-card:hover { border-color: var(--primary-light); transform: translateY(-2px); }
.stat-label {
  color: var(--slate-500);
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  margin-bottom: 8px;
  display: block;
}
.stat-value {
  font-family: var(--font-display);
  font-size: 2rem;
  font-weight: 800;
  color: var(--slate-900);
}


/* ─── SECTIONS ─── */
.projects-section { margin-bottom: 40px; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.section-title { font-family: var(--font-display); font-size: 1.3rem; font-weight: 700; }
.btn-link {
  background: none;
  border: none;
  color: var(--primary);
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-family: var(--font-sans);
  transition: gap 0.2s;
}
.btn-link:hover { gap: 10px; }


/* ─── PROJECT CARDS ─── */
.project-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
.project-card {
  background: var(--white);
  padding: 24px;
  border-radius: var(--radius-24);
  border: 1px solid var(--slate-200);
  transition: 0.3s;
  display: flex;
  flex-direction: column;
}
.project-card:hover { transform: translateY(-5px); border-color: var(--primary-light); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }

.card-title-text { font-family: var(--font-display); font-size: 1.1rem; margin-bottom: 20px; color: var(--slate-900); line-height: 1.4; }
.card-footer-item { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--slate-200); padding-top: 16px; margin-top: auto; }
.client-name { font-size: 13px; color: var(--slate-500); font-weight: 500; }
.project-amount { font-weight: 800; color: var(--teal-deep); font-size: 1.1rem; }


/* ─── BADGES ─── */
.badge { padding: 5px 12px; border-radius: 100px; font-size: 11px; font-weight: 700; margin-bottom: 16px; align-self: flex-start; display: inline-block; }
.badge-progress { background: #CCFBF1; color: #0F766E; }
.badge-pending  { background: #FFEDD5; color: #9A3412; }
.badge-completed{ background: #D1FAE5; color: #065F46; }
.badge-new      { background: #E0F2FE; color: #0369A1; }


/* ─── APPROVAL CARDS ─── */
.approval-card {
  background: var(--white);
  border: 1px solid var(--slate-200);
  border-radius: 16px;
  padding: 18px 20px;
  transition: box-shadow 0.2s, border-color 0.2s;
  animation: fadeUp 0.4s ease both;
}
.approval-card:hover {
  border-color: var(--slate-400);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
}

/* Approved / Rejected state colors */
.approval-card.card-approved { border-color: #10B981; background: #f0fdf9; }
.approval-card.card-rejected { border-color: #ef4444; background: #fef2f2; }

.approval-card-top {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-bottom: 16px;
}

.approval-avatar {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  object-fit: cover;
  flex-shrink: 0;
  border: 2px solid var(--slate-200);
}

.approval-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.approval-name {
  font-weight: 700;
  font-size: 14px;
  color: var(--slate-900);
}
.approval-meta {
  font-size: 12px;
  color: var(--slate-500);
  display: flex;
  align-items: center;
  gap: 5px;
}
.approval-meta i { font-size: 12px; }
.approval-time {
  font-size: 11px;
  color: var(--slate-400);
  display: flex;
  align-items: center;
  gap: 4px;
}
.approval-time i { font-size: 11px; }

/* Override badge margin inside approval card */
.approval-card .badge {
  margin-bottom: 0;
  flex-shrink: 0;
  align-self: flex-start;
}

.approval-actions {
  display: flex;
  gap: 10px;
  padding-top: 14px;
  border-top: 1px solid var(--slate-200);
}

.btn-approve,
.btn-reject {
  flex: 1;
  padding: 9px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  font-family: var(--font-sans);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  border: none;
  transition: all 0.2s;
}

.btn-approve {
  background: #d1fae5;
  color: #065f46;
}
.btn-approve:hover {
  background: #10b981;
  color: var(--white);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-reject {
  background: #fee2e2;
  color: #991b1b;
}
.btn-reject:hover {
  background: #ef4444;
  color: var(--white);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* Feedback setelah aksi */
.action-feedback {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 700;
  padding: 9px 0;
}
.action-feedback.approved { color: #059669; }
.action-feedback.rejected { color: #dc2626; }


/* ─── EMPTY STATE ─── */
.empty-state {
  grid-column: 1 / -1;
  text-align: center;
  padding: 48px 20px;
  background: var(--white);
  border: 2px dashed var(--slate-200);
  border-radius: var(--radius-24);
}
.empty-icon { font-size: 44px; color: var(--slate-400); margin-bottom: 12px; display: flex; align-items: center; justify-content: center; }
.empty-state h3 { font-family: var(--font-display); font-size: 1.15rem; color: var(--slate-900); margin-bottom: 6px; font-weight: 700; }
.empty-state p { color: var(--slate-500); font-size: 13.5px; margin-bottom: 20px; }

.btn-empty {
  background: var(--teal-deep); color: var(--white); border: none;
  padding: 11px 24px; border-radius: 100px; font-weight: 700; cursor: pointer; transition: 0.3s;
  font-family: var(--font-sans);
}
.btn-empty:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15, 118, 110, 0.2); }


/* ─── STATS CONTAINER (2x2 grid style) ─── */
.stats-container {
  display: grid; grid-template-columns: 1fr 1fr; background: var(--white);
  border-radius: var(--radius-24); border: 1px solid var(--slate-200);
  overflow: hidden; margin-bottom: 40px;
}
.stat-item {
  padding: 32px; display: flex; flex-direction: column; align-items: center;
  justify-content: center; text-align: center;
  border-right: 1px solid var(--slate-200); border-bottom: 1px solid var(--slate-200);
}
.stat-item:nth-child(2n) { border-right: none; }
.stat-item:nth-child(n+3) { border-bottom: none; }


/* ─── ACTION BUTTONS ─── */
.action-buttons { display: flex; gap: 24px; margin-top: 20px; margin-bottom: 40px; }
.btn-large {
  flex: 1; padding: 20px; border-radius: 20px; background: var(--white);
  border: 2px solid var(--slate-200); font-family: var(--font-display);
  font-weight: 700; font-size: 16px; color: var(--slate-500);
  cursor: pointer; transition: 0.3s; text-align: center;
}
.btn-large:hover { background: var(--bg-light); border-color: var(--primary); color: var(--primary); transform: translateY(-2px); }


/* ─── PRIMARY ACTION BUTTON ─── */
.btn-primary-action {
  background: linear-gradient(135deg, var(--teal-deep) 0%, var(--primary-light) 100%);
  color: var(--white); border: none; padding: 8px 28px 8px 8px; border-radius: 100px;
  display: flex; align-items: center; gap: 16px; cursor: pointer;
  font-family: var(--font-display); font-weight: 700;
  box-shadow: 0 10px 25px rgba(15, 118, 110, 0.25);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.btn-primary-action .icon-circle {
  width: 44px; height: 44px; background: rgba(255, 255, 255, 0.2);
  border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;
}
.btn-primary-action:hover { transform: translateY(-4px); box-shadow: 0 15px 30px rgba(15, 118, 110, 0.35); }


/* ─── BLOB DECORATION ─── */
.blob { position: absolute; filter: blur(100px); z-index: -1; opacity: 0.3; pointer-events: none; }
.blob-1 { top: 0; right: 0; width: 400px; height: 400px; background: var(--primary-light); }


/* ─── ANIMATIONS ─── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

.anim-welcome { animation: fadeUp 0.6s ease both; }
.anim-stat-0  { animation: fadeUp 0.6s 0.1s ease both; }


/* ════════════════════════════════════════ */
/* ─── USER PAGE ─── */
/* ════════════════════════════════════════ */

/* ─── PAGE HEADER ─── */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 28px;
  animation: fadeUp 0.5s ease both;
}
.page-header-left h1 {
  font-family: var(--font-display);
  font-size: 1.9rem;
  font-weight: 800;
  color: var(--slate-900);
}
.page-header-left p {
  color: var(--slate-500);
  font-size: 0.9rem;
  margin-top: 4px;
}

/* ─── FILTER TABS ─── */
.filter-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 28px;
  animation: fadeUp 0.5s 0.05s ease both;
}
.filter-tab {
  padding: 8px 18px;
  border-radius: 100px;
  border: 1.5px solid var(--slate-200);
  background: var(--white);
  color: var(--slate-500);
  font-size: 13px;
  font-weight: 600;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.2s;
}
.filter-tab:hover { border-color: var(--teal-deep); color: var(--teal-deep); }
.filter-tab.active {
  background: var(--teal-deep);
  border-color: var(--teal-deep);
  color: var(--white);
  box-shadow: 0 4px 14px rgba(15, 118, 110, 0.2);
}

/* ─── USER CARD GRID ─── */
.user-card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 20px;
  animation: fadeUp 0.5s 0.1s ease both;
}

/* ─── USER CARD ITEM ─── */
.user-card-item {
  background: var(--white);
  border: 1px solid var(--slate-200);
  border-radius: 20px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 0;
  transition: all 0.25s;
  cursor: default;
  position: relative;
  overflow: hidden;
}
.user-card-item::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--teal-deep), var(--primary-light));
  opacity: 0;
  transition: opacity 0.25s;
}
.user-card-item:hover {
  border-color: var(--primary-light);
  box-shadow: 0 8px 28px rgba(15, 118, 110, 0.1);
  transform: translateY(-3px);
}
.user-card-item:hover::before { opacity: 1; }

.user-card-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}
.user-card-avatar-wrap {
  position: relative;
}
.user-card-avatar {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  object-fit: cover;
  border: 2px solid var(--slate-200);
  display: block;
}
.user-online-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid var(--white);
  position: absolute;
  bottom: -2px;
  right: -2px;
}
.user-online-dot.online  { background: #22c55e; }
.user-online-dot.offline { background: var(--slate-400); }
.user-online-dot.suspended { background: #ef4444; }

/* badge tanpa margin bawah untuk card */
.badge-inline {
  margin-bottom: 0;
  align-self: auto;
}

.user-card-name {
  font-family: var(--font-display);
  font-size: 1rem;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 4px;
}
.user-card-role {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: var(--slate-500);
  font-weight: 600;
  margin-bottom: 6px;
}
.user-card-role i { font-size: 12px; color: var(--teal-deep); }
.user-card-location {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11.5px;
  color: var(--slate-400);
  margin-bottom: 14px;
}
.user-card-location i { font-size: 11px; }

.user-card-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 16px;
}
.skill-chip {
  padding: 4px 10px;
  background: #f1f5f9;
  border-radius: 100px;
  font-size: 11px;
  font-weight: 600;
  color: var(--slate-500);
}

.user-card-stats {
  display: flex;
  gap: 0;
  border-top: 1px solid var(--slate-200);
  padding-top: 14px;
  margin-bottom: 14px;
}
.user-stat-item {
  flex: 1;
  text-align: center;
}
.user-stat-item + .user-stat-item {
  border-left: 1px solid var(--slate-200);
}
.user-stat-value {
  font-family: var(--font-display);
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--slate-900);
  display: block;
}
.user-stat-label {
  font-size: 10.5px;
  color: var(--slate-400);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

.btn-detail {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: 1.5px solid var(--slate-200);
  background: transparent;
  color: var(--slate-500);
  font-size: 13px;
  font-weight: 700;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}
.btn-detail:hover {
  background: var(--teal-deep);
  border-color: var(--teal-deep);
  color: var(--white);
}


/* ─── MODAL OVERLAY ─── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(4px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s;
}
.modal-overlay.open {
  opacity: 1;
  pointer-events: all;
}

.modal-box {
  background: var(--white);
  border-radius: 28px;
  width: 100%;
  max-width: 560px;
  max-height: 88vh;
  overflow-y: auto;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.18);
  transform: scale(0.94) translateY(16px);
  transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
  scrollbar-width: none;
}
.modal-box::-webkit-scrollbar { display: none; }
.modal-overlay.open .modal-box {
  transform: scale(1) translateY(0);
}

/* ─── MODAL HERO ─── */
.modal-hero {
  height: 110px;
  background: linear-gradient(135deg, var(--teal-deep) 0%, var(--primary-light) 100%);
  border-radius: 28px 28px 0 0;
  position: relative;
  flex-shrink: 0;
}
.modal-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  border: none;
  color: var(--white);
  font-size: 18px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}
.modal-close:hover { background: rgba(255,255,255,0.35); }

.modal-avatar-wrap {
  position: absolute;
  bottom: -28px;
  left: 28px;
}
.modal-avatar {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  object-fit: cover;
  border: 4px solid var(--white);
  box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  display: block;
}

/* ─── MODAL BODY ─── */
.modal-body {
  padding: 44px 28px 28px;
}
.modal-name {
  font-family: var(--font-display);
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 4px;
}
.modal-role-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}
.modal-bio {
  font-size: 13.5px;
  color: var(--slate-500);
  line-height: 1.65;
  margin-bottom: 20px;
}

/* Modal stats bar */
.modal-stats {
  display: flex;
  background: var(--bg-light);
  border-radius: 14px;
  overflow: hidden;
  margin-bottom: 20px;
}
.modal-stat {
  flex: 1;
  padding: 14px;
  text-align: center;
}
.modal-stat + .modal-stat { border-left: 1px solid var(--slate-200); }
.modal-stat-value {
  font-family: var(--font-display);
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--slate-900);
  display: block;
}
.modal-stat-label {
  font-size: 10.5px;
  color: var(--slate-400);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

/* Modal info list */
.modal-info-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 20px;
}
.modal-info-row {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13.5px;
  color: var(--slate-600);
}
.modal-info-row i {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  color: var(--teal-deep);
  flex-shrink: 0;
}

/* Modal skills */
.modal-section-title {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: var(--slate-400);
  margin-bottom: 10px;
}
.modal-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 24px;
}
.modal-skill-chip {
  padding: 6px 14px;
  background: #CCFBF1;
  color: var(--teal-deep);
  border-radius: 100px;
  font-size: 12px;
  font-weight: 700;
}

/* Modal action buttons */
.modal-actions {
  display: flex;
  gap: 10px;
}
.modal-btn-primary {
  flex: 1;
  padding: 12px;
  background: var(--teal-deep);
  color: var(--white);
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 13.5px;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
}
.modal-btn-primary:hover {
  background: #0b6057;
  box-shadow: 0 6px 18px rgba(15,118,110,0.25);
}
.modal-btn-danger {
  padding: 12px 20px;
  background: #fee2e2;
  color: #dc2626;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 13.5px;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}
.modal-btn-danger:hover { background: #dc2626; color: var(--white); }

/* ─── ADD USER MODAL (modal-content style) ─── */
.modal-content {
  background: var(--white);
  border-radius: 24px;
  width: 100%;
  max-width: 480px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.18);
  padding: 32px;
  transform: scale(0.94) translateY(16px);
  transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
  scrollbar-width: none;
}
.modal-content::-webkit-scrollbar { display: none; }
.modal-overlay.open .modal-content {
  transform: scale(1) translateY(0);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}
.modal-header h2 {
  font-family: var(--font-display);
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--slate-900);
}
.close-modal {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: 1.5px solid var(--slate-200);
  background: var(--white);
  color: var(--slate-500);
  font-size: 18px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.close-modal:hover { border-color: var(--teal-deep); color: var(--teal-deep); }

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
}
.form-group label {
  font-size: 12.5px;
  font-weight: 700;
  color: var(--slate-500);
  text-transform: uppercase;
  letter-spacing: 0.4px;
}
.form-group input,
.form-group select {
  padding: 11px 14px;
  border: 1.5px solid var(--slate-200);
  border-radius: 11px;
  font-size: 13.5px;
  font-family: var(--font-sans);
  color: var(--slate-900);
  background: var(--white);
  outline: none;
  transition: all 0.2s;
}
.form-group input::placeholder { color: var(--slate-400); }
.form-group input:focus,
.form-group select:focus {
  border-color: var(--teal-deep);
  box-shadow: 0 3px 14px rgba(15, 118, 110, 0.1);
}

.modal-actions {
  display: flex;
  gap: 10px;
  margin-top: 8px;
}
.btn-primary {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 11px 20px;
  background: var(--teal-deep);
  color: var(--white);
  border: none;
  border-radius: 11px;
  font-size: 13.5px;
  font-weight: 700;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.2s;
}
.btn-primary:hover {
  background: #0b6057;
  box-shadow: 0 6px 18px rgba(15, 118, 110, 0.25);
}
.btn-secondary {
  flex: 1;
  padding: 11px 20px;
  background: var(--bg-light);
  color: var(--slate-500);
  border: 1.5px solid var(--slate-200);
  border-radius: 11px;
  font-size: 13.5px;
  font-weight: 700;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.2s;
}
.btn-secondary:hover { border-color: var(--slate-400); color: var(--slate-900); }

/* ─── page-header flex fix ─── */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}
.page-header-right {
  flex-shrink: 0;
}

/* ─── filter-tabs wrap on small screens ─── */
.filter-tabs {
  flex-wrap: wrap;
}


/* ─── CUSTOM ROLE SELECT ─── */
.custom-role-select {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 4px;
}
.role-option {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  border: 1.5px solid var(--slate-200);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s;
  background: var(--white);
  position: relative;
}
.role-option:hover {
  border-color: var(--teal-deep);
  background: #f0fdf9;
}
.role-option.selected {
  border-color: var(--teal-deep);
  background: #f0fdf9;
  box-shadow: 0 3px 12px rgba(15,118,110,0.1);
}
.role-option-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #e0f2fe;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: var(--teal-deep);
  flex-shrink: 0;
  transition: background 0.2s;
}
.role-option.selected .role-option-icon {
  background: var(--teal-deep);
  color: var(--white);
}
.role-option-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.role-option-label {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--slate-900);
}
.role-option-desc {
  font-size: 11.5px;
  color: var(--slate-400);
}
.role-option-check {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 1.5px solid var(--slate-200);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  color: transparent;
  transition: all 0.2s;
  flex-shrink: 0;
}
.role-option.selected .role-option-check {
  background: var(--teal-deep);
  border-color: var(--teal-deep);
  color: var(--white);
}

/* ─── CARD SKILLS GROW (push btn-detail to bottom) ─── */
.user-card-item {
  display: flex;
  flex-direction: column;
}
.card-skills-grow {
  flex: 1;
  align-content: flex-start;
}
.skill-chip-empty {
  color: var(--slate-400) !important;
  font-style: italic;
}

/* ─── MODAL ACTION GROUPS ─── */
.modal-action-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.modal-action-row {
  display: flex;
  gap: 10px;
}
.modal-btn-edit {
  flex: 0 0 auto;
  padding: 12px 18px;
  background: #eff6ff;
  color: #1d4ed8;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 13.5px;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 7px;
}
.modal-btn-edit:hover { background: #1d4ed8; color: var(--white); }

.modal-btn-warning {
  flex: 1;
  padding: 12px;
  background: #fef3c7;
  color: #92400e;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 13.5px;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
}
.modal-btn-warning:hover { background: #f59e0b; color: var(--white); }

.modal-btn-delete {
  flex: 1;
  padding: 12px;
  background: #fee2e2;
  color: #dc2626;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 13.5px;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
}
.modal-btn-delete:hover { background: #dc2626; color: var(--white); }

/* modal-btn-primary needs flex:1 in action-row context */
.modal-action-row .modal-btn-primary { flex: 1; }

/* ─── CONFIRM DELETE OVERLAY ─── */
.confirm-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s;
}
.confirm-overlay.open {
  opacity: 1;
  pointer-events: all;
}
.confirm-box {
  background: var(--white);
  border-radius: 24px;
  padding: 36px 32px;
  max-width: 400px;
  width: 100%;
  text-align: center;
  box-shadow: 0 24px 64px rgba(0,0,0,0.2);
  transform: scale(0.93) translateY(12px);
  transition: transform 0.28s cubic-bezier(0.34,1.56,0.64,1);
}
.confirm-overlay.open .confirm-box {
  transform: scale(1) translateY(0);
}
.confirm-icon {
  font-size: 48px;
  color: #dc2626;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.confirm-box h3 {
  font-family: var(--font-display);
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 10px;
}
.confirm-box p {
  font-size: 13.5px;
  color: var(--slate-500);
  line-height: 1.6;
  margin-bottom: 28px;
}
.confirm-actions {
  display: flex;
  gap: 10px;
}
.confirm-actions .btn-secondary { flex: 1; }
.confirm-actions .modal-btn-delete { flex: 1; }

/* textarea focus */
.form-group textarea:focus {
  border-color: var(--teal-deep);
  box-shadow: 0 3px 14px rgba(15,118,110,0.1);
  outline: none;
}
</style>
  </head>
  <body>
    <div class="app">
      <!-- ── SIDEBAR ── -->
      <aside class="sidebar">
        <div class="sidebar-header">
          <div class="logo">
            <svg
              width="32"
              height="32"
              viewBox="0 0 32 32"
              fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <rect width="32" height="32" rx="10" fill="url(#logo-gradient)" />
              <path
                d="M16 7L25 11.5V20.5L16 25L7 20.5V11.5L16 7Z"
                fill="white" />
              <defs>
                <linearGradient
                  id="logo-gradient"
                  x1="0"
                  y1="0"
                  x2="32"
                  y2="32">
                  <stop stop-color="#0F766E" />
                  <stop offset="1" stop-color="#10B981" />
                </linearGradient>
              </defs>
            </svg>
            <span>Digitalance</span>
          </div>
        </div>

            <nav class="nav nav-scroll">
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="ri-dashboard-fill"></i> Dashboard
      </a>
      <a href="{{ route('admin.clients.index') }}" class="nav-item {{ request()->routeIs('admin.clients.*','admin.freelancers.*','admin.skomda-students.*') ? 'active' : '' }}">
        <i class="ri-user-line"></i> User
      </a>
      <a href="{{ route('admin.super') }}" class="nav-item {{ request()->routeIs('admin.super*') ? 'active' : '' }}">
        <i class="ri-user-star-line"></i> Admin
      </a>
      <span class="nav-item" style="opacity:0.4;cursor:not-allowed;justify-content:space-between"><span><i class="ri-file-list-3-line"></i> Orders</span><span style="font-size:9px;background:#f1f5f9;color:#94a3b8;padding:2px 6px;border-radius:99px;font-weight:700">Soon</span></span>
      <span class="nav-item" style="opacity:0.4;cursor:not-allowed;justify-content:space-between"><span><i class="ri-star-line"></i> Reviews</span><span style="font-size:9px;background:#f1f5f9;color:#94a3b8;padding:2px 6px;border-radius:99px;font-weight:700">Soon</span></span>
      <a href="{{ route('admin.services') }}" class="nav-item {{ request()->routeIs('admin.services*','admin.service-categories*') ? 'active' : '' }}">
        <i class="ri-tools-line"></i> Services
      </a>
      <a href="{{ route('admin.transactions') }}" class="nav-item {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
        <i class="ri-bank-card-line"></i> Transactions
      </a>
      <span class="nav-item" style="opacity:0.4;cursor:not-allowed;justify-content:space-between"><span><i class="ri-folder-user-line"></i> Portofolios</span><span style="font-size:9px;background:#f1f5f9;color:#94a3b8;padding:2px 6px;border-radius:99px;font-weight:700">Soon</span></span>
      <span class="nav-item" style="opacity:0.4;cursor:not-allowed;justify-content:space-between"><span><i class="ri-price-tag-3-line"></i> Offers</span><span style="font-size:9px;background:#f1f5f9;color:#94a3b8;padding:2px 6px;border-radius:99px;font-weight:700">Soon</span></span>
      <span class="nav-item" style="opacity:0.4;cursor:not-allowed;justify-content:space-between"><span><i class="ri-hammer-line"></i> Working</span><span style="font-size:9px;background:#f1f5f9;color:#94a3b8;padding:2px 6px;border-radius:99px;font-weight:700">Soon</span></span>
      <span class="nav-item" style="opacity:0.4;cursor:not-allowed;justify-content:space-between"><span><i class="ri-discuss-line"></i> Negotiations</span><span style="font-size:9px;background:#f1f5f9;color:#94a3b8;padding:2px 6px;border-radius:99px;font-weight:700">Soon</span></span>
    </nav>

            <div class="sidebar-footer">
      <div class="nav-divider"></div>
      <nav class="nav">
        <a href="{{ url('admin/profile') }}" class="nav-item"><i class="ri-user-line"></i> Account</a>
        <span class="nav-item" style="opacity:0.4;cursor:not-allowed;justify-content:space-between"><span><i class="ri-settings-3-line"></i> Settings</span><span style="font-size:9px;background:#f1f5f9;color:#94a3b8;padding:2px 6px;border-radius:99px;font-weight:700">Soon</span></span>
        <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none">@csrf</form>
        <button onclick="document.getElementById('logout-form').submit();" class="nav-item text-danger">
          <i class="ri-logout-box-line"></i> Logout
        </button>
      </nav>
      <div class="copyright">&copy; 2026 Digitalance.<br/>All rights reserved.</div>
    </div>
      </aside>

      <!-- ── MAIN ── -->
      <main class="main">
        <!-- Header -->
        <header class="main-header">
          <div class="search-wrap">
            <i class="ri-search-line search-icon"></i>
            <i
              class="ri-equalizer-line"
              style="
                position: absolute;
                right: 16px;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                cursor: pointer;
              "></i>
            <input
              class="search-input"
              id="user-search-input"
              type="text"
              placeholder="Cari nama, email, lokasi..." />
          </div>

                    <div class="header-right">
            <button class="btn-icon" id="notif-btn">
              <i class="ri-notification-3-line"></i>
              <span class="notif-dot"></span>
            </button>
            <div class="user-card">
              <img class="user-avatar" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0f766e&color=fff" alt="Admin" />
              <div class="user-info">
                <span class="user-name">{{ Auth::user()->name }}</span>
                <span class="user-role">System Manager</span>
              </div>
            </div>
          </div>
        </header>

        <!-- Page Title -->
        <div class="page-header">
          <div class="page-header-left">
            <h1>User Management</h1>
            <p>Kelola seluruh pengguna yang terdaftar di platform Digitalance.</p>
          </div>
          <div class="page-header-right">
            <button class="btn-primary" id="btn-add-user">
              <i class="ri-user-add-line"></i> Add New User
            </button>
          </div>
        </div>

        <div class="modal-overlay" id="modal-add-user">
          <div class="modal-content">
            <div class="modal-header">
              <h2>Add New User</h2>
              <button class="close-modal" id="btn-close-add-user">
                <i class="ri-close-line"></i>
              </button>
            </div>
            <form id="form-add-user">
              <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="new-user-name" required placeholder="e.g. John Doe" />
              </div>
              <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="new-user-email" required placeholder="john@example.com" />
              </div>
              <div class="form-group">
                <label>Role</label>
                <div class="custom-role-select" id="add-role-select"></div>
                <input type="hidden" id="new-user-role" value="Client" />
              </div>
              <div class="form-group">
                <label>Location</label>
                <input type="text" id="new-user-location" required placeholder="e.g. Jakarta, Indonesia" />
              </div>
              <div class="modal-actions">
                <button type="button" class="btn-secondary" id="btn-cancel-add-user">Cancel</button>
                <button type="submit" class="btn-primary">Save User</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
          <button class="filter-tab active" data-filter="all">All</button>
          <button class="filter-tab" data-filter="Active">Active</button>
          <button class="filter-tab" data-filter="Inactive">Inactive</button>
          <button class="filter-tab" data-filter="Suspended">Suspended</button>
          <button class="filter-tab" data-filter="Client">Client</button>
          <button class="filter-tab" data-filter="Freelancer">
            Freelancer
          </button>
          <button class="filter-tab" data-filter="Skomda Student">
            Skomda Students
          </button>
        </div>

        <div class="user-card-grid" id="user-card-grid"></div>
      </main>
    </div>

    <div class="modal-overlay" id="user-modal-overlay">
      <div class="modal-box" id="user-modal-box"></div>
    </div>

    <script>
// ─── PENGATURAN NOTIFIKASI ADMIN ───
const hasUnreadMessages = {{ (isset($pendingVerifications) && count($pendingVerifications ?? []) > 0) ? "true" : "false" }};

// ─── BADGE MAP ───
const BADGE_MAP = {
  'Pending':   'badge-pending',
  'Approved':  'badge-completed',
  'Rejected':  'badge-new',
  'In Review': 'badge-progress',
};

// pendingVerifications handled by blade

// ─── RENDER PENDING VERIFICATIONS ───
function renderPendingVerifications() {
  const grid = document.getElementById('admin-approval-grid');
  if (!grid) return;

  if (!pendingVerifications || pendingVerifications.length === 0) {
    grid.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon"><i class="ri-shield-check-line"></i></div>
        <h3>All clear!</h3>
        <p>No pending verifications at the moment. Everything is up to date.</p>
      </div>`;
    return;
  }

  grid.innerHTML = pendingVerifications.map(v => `
    <div class="approval-card" data-id="${v.id}">
      <div class="approval-card-top">
        <img class="approval-avatar" src="${v.avatar}" alt="${v.name}" />
        <div class="approval-info">
          <span class="approval-name">${v.name}</span>
          <span class="approval-meta">
            <i class="ri-briefcase-line"></i> ${v.role} &middot; ${v.skill}
          </span>
          <span class="approval-time">
            <i class="ri-time-line"></i> Submitted ${v.submittedAt}
          </span>
        </div>
        <span class="badge ${BADGE_MAP[v.status] ?? 'badge-pending'}">${v.status}</span>
      </div>
      <div class="approval-actions">
        <button class="btn-approve" onclick="handleApprove(${v.id})">
          <i class="ri-check-line"></i> Approve
        </button>
        <button class="btn-reject" onclick="handleReject(${v.id})">
          <i class="ri-close-line"></i> Reject
        </button>
      </div>
    </div>
  `).join('');
}

// ─── HANDLE APPROVE ───
function handleApprove(id) {
  const card = document.querySelector(`.approval-card[data-id="${id}"]`);
  if (!card) return;
  card.classList.add('card-approved');
  card.querySelector('.approval-actions').innerHTML = `
    <span class="action-feedback approved">
      <i class="ri-check-double-line"></i> Approved
    </span>`;
  setTimeout(() => {
    card.style.transition = 'opacity 0.3s, transform 0.3s';
    card.style.opacity = '0';
    card.style.transform = 'translateX(20px)';
    setTimeout(() => card.remove(), 320);
  }, 1200);
}

// ─── HANDLE REJECT ───
function handleReject(id) {
  const card = document.querySelector(`.approval-card[data-id="${id}"]`);
  if (!card) return;
  card.classList.add('card-rejected');
  card.querySelector('.approval-actions').innerHTML = `
    <span class="action-feedback rejected">
      <i class="ri-close-circle-line"></i> Rejected
    </span>`;
  setTimeout(() => {
    card.style.transition = 'opacity 0.3s, transform 0.3s';
    card.style.opacity = '0';
    card.style.transform = 'translateX(20px)';
    setTimeout(() => card.remove(), 320);
  }, 1200);
}

// ─── INIT NOTIFIKASI ───
function initAdminDashboard() {
  initSidebarNav();
  renderPendingVerifications();
}

// ─── INIT SIDEBAR NAV ───
// Nav item tidak bisa pindah active karena belum ada path/halaman yang terhubung.
// Active state hanya diset via class di HTML secara manual sesuai halaman aktif.
function initSidebarNav() {
  const navItems = document.querySelectorAll('.nav-item');
  navItems.forEach(item => {
    // Cegah perpindahan active state saat diklik
    item.addEventListener('click', (e) => {
      if (!item.classList.contains('active')) {
        e.preventDefault();
        // Tidak ada aksi — hanya hover yang berfungsi
      }
    });
  });
}

// Jalankan ketika DOM siap
document.addEventListener('DOMContentLoaded', initAdminDashboard);


// ════════════════════════════════════════
// ─── USER PAGE ───
// ════════════════════════════════════════


// ─── DATA DARI DB ───
const clientsData = @json($clientsData);
const freelancersData = @json($freelancersData);
const skomdaData = @json($skomdaData);
const usersData = [].concat(
  clientsData.map(function(u){return Object.assign({},u,{_uid:'c_'+u.id});}),
  freelancersData.map(function(u){return Object.assign({},u,{_uid:'f_'+u.id});}),
  skomdaData.map(function(u){return Object.assign({},u,{_uid:'s_'+u.id});})
);

// ─── MAPS ───
const USER_STATUS_MAP = {
  'Active':    'badge-completed',
  'Inactive':  'badge-pending',
  'Suspended': 'badge-new',
};
const USER_DOT_MAP = {
  'Active':    'online',
  'Inactive':  'offline',
  'Suspended': 'suspended',
};
const ROLE_ICON_MAP = {
  'Client':       'ri-briefcase-line',
  'Freelancer':     'ri-user-star-line',
  'Skomda Student': 'ri-graduation-cap-line',
};

// ─── RENDER USER CARDS ───
function renderUserCards(data = usersData) {
  const grid = document.getElementById('user-card-grid');
  if (!grid) return;

  if (!data || data.length === 0) {
    grid.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon"><i class="ri-user-search-line"></i></div>
        <h3>No users found</h3>
        <p>Try adjusting the filter or search keyword.</p>
      </div>`;
    return;
  }

  grid.innerHTML = data.map(u => `
    <div class="user-card-item">
      <div class="user-card-top">
        <div class="user-card-avatar-wrap">
          <img class="user-card-avatar" src="${u.avatar}" alt="${u.name}" />
          <span class="user-online-dot ${USER_DOT_MAP[u.status] ?? 'offline'}"></span>
        </div>
        <span class="badge ${USER_STATUS_MAP[u.status] ?? 'badge-pending'} badge-inline">${u.status}</span>
      </div>

      <h3 class="user-card-name">${u.name}</h3>
      <span class="user-card-role">
        <i class="${ROLE_ICON_MAP[u.role] ?? 'ri-user-line'}"></i> ${u.role}
      </span>
      <span class="user-card-location">
        <i class="ri-map-pin-line"></i> ${u.location}
      </span>

      <div class="user-card-skills card-skills-grow">
        ${u.skills.length > 0
          ? u.skills.map(s => `<span class="skill-chip">${s}</span>`).join('')
          : '<span class="skill-chip skill-chip-empty">No skills listed</span>'}
      </div>

      <div class="user-card-stats">
        <div class="user-stat-item">
          <span class="user-stat-value">${u.totalOrders}</span>
          <span class="user-stat-label">Orders</span>
        </div>
        <div class="user-stat-item">
          <span class="user-stat-value">${u.totalEarning}</span>
          <span class="user-stat-label">Earned</span>
        </div>
      </div>

      <button class="btn-detail" onclick="openUserModal('${u._uid}')">
        <i class="ri-eye-line"></i> Lihat Profil
      </button>
    </div>
  `).join('');
}

// ─── OPEN MODAL ───
function openUserModal(id) {
  const u = usersData.find(u => u._uid === id);
  if (!u) return;

  const overlay = document.getElementById('user-modal-overlay');
  const box     = document.getElementById('user-modal-box');

  const isSuspended = u.status === 'Suspended';
  const suspendLabel = isSuspended ? 'Unsuspend' : 'Suspend';
  const suspendIcon  = isSuspended ? 'ri-checkbox-circle-line' : 'ri-forbid-line';
  const suspendClass = isSuspended ? 'modal-btn-warning' : 'modal-btn-danger';

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeUserModal()">
        <i class="ri-close-line"></i>
      </button>
      <div class="modal-avatar-wrap">
        <img class="modal-avatar" src="${u.avatar}" alt="${u.name}" />
      </div>
    </div>

    <div class="modal-body">
      <h2 class="modal-name">${u.name}</h2>
      <div class="modal-role-row">
        <span class="badge ${USER_STATUS_MAP[u.status] ?? 'badge-pending'} badge-inline">${u.status}</span>
        <span class="user-card-role" style="margin:0">
          <i class="${ROLE_ICON_MAP[u.role] ?? 'ri-user-line'}"></i> ${u.role}
        </span>
      </div>
      <p class="modal-bio">${u.bio}</p>

      <div class="modal-stats">
        <div class="modal-stat">
          <span class="modal-stat-value">${u.totalOrders}</span>
          <span class="modal-stat-label">Orders</span>
        </div>
        <div class="modal-stat">
          <span class="modal-stat-value">${u.totalEarning}</span>
          <span class="modal-stat-label">Earned</span>
        </div>
        <div class="modal-stat">
          <span class="modal-stat-value">${u.lastActive}</span>
          <span class="modal-stat-label">Last Active</span>
        </div>
      </div>

      <div class="modal-info-list">
        <div class="modal-info-row">
          <i class="ri-mail-line"></i>
          <span>${u.email}</span>
        </div>
        <div class="modal-info-row">
          <i class="ri-phone-line"></i>
          <span>${u.phone}</span>
        </div>
        <div class="modal-info-row">
          <i class="ri-map-pin-line"></i>
          <span>${u.location}</span>
        </div>
        <div class="modal-info-row">
          <i class="ri-calendar-line"></i>
          <span>Bergabung ${u.joinDate}</span>
        </div>
      </div>

      <p class="modal-section-title">Skills</p>
      <div class="modal-skills">
        ${u.skills.length > 0
          ? u.skills.map(s => `<span class="modal-skill-chip">${s}</span>`).join('')
          : '<span style="font-size:13px;color:var(--slate-400)">No skills listed</span>'}
      </div>

      <div class="modal-action-group">
        <div class="modal-action-row">
          <button class="modal-btn-primary" onclick="closeUserModal()">
            <i class="ri-mail-send-line"></i> Kirim Pesan
          </button>
          <button class="modal-btn-edit" onclick="openEditUserModal('${u._uid}')">
            <i class="ri-edit-line"></i> Edit
          </button>
        </div>
        <div class="modal-action-row">
          <button class="${suspendClass}" onclick="toggleSuspendUser('${u._uid}')">
            <i class="${suspendIcon}"></i> ${suspendLabel}
          </button>
          <button class="modal-btn-delete" onclick="confirmDeleteUser('${u._uid}')">
            <i class="ri-delete-bin-line"></i> Hapus Akun
          </button>
        </div>
      </div>
    </div>
  `;

  overlay.classList.add('open');
}

// ─── CLOSE MODAL ───
function closeUserModal() {
  document.getElementById('user-modal-overlay').classList.remove('open');
}

// ─── TOGGLE SUSPEND ───
function toggleSuspendUser(id) {
  const u = usersData.find(u => u._uid === id);
  if (!u) return;
  u.status = u.status === 'Suspended' ? 'Active' : 'Suspended';
  refreshAfterChange();
  openUserModal(id); // re-render modal with new state
}

// ─── CONFIRM DELETE ───
function confirmDeleteUser(id) {
  const u = usersData.find(u => u._uid === id);
  if (!u) return;

  // Inject confirm overlay
  const confirmEl = document.createElement('div');
  confirmEl.className = 'confirm-overlay';
  confirmEl.id = 'confirm-delete-overlay';
  confirmEl.innerHTML = `
    <div class="confirm-box">
      <div class="confirm-icon"><i class="ri-error-warning-line"></i></div>
      <h3>Hapus Akun?</h3>
      <p>Akun <strong>${u.name}</strong> akan dihapus secara permanen dan tidak bisa dipulihkan.</p>
      <div class="confirm-actions">
        <button class="btn-secondary" onclick="closeConfirmDelete()">Batal</button>
        <button class="modal-btn-delete" onclick="executeDeleteUser(${id})">
          <i class="ri-delete-bin-line"></i> Ya, Hapus
        </button>
      </div>
    </div>
  `;
  document.body.appendChild(confirmEl);
  requestAnimationFrame(() => confirmEl.classList.add('open'));
}

function closeConfirmDelete() {
  const el = document.getElementById('confirm-delete-overlay');
  if (el) { el.classList.remove('open'); setTimeout(() => el.remove(), 250); }
}

function executeDeleteUser(id) {
  const idx = usersData.findIndex(u => u._uid === id);
  if (idx !== -1) usersData.splice(idx, 1);
  closeConfirmDelete();
  closeUserModal();
  refreshAfterChange();
}

// ─── OPEN EDIT MODAL ───
function openEditUserModal(id) {
  const u = usersData.find(u => u._uid === id);
  if (!u) return;

  // Remove existing if any
  const existing = document.getElementById('edit-user-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'edit-user-overlay';
  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit User</h2>
        <button class="close-modal" onclick="closeEditUserModal()">
          <i class="ri-close-line"></i>
        </button>
      </div>
      <form id="form-edit-user">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" id="edit-user-name" required value="${u.name}" />
        </div>
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" id="edit-user-email" required value="${u.email}" />
        </div>
        <div class="form-group">
          <label>Role</label>
          <div class="custom-role-select" id="edit-role-select">
            ${buildRoleOptions(u.role)}
          </div>
          <input type="hidden" id="edit-user-role" value="${u.role}" />
        </div>
        <div class="form-group">
          <label>Location</label>
          <input type="text" id="edit-user-location" required value="${u.location}" />
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" id="edit-user-phone" value="${u.phone}" />
        </div>
        <div class="form-group">
          <label>Bio</label>
          <textarea id="edit-user-bio" rows="3" style="padding:11px 14px;border:1.5px solid var(--slate-200);border-radius:11px;font-family:var(--font-sans);font-size:13.5px;resize:vertical;outline:none;transition:all 0.2s;">${u.bio}</textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" onclick="closeEditUserModal()">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>
  `;
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  // Role select logic
  el.querySelectorAll('.role-option').forEach(opt => {
    opt.addEventListener('click', () => {
      el.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
      document.getElementById('edit-user-role').value = opt.dataset.value;
    });
  });

  // Close on backdrop click
  el.addEventListener('click', (e) => { if (e.target === el) closeEditUserModal(); });

  // Submit
  document.getElementById('form-edit-user').addEventListener('submit', (e) => {
    e.preventDefault();
    u.name     = document.getElementById('edit-user-name').value.trim();
    u.email    = document.getElementById('edit-user-email').value.trim();
    u.role     = document.getElementById('edit-user-role').value;
    u.location = document.getElementById('edit-user-location').value.trim();
    u.phone    = document.getElementById('edit-user-phone').value.trim();
    u.bio      = document.getElementById('edit-user-bio').value.trim();
    closeEditUserModal();
    refreshAfterChange();
    openUserModal(u._uid); // re-open with updated info
  });
}

function closeEditUserModal() {
  const el = document.getElementById('edit-user-overlay');
  if (el) { el.classList.remove('open'); setTimeout(() => el.remove(), 280); }
}

// ─── HELPER: build role options HTML ───
function buildRoleOptions(selected) {
  const roles = [
    { value: 'Client',        icon: 'ri-briefcase-line',       label: 'Client',        desc: 'Pemberi kerja / klien' },
    { value: 'Freelancer',    icon: 'ri-user-star-line',        label: 'Freelancer',    desc: 'Penyedia jasa independen' },
    { value: 'Skomda Student',icon: 'ri-graduation-cap-line',   label: 'Skomda Student',desc: 'Siswa program Skomda' },
  ];
  return roles.map(r => `
    <div class="role-option${r.value === selected ? ' selected' : ''}" data-value="${r.value}">
      <div class="role-option-icon"><i class="${r.icon}"></i></div>
      <div class="role-option-text">
        <span class="role-option-label">${r.label}</span>
        <span class="role-option-desc">${r.desc}</span>
      </div>
      <div class="role-option-check"><i class="ri-check-line"></i></div>
    </div>
  `).join('');
}

// ─── REFRESH CARDS after data change ───
function refreshAfterChange() {
  const activeTab = document.querySelector('.filter-tab.active');
  const filter    = activeTab ? activeTab.dataset.filter : 'all';
  const q         = (document.getElementById('user-search-input')?.value || '').toLowerCase();
  let filtered    = filter === 'all' ? usersData : usersData.filter(u => u.status === filter || u.role === filter);
  if (q) filtered = filtered.filter(u =>
    u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q) ||
    u.role.toLowerCase().includes(q)  || u.location.toLowerCase().includes(q)
  );
  renderUserCards(filtered);
}

// ─── FILTER TABS ───
function initUserFilters() {
  const tabs = document.querySelectorAll('.filter-tab');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const filter = tab.dataset.filter;
      const filtered = filter === 'all'
        ? usersData
        : usersData.filter(u => u.status === filter || u.role === filter);
      renderUserCards(filtered);
    });
  });
}

// ─── SEARCH USER ───
function initUserSearch() {
  const input = document.getElementById('user-search-input');
  if (!input) return;
  input.addEventListener('input', () => {
    const q = input.value.toLowerCase();
    const filtered = usersData.filter(u =>
      u.name.toLowerCase().includes(q) ||
      u.email.toLowerCase().includes(q) ||
      u.role.toLowerCase().includes(q) ||
      u.location.toLowerCase().includes(q)
    );
    renderUserCards(filtered);
  });
}

// ─── ADD USER MODAL ───
function openAddUserModal() {
  document.getElementById('modal-add-user').classList.add('open');
}

function closeAddUserModal() {
  const modal = document.getElementById('modal-add-user');
  modal.classList.remove('open');
  document.getElementById('form-add-user').reset();
  // reset custom role select
  document.querySelectorAll('#add-role-select .role-option').forEach((opt, i) => {
    opt.classList.toggle('selected', i === 0);
  });
  document.getElementById('new-user-role').value = 'Client';
}

function initAddUserModal() {
  // Inject custom role select into add-role-select container
  const roleContainer = document.getElementById('add-role-select');
  if (roleContainer) {
    roleContainer.innerHTML = buildRoleOptions('Client');
    roleContainer.querySelectorAll('.role-option').forEach(opt => {
      opt.addEventListener('click', () => {
        roleContainer.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
        document.getElementById('new-user-role').value = opt.dataset.value;
      });
    });
  }

  const btnAdd    = document.getElementById('btn-add-user');
  const btnClose  = document.getElementById('btn-close-add-user');
  const btnCancel = document.getElementById('btn-cancel-add-user');
  const overlay   = document.getElementById('modal-add-user');
  const form      = document.getElementById('form-add-user');

  if (btnAdd)    btnAdd.addEventListener('click', openAddUserModal);
  if (btnClose)  btnClose.addEventListener('click', closeAddUserModal);
  if (btnCancel) btnCancel.addEventListener('click', closeAddUserModal);

  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeAddUserModal();
    });
  }

  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const name     = document.getElementById('new-user-name').value.trim();
      const email    = document.getElementById('new-user-email').value.trim();
      const role     = document.getElementById('new-user-role').value;
      const location = document.getElementById('new-user-location').value.trim();

      const newUser = {
        id:           Date.now(),
        name,
        email,
        avatar:       `https://picsum.photos/seed/${name.split(' ')[0].toLowerCase()}/200/200`,
        role,
        status:       'Active',
        joinDate:     new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
        location,
        phone:        '-',
        skills:       [],
        totalOrders:  0,
        totalEarning: '$0',
        lastActive:   'Just now',
        bio:          'No bio yet.',
      };

      usersData.push(newUser);
      refreshAfterChange();
      closeAddUserModal();
    });
  }
}

// ─── INIT USER PAGE ───
function initUserPage() {
  renderUserCards();
  initUserFilters();
  initUserSearch();
  initAddUserModal();
  initAdminDashboard();

  // Tutup user detail modal saat klik overlay
  const userOverlay = document.getElementById('user-modal-overlay');
  if (userOverlay) {
    userOverlay.addEventListener('click', (e) => {
      if (e.target === userOverlay) closeUserModal();
    });
  }

  // Notif
  const notifBtn = document.getElementById('notif-btn');
  if (notifBtn) {
    notifBtn.classList.toggle('has-unread', hasUnreadMessages);
    notifBtn.addEventListener('click', () => notifBtn.classList.remove('has-unread'));
  }
}

// Jalankan ketika DOM siap
document.addEventListener('DOMContentLoaded', initUserPage);
</script>
  </body>
</html>