<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Diarra Transport') – Gestion Bagages</title>
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
@media(max-width:768px){
  .sidebar{transform:translateX(-100%);}
  .sidebar.mobile-open{transform:translateX(0);}
  .main-wrapper{margin-left:0;}
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
  <button class="topbar-hamburger" id="sidebarToggle" aria-label="Menu">☰</button>
  <div class="topbar-brand">
    @include('partials.logo-svg', ['size'=>36, 'class'=>'topbar-logo'])
    <div class="topbar-brand-text">
      <div class="t1">DIARRA TRANSPORT</div>
      <div class="t2">Gestion des Bagages</div>
    </div>
  </div>
  <div class="topbar-right">
    <button class="topbar-btn" title="Recherche">🔍</button>

    {{-- Bouton mode Jour / Nuit --}}
    <div class="icon-btn" id="themeToggle" onclick="toggleTheme()" title="Mode Jour/Nuit">
      <span id="themeIcon">🌙</span>
    </div>

    {{-- User chip + dropdown --}}
    <div class="user-chip" id="userChip" onclick="toggleUserDropdown(event)">
      <div class="user-avatar">{{ auth()->user()->initiale }}</div>
      <span class="user-name">{{ auth()->user()->nom_complet }}</span>
      <span class="user-chevron">▾</span>
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
        <div class="ud-icon">⊙</div>
        <span>Mon Profile</span>
      </div>
      <form action="{{ route('logout') }}" method="POST" id="logoutForm">@csrf</form>
      <div class="ud-item logout" onclick="document.getElementById('logoutForm').submit()">
        <div class="ud-icon">→|</div>
        <span>Deconnexion</span>
      </div>
    </div>
  </div>
</header>

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

  {{-- ── Tableau de Bord (admin uniquement, tout en haut) ─── --}}
  @if($isAdmin)
  <div class="sidebar-group">
    <a href="{{ route('admin.dashboard') }}" style="display:block">
      <div class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="icon">📊</span> Tableau de Bord
      </div>
    </a>
  </div>
  @endif

  {{-- ── Bagages ────────────────────────────────────────────── --}}
  <div class="sidebar-group">
    <div class="sidebar-group-label">Bagages</div>
    <div class="sidebar-item {{ request()->routeIs('bagages.*') ? 'active' : '' }}" onclick="toggleMenu('menuBagages',this)">
      <span class="icon">🧳</span> Bagages <span class="arrow">▾</span>
    </div>
    <div class="sidebar-sub {{ request()->routeIs('bagages.*') ? 'show' : '' }}" id="menuBagages">
      @if($canBagage)
        <a href="{{ route('bagages.create') }}" class="{{ request()->routeIs('bagages.create') ? 'active' : '' }}">📦 Bagage Simple</a>
      @endif
      <a href="{{ route('bagages.index') }}" class="{{ request()->routeIs('bagages.index') ? 'active' : '' }}">📋 Bagages Enregistrés</a>
      @if($canBagage)
        <a href="{{ route('embarquement.index') }}" class="{{ request()->routeIs('embarquement.*') ? 'active' : '' }}">🚌 Embarquement</a>
      @endif
    </div>
  </div>

  {{-- ── État de Caisse (admin + agent uniquement) ──────────── --}}
  @if($canBagage)
  <div class="sidebar-group">
    <div class="sidebar-item {{ request()->routeIs('caisse.*') ? 'active' : '' }}" onclick="toggleMenu('menuCaisse',this)">
      <span class="icon">💰</span> État de Caisse <span class="arrow">▾</span>
    </div>
    <div class="sidebar-sub {{ request()->routeIs('caisse.*') ? 'show' : '' }}" id="menuCaisse">
      <a href="{{ route('caisse.ma_caisse') }}"  class="{{ request()->routeIs('caisse.ma_caisse')  ? 'active' : '' }}">📅 Ma Caisse</a>
      <a href="{{ route('caisse.par_depart') }}" class="{{ request()->routeIs('caisse.par_depart') ? 'active' : '' }}">🚌 Caisse par Départ</a>
      @if($isAdmin)
        <a href="{{ route('caisse.global') }}" class="{{ request()->routeIs('caisse.global') ? 'active' : '' }}">📊 État Global</a>
      @endif
    </div>
  </div>
  @endif

  {{-- ── Gestion des Bagages ─────────────────────────────────── --}}
  @if($canBagage)
  <div class="sidebar-group">
    <div class="sidebar-item {{ request()->routeIs('gestion.*') ? 'active' : '' }}" onclick="toggleMenu('menuGestion',this)">
      <span class="icon">📁</span> Gestion des Bagages <span class="arrow">▾</span>
    </div>
    <div class="sidebar-sub {{ request()->routeIs('gestion.*') ? 'show' : '' }}" id="menuGestion">
      <a href="{{ route('bagages.index') }}"     class="{{ request()->routeIs('gestion.enregistres') ? 'active' : '' }}">📋 Bagages Enregistrés</a>
      <a href="{{ route('gestion.embarques') }}" class="{{ request()->routeIs('gestion.embarques')   ? 'active' : '' }}">✅ Bagages Embarqués</a>
    </div>
  </div>
  @endif

  {{-- ── Mon Profil ───────────────────────────────────────────── --}}
  <div class="sidebar-group">
    <a href="{{ route('profil.index') }}" style="display:block">
      <div class="sidebar-item {{ request()->routeIs('profil.*') ? 'active' : '' }}">
        <span class="icon">👤</span> Mon Profil
      </div>
    </a>
  </div>

  {{-- ── Administration (admin uniquement) ──────────────────── --}}
  @if($isAdmin)
  <div class="sidebar-group">
    <div class="sidebar-item {{ request()->routeIs('admin.*') ? 'active' : '' }}" onclick="toggleMenu('menuAdmin',this)">
      <span class="icon">🛡</span> Administration Système <span class="arrow">▾</span>
    </div>
    <div class="sidebar-sub {{ request()->routeIs('admin.*') ? 'show' : '' }}" id="menuAdmin">
      <a href="{{ route('admin.societe') }}"      class="{{ request()->routeIs('admin.societe')       ? 'active' : '' }}">⚙️ Paramètres</a>
      <a href="{{ route('admin.utilisateurs') }}" class="{{ request()->routeIs('admin.utilisateurs*') ? 'active' : '' }}">👥 Utilisateurs Système</a>
      <a href="{{ route('admin.profils') }}"      class="{{ request()->routeIs('admin.profils')       ? 'active' : '' }}">🔑 Profils Utilisateurs</a>
      <a href="{{ route('admin.logs') }}"         class="{{ request()->routeIs('admin.logs')          ? 'active' : '' }}">📄 Logs Système</a>
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

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session('success') }}'));</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session('error') }}'));</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar toggle ────────────────────
const sidebar     = document.getElementById('sidebar');
const mainWrapper = document.getElementById('mainWrapper');
const toggle      = document.getElementById('sidebarToggle');
let sidebarOpen   = true;

toggle.addEventListener('click', () => {
  if (window.innerWidth <= 768) {
    sidebar.classList.toggle('mobile-open');
  } else {
    sidebarOpen = !sidebarOpen;
    sidebar.style.transform     = sidebarOpen ? '' : 'translateX(-100%)';
    mainWrapper.style.marginLeft = sidebarOpen ? 'var(--sidebar-w)' : '0';
  }
});

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
  const icons   = {success:'✅', error:'❌', info:'ℹ️', warning:'⚠️'};
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
  document.getElementById('themeIcon').textContent = isDay ? '☀️' : '🌙';
}
// Restaurer thème au chargement
(function() {
  if (localStorage.getItem('diarra-theme') === 'day') {
    document.body.classList.add('day-mode');
    const ic = document.getElementById('themeIcon');
    if (ic) ic.textContent = '☀️';
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
