<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Diarra Transport') – Gestion Bagages</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
:root {
  --dark:  #0e1621;
  --dark2: #162032;
  --dark3: #1e2d44;
  --gold:  #c9a84c;
  --gold2: #e8c97a;
  --green: #2b7a44;
  --green2:#3aab5e;
  --red:   #c0392b;
  --blue:  #1a6bbf;
  --text:  #eef2f7;
  --text2: #7a90a8;
  --border:rgba(201,168,76,.15);
  --sidebar-w: 255px;
  --topbar-h:  58px;
}
/* ── Transition douce mode jour/nuit ── */
body,body *:not(.spinner){transition:background-color .3s ease,color .2s ease,border-color .2s ease;}
/* ── Mode Jour ───────────────────────── */
body.day-mode{
  --dark:#f0f4f8;--dark2:#ffffff;--dark3:#e8edf3;
  --dark4:#dde3eb;--gold:#b8922a;--text:#1a2332;--text2:#5a6a7e;
  --border:rgba(184,146,42,.2);
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);min-height:100vh;}
a{text-decoration:none;color:inherit;}
/* ── Topbar ──────────────────────────── */
.topbar{
  position:fixed;top:0;left:0;right:0;height:var(--topbar-h);
  background:var(--dark2);border-bottom:1px solid var(--border);
  display:flex;align-items:center;z-index:200;
}
.topbar-hamburger{
  width:var(--topbar-h);height:var(--topbar-h);border:none;
  background:none;border-right:1px solid var(--border);
  color:var(--text2);font-size:1.4rem;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:background .2s;flex-shrink:0;
}
.topbar-hamburger:hover{background:var(--dark3);color:var(--gold);}
.topbar-brand{display:flex;align-items:center;gap:10px;padding:0 16px;flex:1;}
.topbar-logo{width:36px;height:36px;flex-shrink:0;}
.topbar-brand-text .t1{font-size:.95rem;font-weight:700;color:var(--gold);letter-spacing:.05em;}
.topbar-brand-text .t2{font-size:.7rem;color:var(--text2);margin-top:-2px;}
.topbar-right{display:flex;align-items:center;gap:6px;padding-right:12px;}
.topbar-btn{
  width:36px;height:36px;border:none;background:none;
  color:var(--text2);font-size:1rem;cursor:pointer;border-radius:8px;
  display:flex;align-items:center;justify-content:center;transition:.2s;
}
.topbar-btn:hover{background:var(--dark3);color:var(--gold);}
</style>
<style>
.user-chip{
  display:flex;align-items:center;gap:8px;padding:4px 10px;
  border-radius:8px;background:var(--dark3);border:1px solid var(--border);
  cursor:pointer;position:relative;user-select:none;
}
.user-chip:hover{background:var(--dark3);border-color:var(--gold);}
.user-avatar{
  width:28px;height:28px;border-radius:50%;background:var(--gold);
  color:#0e1621;font-weight:700;font-size:.8rem;
  display:flex;align-items:center;justify-content:center;
}
.user-name{font-size:.8rem;color:var(--text);font-weight:500;white-space:nowrap;}
.user-chevron{font-size:.6rem;color:var(--text2);margin-left:2px;transition:transform .2s;}
.user-chip.open .user-chevron{transform:rotate(180deg);}
/* ── Dropdown user ───────────────────── */
.user-dropdown{
  position:absolute;top:52px;right:12px;
  background:#ffffff;border:1px solid #e0e0e0;
  border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,.18);
  min-width:210px;z-index:500;display:none;overflow:hidden;
}
.user-dropdown.open{display:block;}
.ud-header{
  padding:14px 16px;background:#fafafa;
  border-bottom:1px solid #f0f0f0;
  display:flex;align-items:center;gap:10px;
}
.ud-avatar{
  width:36px;height:36px;border-radius:50%;
  background:#1a6bbf;color:white;
  display:flex;align-items:center;justify-content:center;
  font-weight:700;font-size:.9rem;flex-shrink:0;
}
.ud-name{font-size:13px;font-weight:700;color:#1a2332;line-height:1.2;}
.ud-role{font-size:11px;color:#7a90a8;margin-top:1px;}
.ud-item{
  display:flex;align-items:center;gap:12px;
  padding:13px 16px;cursor:pointer;
  color:#2c3e50;font-size:13px;
  border-bottom:1px solid #f5f5f5;transition:background .15s;
}
.ud-item:last-child{border-bottom:none;}
.ud-item:hover{background:#f5f7fa;}
.ud-icon{
  width:28px;height:28px;border-radius:50%;background:#f0f4f8;
  display:flex;align-items:center;justify-content:center;
  font-size:.9rem;flex-shrink:0;
}
.ud-item.logout{color:#c0392b;}
.ud-item.logout .ud-icon{background:#fdf0ef;}
/* ── Bouton thème ────────────────────── */
.icon-btn{
  width:34px;height:34px;border:none;background:none;border-radius:8px;
  color:var(--text2);font-size:1rem;cursor:pointer;
  display:flex;align-items:center;justify-content:center;transition:.2s;
}
.icon-btn:hover{background:var(--dark3);color:var(--gold);}
.btn-logout{
  width:34px;height:34px;border:none;background:none;border-radius:8px;
  color:var(--text2);font-size:1.1rem;cursor:pointer;
  display:flex;align-items:center;justify-content:center;transition:.2s;
}
.btn-logout:hover{background:rgba(192,57,43,.2);color:var(--red);}
/* ── Sidebar ─────────────────────────── */
.sidebar{
  position:fixed;top:var(--topbar-h);left:0;bottom:0;
  width:var(--sidebar-w);background:var(--dark2);
  border-right:1px solid var(--border);overflow-y:auto;
  z-index:100;transition:transform .3s;
}
.sidebar::-webkit-scrollbar{width:4px;}
.sidebar::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px;}
.sidebar-group{padding:10px 0;}
.sidebar-group-label{
  font-size:.65rem;font-weight:600;color:var(--text2);
  letter-spacing:.1em;text-transform:uppercase;
  padding:6px 16px 4px;
}
.sidebar-item{
  display:flex;align-items:center;gap:10px;padding:9px 16px;
  font-size:.82rem;color:var(--text2);cursor:pointer;
  transition:.15s;border-left:3px solid transparent;
  user-select:none;
}
.sidebar-item:hover{background:var(--dark3);color:var(--text);}
.sidebar-item.active{
  background:rgba(201,168,76,.08);border-left-color:var(--gold);
  color:var(--gold);font-weight:500;
}
.sidebar-item .icon{font-size:1rem;width:20px;text-align:center;}
.sidebar-item .arrow{margin-left:auto;font-size:.7rem;transition:transform .25s;}
.sidebar-item.open .arrow{transform:rotate(180deg);}
.sidebar-sub{display:none;overflow:hidden;}
.sidebar-sub.show{display:block;}
.sidebar-sub a{
  display:flex;align-items:center;gap:8px;
  padding:7px 16px 7px 44px;font-size:.78rem;
  color:var(--text2);transition:.15s;border-left:3px solid transparent;
}
.sidebar-sub a:hover{background:var(--dark3);color:var(--text);}
.sidebar-sub a.active{color:var(--gold);background:rgba(201,168,76,.06);border-left-color:var(--gold);}
/* ── Main layout ─────────────────────── */
.main-wrapper{
  margin-left:var(--sidebar-w);padding-top:var(--topbar-h);
  min-height:100vh;transition:margin-left .3s;
}
.main-content{padding:24px;}

/* ── Apple Mobile Tab Bar & Drawer Styles ── */
.apple-tab-bar {
  display: none;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 300;
  height: calc(58px + max(env(safe-area-inset-bottom, 0px), 8px));
  padding-bottom: max(env(safe-area-inset-bottom, 0px), 8px);
  background: rgba(14, 22, 33, 0.88);
  backdrop-filter: blur(24px) saturate(180%);
  -webkit-backdrop-filter: blur(24px) saturate(180%);
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.25);
  justify-content: space-around;
  align-items: center;
  padding-left: 6px;
  padding-right: 6px;
}
body.day-mode .apple-tab-bar {
  background: rgba(255, 255, 255, 0.88);
  border-top: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.05);
}
.apple-tab-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 1;
  min-width: 0;
  height: 52px;
  color: var(--text2);
  text-decoration: none;
  position: relative;
  cursor: pointer;
  background: none;
  border: none;
  padding: 0;
  transition: transform 0.15s ease, color 0.15s ease;
  user-select: none;
  -webkit-tap-highlight-color: transparent;
}
.apple-tab-item:active {
  transform: scale(0.9);
}
.apple-tab-item .tab-icon {
  font-size: 1.25rem;
  line-height: 1;
  margin-bottom: 3px;
  position: relative;
  transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.apple-tab-item .tab-label {
  font-size: 0.68rem;
  font-weight: 500;
  letter-spacing: -0.01em;
  line-height: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}
.apple-tab-item.active {
  color: var(--gold);
}
.apple-tab-item.active .tab-icon {
  transform: translateY(-1px);
  filter: drop-shadow(0 2px 6px rgba(201, 168, 76, 0.4));
}
.apple-tab-item.active .tab-label {
  font-weight: 700;
}
.apple-tab-item.active::after {
  content: '';
  position: absolute;
  bottom: 2px;
  width: 14px;
  height: 3px;
  border-radius: 2px;
  background: var(--gold);
  box-shadow: 0 1px 4px rgba(201, 168, 76, 0.5);
}

/* Bouton surélevé central Apple (Nouveau Colis) */
.apple-tab-item.apple-tab-primary {
  position: relative;
  top: -12px;
}
.apple-tab-item.apple-tab-primary .primary-btn-circle {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--gold) 0%, var(--gold2) 100%);
  color: #0e1621;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  box-shadow: 0 4px 16px rgba(201, 168, 76, 0.45), 0 0 0 3px var(--dark2);
  transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
}
body.day-mode .apple-tab-item.apple-tab-primary .primary-btn-circle {
  box-shadow: 0 4px 16px rgba(184, 146, 42, 0.35), 0 0 0 3px #ffffff;
}
.apple-tab-item.apple-tab-primary:active .primary-btn-circle {
  transform: scale(0.9);
}
.apple-tab-item.apple-tab-primary .tab-label {
  margin-top: 3px;
  font-weight: 700;
  color: var(--gold);
}
.apple-tab-item.apple-tab-primary::after {
  display: none !important;
}

/* ── Mobile Sidebar Drawer & Backdrop ── */
.sidebar-backdrop {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.65);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  z-index: 250;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.sidebar-backdrop.active {
  display: block;
  opacity: 1;
}
.sidebar-mobile-header {
  display: none;
}

/* ── Tableaux & Conteneurs fluides ── */
.table-responsive-dt {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: 8px;
}
.table-responsive-dt::-webkit-scrollbar {
  height: 5px;
}
.table-responsive-dt::-webkit-scrollbar-thumb {
  background: var(--border);
  border-radius: 4px;
}

@media(max-width:768px){
  :root {
    --topbar-h: 54px;
  }
  .apple-tab-bar {
    display: flex;
  }
  .topbar {
    height: 54px;
  }
  .topbar-brand {
    padding: 0 8px;
    gap: 8px;
  }
  .topbar-logo {
    width: 32px;
    height: 32px;
  }
  .topbar-brand-text .t1 {
    font-size: .85rem;
    letter-spacing: 0;
  }
  .topbar-brand-text .t2 {
    display: none;
  }
  .user-name {
    display: none;
  }
  .user-chip {
    padding: 3px 6px;
  }
  .topbar-btn[title="Recherche"] {
    display: none;
  }
  .sidebar {
    top: 0;
    bottom: 0;
    width: min(85vw, 300px);
    z-index: 260;
    transform: translateX(-100%);
    transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: none;
    border-right: 1px solid var(--border);
  }
  .sidebar.mobile-open {
    transform: translateX(0);
    box-shadow: 10px 0 40px rgba(0, 0, 0, 0.5);
  }
  .sidebar-mobile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
    background: var(--dark3);
  }
  .smh-brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .smh-t1 {
    font-size: .85rem;
    font-weight: 700;
    color: var(--gold);
  }
  .smh-t2 {
    font-size: .7rem;
    color: var(--text2);
  }
  .smh-close {
    width: 32px;
    height: 32px;
    border: none;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 8px;
    color: var(--text2);
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .smh-close:hover {
    color: var(--gold);
    background: var(--dark2);
  }
  .main-wrapper {
    margin-left: 0 !important;
    padding-top: 54px;
    padding-bottom: calc(75px + max(env(safe-area-inset-bottom, 0px), 8px)) !important;
  }
  .main-content {
    padding: 14px 12px !important;
  }
  /* Empêche le zoom auto désagréable sur iPhone Safari */
  .form-control, .form-select, input[type="text"], input[type="number"], input[type="date"], select {
    font-size: 16px !important;
  }
  /* Taille optimale tactile pour les boutons */
  .btn-gold, .btn-outline-gold, .btn-reset {
    min-height: 42px;
    font-size: .88rem;
  }
  .card-dt, .fc {
    padding: 16px 14px !important;
    border-radius: 12px;
  }
  .page-header {
    margin-bottom: 14px;
    padding-bottom: 12px;
  }
  .page-header h1 {
    font-size: 1.15rem;
  }
  .toast-container {
    bottom: calc(75px + env(safe-area-inset-bottom, 0px));
    right: 12px;
    left: 12px;
  }
  .toast-dt {
    min-width: unset;
    max-width: 100%;
  }
}
/* ── Page header ─────────────────────── */
.page-header{
  margin-bottom:20px;padding-bottom:16px;
  border-bottom:1px solid var(--border);
}
.page-header h1{font-size:1.25rem;font-weight:600;color:var(--text);}
.page-header p{font-size:.82rem;color:var(--text2);margin-top:4px;}
/* ── Cards ───────────────────────────── */
.card-dt{
  background:var(--dark2);border:1px solid var(--border);
  border-radius:12px;padding:20px;
}
.card-dt .card-title{
  font-size:.9rem;font-weight:600;color:var(--gold);
  margin-bottom:16px;display:flex;align-items:center;gap:8px;
}
/* ── Form elements ───────────────────── */
.form-control,.form-select{
  background:var(--dark3);border:1px solid var(--border);
  color:var(--text);font-family:'Outfit',sans-serif;
  border-radius:8px;font-size:.85rem;
}
.form-control:focus,.form-select:focus{
  background:var(--dark3);color:var(--text);
  border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.15);
}
.form-control::placeholder{color:var(--text2);}
.form-select option{background:var(--dark3);}
.form-label{font-size:.8rem;font-weight:500;color:var(--text2);margin-bottom:5px;}
/* ── Buttons ─────────────────────────── */
.btn-gold{
  background:linear-gradient(135deg,var(--gold),var(--gold2));
  color:#0e1621;font-weight:700;border:none;border-radius:8px;
  padding:10px 22px;font-size:.88rem;letter-spacing:.03em;
  transition:.2s;cursor:pointer;
}
.btn-gold:hover{filter:brightness(1.1);transform:translateY(-1px);}
.btn-outline-gold{
  background:transparent;border:1px solid var(--gold);
  color:var(--gold);border-radius:8px;padding:8px 16px;
  font-size:.85rem;transition:.2s;
}
.btn-outline-gold:hover{background:rgba(201,168,76,.1);}
</style>
<style>
/* ── Tables ──────────────────────────── */
.table-dt{width:100%;border-collapse:collapse;font-size:.82rem;}
.table-dt thead th{
  background:var(--dark3);color:var(--gold);font-weight:600;
  padding:11px 14px;border-bottom:1px solid var(--border);
  white-space:nowrap;font-size:.78rem;letter-spacing:.04em;text-transform:uppercase;
}
.table-dt tbody tr{border-bottom:1px solid rgba(255,255,255,.04);}
.table-dt tbody tr:hover{background:rgba(255,255,255,.03);}
.table-dt tbody td{padding:10px 14px;color:var(--text2);vertical-align:middle;}
.table-dt tbody td .mono{font-family:'Space Mono',monospace;color:var(--gold);font-size:.78rem;}
/* ── Badges statut ───────────────────── */
.badge-dt{
  display:inline-block;padding:3px 10px;border-radius:20px;
  font-size:.7rem;font-weight:600;letter-spacing:.03em;
}
.badge-success{background:rgba(43,122,68,.25);color:#4ade80;border:1px solid rgba(43,122,68,.4);}
.badge-info   {background:rgba(26,107,191,.25);color:#60a5fa;border:1px solid rgba(26,107,191,.4);}
.badge-warning{background:rgba(201,168,76,.2);color:var(--gold);border:1px solid rgba(201,168,76,.3);}
.badge-danger {background:rgba(192,57,43,.25);color:#f87171;border:1px solid rgba(192,57,43,.4);}
.badge-secondary{background:rgba(122,144,168,.15);color:var(--text2);border:1px solid rgba(122,144,168,.2);}
/* ── Stat cards ──────────────────────── */
.stat-card{
  background:var(--dark2);border:1px solid var(--border);
  border-radius:12px;padding:18px 20px;
  display:flex;flex-direction:column;gap:4px;
}
.stat-card .stat-label{font-size:.75rem;color:var(--text2);}
.stat-card .stat-value{font-size:1.6rem;font-weight:700;font-family:'Space Mono',monospace;}
.stat-card .stat-sub  {font-size:.72rem;color:var(--text2);}
.stat-gold  .stat-value{color:var(--gold);}
.stat-green .stat-value{color:#4ade80;}
.stat-blue  .stat-value{color:#60a5fa;}
.stat-red   .stat-value{color:#f87171;}
/* ── Toast ───────────────────────────── */
.toast-container{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;}
.toast-dt{
  background:var(--dark3);border:1px solid var(--border);
  border-radius:10px;padding:14px 18px;min-width:280px;max-width:360px;
  display:flex;align-items:flex-start;gap:12px;
  box-shadow:0 8px 32px rgba(0,0,0,.4);
  animation:slideIn .3s ease;
}
@keyframes slideIn{from{transform:translateX(100%);opacity:0;}to{transform:translateX(0);opacity:1;}}
.toast-dt .toast-icon{font-size:1.2rem;}
.toast-dt .toast-body{flex:1;}
.toast-dt .toast-title{font-weight:600;font-size:.85rem;color:var(--text);}
.toast-dt .toast-msg{font-size:.78rem;color:var(--text2);margin-top:2px;}
/* ── Misc ────────────────────────────── */
.tarif-box{
  background:var(--dark3);border-radius:8px;padding:12px 16px;
  text-align:center;border:1px solid var(--border);
}
.tarif-box .tarif-label{font-size:.7rem;color:var(--text2);text-transform:uppercase;letter-spacing:.05em;}
.tarif-box .tarif-value{font-family:'Space Mono',monospace;font-size:1.3rem;font-weight:700;color:#4ade80;}
.checkbox-known label{cursor:pointer;user-select:none;color:var(--text2);font-size:.85rem;}
</style>
@yield('styles')
</head>
<body>

{{-- ══ TOPBAR ══════════════════════════════════════════════════ --}}
<header class="topbar">
  <button class="topbar-hamburger" id="sidebarToggle" aria-label="Menu"><i class="bi bi-list"></i></button>
  <div class="topbar-brand">
    @include('partials.logo-svg', ['size'=>36, 'class'=>'topbar-logo'])
    <div class="topbar-brand-text">
      <div class="t1">DIARRA TRANSPORT</div>
      <div class="t2">Gestion des Bagages</div>
    </div>
  </div>
  <div class="topbar-right">
    <button class="topbar-btn" title="Recherche"><i class="bi bi-search"></i></button>

    {{-- Bouton mode Jour / Nuit --}}
    <div class="icon-btn" id="themeToggle" onclick="toggleTheme()" title="Mode Jour/Nuit">
      <span id="themeIcon"><i class="bi bi-moon-stars"></i></span>
    </div>

    {{-- User chip + dropdown --}}
    <div class="user-chip" id="userChip" onclick="toggleUserDropdown(event)">
      <div class="user-avatar">{{ auth()->user()->initiale }}</div>
      <span class="user-name">{{ auth()->user()->nom_complet }}</span>
      <span class="user-chevron"><i class="bi bi-chevron-down"></i></span>
    </div>

    {{-- Dropdown (fond blanc, hors dark) --}}
    <div class="user-dropdown" id="userDropdown">
      <div class="ud-header">
        <div class="ud-avatar">{{ auth()->user()->initiale }}</div>
        <div>
          <div class="ud-name">{{ auth()->user()->nom_complet }}</div>
          <div class="ud-role">{{ auth()->user()->role?->nom ?? 'Utilisateur' }}</div>
        </div>
      </div>
      <div class="ud-item" onclick="window.location='{{ route('profil.index') }}'">
        <div class="ud-icon"><i class="bi bi-person-circle"></i></div>
        <span>Mon Profile</span>
      </div>
      <form action="{{ route('logout') }}" method="POST" id="logoutForm">@csrf</form>
      <div class="ud-item logout" onclick="document.getElementById('logoutForm').submit()">
        <div class="ud-icon"><i class="bi bi-box-arrow-right"></i></div>
        <span>Deconnexion</span>
      </div>
    </div>
  </div>
</header>

{{-- Backdrop flou pour fermeture au toucher hors du drawer mobile --}}
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebarMobile()"></div>

{{-- ══ SIDEBAR ═════════════════════════════════════════════════ --}}
@php
  $user      = auth()->user();
  $roleSlug  = $user->role?->slug ?? '';
  $isAdmin   = $roleSlug === 'administrateur';
  $isAgent   = $roleSlug === 'agent_bagages';
  $isControl = $roleSlug === 'controleur';
  $canBagage = $isAdmin || $isAgent;
@endphp
<nav class="sidebar" id="sidebar">

  {{-- Header mobile du drawer --}}
  <div class="sidebar-mobile-header">
    <div class="smh-brand">
      @include('partials.logo-svg', ['size'=>28, 'class'=>'smh-logo'])
      <div>
        <div class="smh-t1">DIARRA TRANSPORT</div>
        <div class="smh-t2">{{ auth()->user()->role?->nom ?? 'Agent' }}</div>
      </div>
    </div>
    <button type="button" class="smh-close" onclick="closeSidebarMobile()" aria-label="Fermer"><i class="bi bi-x-lg"></i></button>
  </div>

  {{-- ── Tableau de Bord (admin uniquement, tout en haut) ─── --}}
  @if($isAdmin)
  <div class="sidebar-group">
    <a href="{{ route('admin.dashboard') }}" style="display:block">
      <div class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="icon"><i class="bi bi-bar-chart-line"></i></span> Tableau de Bord
      </div>
    </a>
  </div>
  @endif

  {{-- ── Bagages ────────────────────────────────────────────── --}}
  <div class="sidebar-group">
    <div class="sidebar-group-label">Bagages</div>
    <div class="sidebar-item {{ request()->routeIs('bagages.*') ? 'active' : '' }}" onclick="toggleMenu('menuBagages',this)">
      <span class="icon"><i class="bi bi-box-seam"></i></span> Bagages <span class="arrow"><i class="bi bi-chevron-down"></i></span>
    </div>
    <div class="sidebar-sub {{ request()->routeIs('bagages.*') ? 'show' : '' }}" id="menuBagages">
      @if($canBagage)
        <a href="{{ route('bagages.create') }}" class="{{ request()->routeIs('bagages.create') ? 'active' : '' }}"><i class="bi bi-box"></i> Enregistrer un Colis</a>
      @endif
      <a href="{{ route('bagages.index') }}" class="{{ request()->routeIs('bagages.index') ? 'active' : '' }}"><i class="bi bi-clipboard-data"></i> Colis Enregistrés</a>
      @if($canBagage)
        <a href="{{ route('embarquement.index') }}" class="{{ request()->routeIs('embarquement.*') ? 'active' : '' }}"><i class="bi bi-truck-front"></i> Embarquement</a>
      @endif
    </div>
  </div>

  {{-- ── État de Caisse (admin + agent uniquement) ──────────── --}}
  @if($canBagage)
  <div class="sidebar-group">
    <div class="sidebar-item {{ request()->routeIs('caisse.*') ? 'active' : '' }}" onclick="toggleMenu('menuCaisse',this)">
      <span class="icon"><i class="bi bi-cash-coin"></i></span> État de Caisse <span class="arrow"><i class="bi bi-chevron-down"></i></span>
    </div>
    <div class="sidebar-sub {{ request()->routeIs('caisse.*') ? 'show' : '' }}" id="menuCaisse">
      <a href="{{ route('caisse.ma_caisse') }}"  class="{{ request()->routeIs('caisse.ma_caisse')  ? 'active' : '' }}"><i class="bi bi-calendar-day"></i> Ma Caisse</a>
      <a href="{{ route('caisse.par_depart') }}" class="{{ request()->routeIs('caisse.par_depart') ? 'active' : '' }}"><i class="bi bi-truck-front"></i> Caisse par Départ</a>
      @if($isAdmin)
        <a href="{{ route('caisse.global') }}" class="{{ request()->routeIs('caisse.global') ? 'active' : '' }}"><i class="bi bi-bar-chart-line"></i> État Global</a>
      @endif
    </div>
  </div>
  @endif

  {{-- ── Gestion des Bagages ─────────────────────────────────── --}}
  @if($canBagage)
  <div class="sidebar-group">
    <div class="sidebar-item {{ request()->routeIs('gestion.*') ? 'active' : '' }}" onclick="toggleMenu('menuGestion',this)">
      <span class="icon"><i class="bi bi-folder2-open"></i></span> Gestion des Bagages <span class="arrow"><i class="bi bi-chevron-down"></i></span>
    </div>
    <div class="sidebar-sub {{ request()->routeIs('gestion.*') ? 'show' : '' }}" id="menuGestion">
      <a href="{{ route('bagages.index') }}"     class="{{ request()->routeIs('gestion.enregistres') ? 'active' : '' }}"><i class="bi bi-clipboard-data"></i> Colis Enregistrés</a>
      <a href="{{ route('gestion.embarques') }}" class="{{ request()->routeIs('gestion.embarques')   ? 'active' : '' }}"><i class="bi bi-check-circle"></i> Colis Embarqués</a>
    </div>
  </div>
  @endif

  {{-- ── Mon Profil ───────────────────────────────────────────── --}}
  <div class="sidebar-group">
    <a href="{{ route('profil.index') }}" style="display:block">
      <div class="sidebar-item {{ request()->routeIs('profil.*') ? 'active' : '' }}">
        <span class="icon"><i class="bi bi-person"></i></span> Mon Profil
      </div>
    </a>
  </div>

  {{-- ── Administration (admin uniquement) ──────────────────── --}}
  @if($isAdmin)
  <div class="sidebar-group">
    <div class="sidebar-item {{ request()->routeIs('admin.*') ? 'active' : '' }}" onclick="toggleMenu('menuAdmin',this)">
      <span class="icon"><i class="bi bi-shield-lock"></i></span> Administration Système <span class="arrow"><i class="bi bi-chevron-down"></i></span>
    </div>
    <div class="sidebar-sub {{ request()->routeIs('admin.*') ? 'show' : '' }}" id="menuAdmin">
      <a href="{{ route('admin.societe') }}"      class="{{ request()->routeIs('admin.societe')       ? 'active' : '' }}"><i class="bi bi-gear"></i> Paramètres</a>
      <a href="{{ route('admin.utilisateurs') }}" class="{{ request()->routeIs('admin.utilisateurs*') ? 'active' : '' }}"><i class="bi bi-people"></i> Utilisateurs Système</a>
      <a href="{{ route('admin.profils') }}"      class="{{ request()->routeIs('admin.profils')       ? 'active' : '' }}"><i class="bi bi-key"></i> Profils Utilisateurs</a>
      <a href="{{ route('admin.logs') }}"         class="{{ request()->routeIs('admin.logs')          ? 'active' : '' }}"><i class="bi bi-file-text"></i> Logs Système</a>
    </div>
  </div>
  @endif

</nav>

{{-- ══ MAIN ════════════════════════════════════════════════════ --}}
<div class="main-wrapper" id="mainWrapper">
  <div class="main-content">
    @yield('content')
  </div>
</div>

{{-- ══ TOAST CONTAINER ════════════════════════════════════════ --}}
<div class="toast-container" id="toastContainer"></div>

{{-- ══ APPLE MOBILE TAB BAR ════════════════════════════════════ --}}
<nav class="apple-tab-bar" id="appleTabBar" aria-label="Navigation Mobile">
  @if($isAdmin)
    <a href="{{ route('admin.dashboard') }}" class="apple-tab-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <div class="tab-icon"><i class="bi bi-speedometer2"></i></div>
      <span class="tab-label">Dashboard</span>
    </a>
    <a href="{{ route('bagages.index') }}" class="apple-tab-item {{ request()->routeIs('bagages.index') ? 'active' : '' }}">
      <div class="tab-icon"><i class="bi bi-box-seam"></i></div>
      <span class="tab-label">Colis</span>
    </a>
    <a href="{{ route('bagages.create') }}" class="apple-tab-item apple-tab-primary {{ request()->routeIs('bagages.create') ? 'active' : '' }}" aria-label="Nouveau Colis">
      <div class="primary-btn-circle" title="Enregistrer un nouveau colis">
        <i class="bi bi-plus-lg"></i>
      </div>
      <span class="tab-label">Nouveau</span>
    </a>
    <a href="{{ route('caisse.global') }}" class="apple-tab-item {{ request()->routeIs('caisse.*') ? 'active' : '' }}">
      <div class="tab-icon"><i class="bi bi-cash-stack"></i></div>
      <span class="tab-label">Caisse</span>
    </a>
    <button type="button" class="apple-tab-item apple-tab-btn" onclick="toggleSidebarMobile()" aria-label="Menu">
      <div class="tab-icon"><i class="bi bi-grid-fill"></i></div>
      <span class="tab-label">Menu</span>
    </button>
  @elseif($isAgent)
    <a href="{{ route('bagages.index') }}" class="apple-tab-item {{ request()->routeIs('bagages.index') ? 'active' : '' }}">
      <div class="tab-icon"><i class="bi bi-clipboard-data"></i></div>
      <span class="tab-label">Colis</span>
    </a>
    <a href="{{ route('embarquement.index') }}" class="apple-tab-item {{ request()->routeIs('embarquement.*') ? 'active' : '' }}">
      <div class="tab-icon"><i class="bi bi-truck-front"></i></div>
      <span class="tab-label">Embarq.</span>
    </a>
    <a href="{{ route('bagages.create') }}" class="apple-tab-item apple-tab-primary {{ request()->routeIs('bagages.create') ? 'active' : '' }}" aria-label="Nouveau Colis">
      <div class="primary-btn-circle" title="Enregistrer un nouveau colis">
        <i class="bi bi-plus-lg"></i>
      </div>
      <span class="tab-label">Nouveau</span>
    </a>
    <a href="{{ route('caisse.ma_caisse') }}" class="apple-tab-item {{ request()->routeIs('caisse.*') ? 'active' : '' }}">
      <div class="tab-icon"><i class="bi bi-wallet2"></i></div>
      <span class="tab-label">Ma Caisse</span>
    </a>
    <button type="button" class="apple-tab-item apple-tab-btn" onclick="toggleSidebarMobile()" aria-label="Menu">
      <div class="tab-icon"><i class="bi bi-grid-fill"></i></div>
      <span class="tab-label">Menu</span>
    </button>
  @else
    <a href="{{ route('bagages.index') }}" class="apple-tab-item {{ request()->routeIs('bagages.index') ? 'active' : '' }}">
      <div class="tab-icon"><i class="bi bi-box-seam"></i></div>
      <span class="tab-label">Colis</span>
    </a>
    <a href="{{ route('embarquement.index') }}" class="apple-tab-item {{ request()->routeIs('embarquement.*') ? 'active' : '' }}">
      <div class="tab-icon"><i class="bi bi-truck-front"></i></div>
      <span class="tab-label">Embarq.</span>
    </a>
    <a href="{{ route('profil.index') }}" class="apple-tab-item {{ request()->routeIs('profil.*') ? 'active' : '' }}">
      <div class="tab-icon"><i class="bi bi-person-circle"></i></div>
      <span class="tab-label">Profil</span>
    </a>
    <button type="button" class="apple-tab-item apple-tab-btn" onclick="toggleSidebarMobile()" aria-label="Menu">
      <div class="tab-icon"><i class="bi bi-grid-fill"></i></div>
      <span class="tab-label">Menu</span>
    </button>
  @endif
</nav>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session('success') }}'));</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session('error') }}'));</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar toggle & Mobile Drawer ────────────────────
const sidebar     = document.getElementById('sidebar');
const mainWrapper = document.getElementById('mainWrapper');
const toggle      = document.getElementById('sidebarToggle');
let sidebarOpen   = true;

function openSidebarMobile() {
  const sb = document.getElementById('sidebar');
  const bd = document.getElementById('sidebarBackdrop');
  if (sb) sb.classList.add('mobile-open');
  if (bd) bd.classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeSidebarMobile() {
  const sb = document.getElementById('sidebar');
  const bd = document.getElementById('sidebarBackdrop');
  if (sb) sb.classList.remove('mobile-open');
  if (bd) bd.classList.remove('active');
  document.body.style.overflow = '';
}
function toggleSidebarMobile() {
  const sb = document.getElementById('sidebar');
  if (sb && sb.classList.contains('mobile-open')) {
    closeSidebarMobile();
  } else {
    openSidebarMobile();
  }
}

if (toggle) {
  toggle.addEventListener('click', () => {
    if (window.innerWidth <= 768) {
      toggleSidebarMobile();
    } else {
      sidebarOpen = !sidebarOpen;
      sidebar.style.transform     = sidebarOpen ? '' : 'translateX(-100%)';
      mainWrapper.style.marginLeft = sidebarOpen ? 'var(--sidebar-w)' : '0';
    }
  });
}

// ── Sous-menus sidebar ────────────────
function toggleMenu(id, el) {
  const sub = document.getElementById(id);
  if (!sub) return;
  const isOpen = sub.classList.contains('show');
  sub.classList.toggle('show', !isOpen);
  el.classList.toggle('open', !isOpen);
}

// Ouvre les menus actifs au chargement
document.querySelectorAll('.sidebar-sub.show').forEach(sub => {
  const parentItem = sub.previousElementSibling;
  if (parentItem) parentItem.classList.add('open');
});

// ── Toast ─────────────────────────────
function showToast(type, message, title) {
  const icons   = {success:'<i class="bi bi-check-circle-fill" style="color:#4ade80;"></i>', error:'<i class="bi bi-x-circle-fill" style="color:#f87171;"></i>', info:'<i class="bi bi-info-circle-fill" style="color:#60a5fa;"></i>', warning:'<i class="bi bi-exclamation-triangle-fill" style="color:#f8c97a;"></i>'};
  const titles  = {success:'Succès', error:'Erreur', info:'Information', warning:'Attention'};
  const container = document.getElementById('toastContainer');
  const div = document.createElement('div');
  div.className = 'toast-dt';
  div.innerHTML = `
    <span class="toast-icon">${icons[type]||'ℹ️'}</span>
    <div class="toast-body">
      <div class="toast-title">${title||titles[type]||'Info'}</div>
      <div class="toast-msg">${message}</div>
    </div>
  `;
  container.appendChild(div);
  setTimeout(() => {
    div.style.opacity='0'; div.style.transform='translateX(100%)';
    div.style.transition='.3s';
    setTimeout(()=>div.remove(), 300);
  }, 4000);
}

// ── Format FCFA ───────────────────────
function formatFcfa(n) {
  return parseInt(n||0).toLocaleString('fr-FR') + ' FCFA';
}

// ── Dropdown utilisateur ──────────────
function toggleUserDropdown(e) {
  e.stopPropagation();
  const chip     = document.getElementById('userChip');
  const dropdown = document.getElementById('userDropdown');
  const isOpen   = dropdown.classList.contains('open');
  dropdown.classList.toggle('open', !isOpen);
  chip.classList.toggle('open', !isOpen);
}
document.addEventListener('click', function(e) {
  const chip     = document.getElementById('userChip');
  const dropdown = document.getElementById('userDropdown');
  if (chip && !chip.contains(e.target)) {
    dropdown.classList.remove('open');
    chip.classList.remove('open');
  }
});

// ── Toggle thème Jour / Nuit ──────────
function toggleTheme() {
  const isDay = document.body.classList.toggle('day-mode');
  localStorage.setItem('diarra-theme', isDay ? 'day' : 'night');
  document.getElementById('themeIcon').innerHTML = isDay ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon-stars"></i>';
}
// Restaurer thème au chargement
(function() {
  if (localStorage.getItem('diarra-theme') === 'day') {
    document.body.classList.add('day-mode');
    const ic = document.getElementById('themeIcon');
    if (ic) ic.innerHTML = '<i class="bi bi-sun"></i>';
  }
})();
</script>
@yield('scripts')
</body>
</html>
<!-- Pagination Bootstrap dark override -->
<style>
.pagination{--bs-pagination-bg:var(--dark3);--bs-pagination-border-color:var(--border);--bs-pagination-color:var(--text2);--bs-pagination-hover-bg:var(--dark2);--bs-pagination-hover-color:var(--gold);--bs-pagination-hover-border-color:var(--border);--bs-pagination-active-bg:var(--gold);--bs-pagination-active-border-color:var(--gold);--bs-pagination-active-color:#0e1621;--bs-pagination-disabled-bg:var(--dark3);--bs-pagination-disabled-border-color:var(--border);--bs-pagination-disabled-color:var(--text2);font-size:.8rem;}
</style>
