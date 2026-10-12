@extends('layouts.app')
@section('title','Tableau de Bord')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" rel="preload" as="script">
<style>
/* ── Header bleu dashboard ─── */
.db-header{
  background:linear-gradient(135deg,#1a6bbf,#1256a0);
  border-radius:14px;padding:18px 24px;margin-bottom:22px;
  display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;
}
.db-header h1{font-size:1.4rem;font-weight:700;color:#fff;margin:0;}
.db-header p{font-size:.82rem;color:rgba(255,255,255,.7);margin:2px 0 0;}

/* ── Filtres date ─── */
.db-filters{
  display:flex;align-items:center;gap:10px;flex-wrap:wrap;
  background:var(--dark2);border:1px solid var(--border);
  border-radius:10px;padding:12px 16px;margin-bottom:20px;
}
.db-filters label{font-size:.75rem;color:var(--text2);text-transform:uppercase;letter-spacing:.06em;}
.db-date{
  height:36px;padding:0 10px;border-radius:7px;font-size:.82rem;
  border:1px solid var(--border);background:var(--dark3);color:var(--text);
  font-family:inherit;cursor:pointer;outline:none;color-scheme:dark;
}
.db-date:focus{border-color:var(--gold);}
.btn-refresh{
  height:36px;padding:0 16px;border-radius:7px;
  background:linear-gradient(135deg,var(--gold),var(--gold2));
  border:none;color:#0e1621;font-weight:700;font-size:.82rem;
  cursor:pointer;font-family:inherit;
}
@media(max-width:600px){
  .db-filters{flex-direction:column;align-items:stretch;gap:12px;}
  .db-filters > div{width:100%;}
  .db-date{width:100%;}
  .btn-refresh{width:100%;min-height:42px;}
}

/* ── Stat cards ─── */
.db-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;}
@media(max-width:900px){.db-stats{grid-template-columns:1fr 1fr;}}
@media(max-width:560px){.db-stats{grid-template-columns:1fr;}}
.db-stat-card{
  background:var(--dark2);border:1px solid var(--border);border-radius:12px;
  padding:20px 22px;position:relative;
  box-shadow:0 4px 20px rgba(0,0,0,0.15);
  transition:transform 0.2s ease,border-color 0.2s ease;
}
.db-stat-card:hover{
  transform:translateY(-2px);
  border-color:rgba(201,168,76,0.35);
}
.db-stat-card .refresh-icon{
  position:absolute;top:12px;right:14px;
  font-size:.9rem;color:var(--text2);cursor:pointer;
  transition:color 0.15s ease,transform 0.2s ease;
}
.db-stat-card .refresh-icon:hover{
  color:var(--gold);
  transform:rotate(90deg);
}
.db-stat-card .s-label{
  font-size:.72rem;color:var(--text2);margin-bottom:8px;
  font-weight:600;text-transform:uppercase;letter-spacing:.05em;
}
.db-stat-card .s-value{
  font-size:1.85rem;font-weight:700;color:#38bdf8;
  font-family:'Space Mono',monospace;line-height:1.1;
}
.db-stat-card .s-value.green{color:#4ade80;}
.db-stat-card .s-value.orange{color:#fbbf24;}
.db-stat-card .s-value.gold{color:var(--gold2);}

/* ── Graphiques ─── */
.db-charts{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px;}
@media(max-width:860px){.db-charts{grid-template-columns:1fr;}}
.db-chart-card{
  background:var(--dark2);border:1px solid var(--border);border-radius:12px;
  padding:20px 22px;box-shadow:0 4px 20px rgba(0,0,0,0.15);
}
.db-chart-title{
  font-size:.88rem;font-weight:700;color:var(--text);margin-bottom:16px;
  display:flex;justify-content:space-between;align-items:center;
}
.db-chart-title .refresh-icon{
  font-size:.9rem;color:var(--text2);cursor:pointer;
  transition:color 0.15s ease,transform 0.2s ease;
}
.db-chart-title .refresh-icon:hover{
  color:var(--gold);
  transform:rotate(90deg);
}
.chart-container-box{
  position:relative;height:240px;width:100%;
}

/* ── Gestion des droits ─── */
.droits-card{display:none;}
.droits-table{display:none;}
.droits-title{display:none;}
.badge-role,.badge-admin,.badge-agent,.badge-ctrl,.badge-actif,.badge-inactif,.droits-select,.btn-droits{display:none;}
</style>
@endsection

@section('content')
@php
  use Carbon\Carbon;
  $dc = Carbon::parse($debut);
  $df = Carbon::parse($fin);
@endphp

{{-- ── HEADER ──────────────────────────────────────────── --}}
<div class="db-header">
  <div>
    <h1>📊 Tableau de Bord</h1>
    <p>Vue d'ensemble — {{ $dc->locale('fr')->isoFormat('D MMM YYYY') }} → {{ $df->locale('fr')->isoFormat('D MMM YYYY') }}</p>
  </div>
  <div style="font-size:.78rem;color:rgba(255,255,255,.7);">
    Accès : Administrateur uniquement
  </div>
</div>

{{-- ── FILTRES DATE ─────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.dashboard') }}" class="db-filters">
  <div>
    <label>Début</label><br>
    <input type="date" name="debut" class="db-date" value="{{ $debut }}" max="{{ now()->toDateString() }}">
  </div>
  <div>
    <label>Fin</label><br>
    <input type="date" name="fin"   class="db-date" value="{{ $fin }}"   max="{{ now()->toDateString() }}">
  </div>
  <button type="submit" class="btn-refresh" style="margin-top:16px;">🔄 Actualiser</button>
  <a href="{{ route('admin.dashboard') }}"
     style="margin-top:16px;height:34px;padding:0 14px;border-radius:7px;
            background:var(--dark3);border:1px solid var(--border);color:var(--text2);
            font-size:.78rem;display:flex;align-items:center;text-decoration:none;">
    Réinitialiser
  </a>
</form>

{{-- ── STAT CARDS ───────────────────────────────────────── --}}
<div class="db-stats">

  <div class="db-stat-card">
    <span class="refresh-icon" onclick="location.reload()" title="Actualiser">↻</span>
    <div class="s-label">Total Bagages Enregistrés</div>
    <div class="s-value">{{ number_format($totalEnregistres, 0, ',', ' ') }}</div>
  </div>

  <div class="db-stat-card">
    <span class="refresh-icon" onclick="location.reload()" title="Actualiser">↻</span>
    <div class="s-label">Total Bagages Embarqués</div>
    <div class="s-value green">{{ number_format($totalEmbarques, 0, ',', ' ') }}</div>
  </div>

  <div class="db-stat-card">
    <span class="refresh-icon" onclick="location.reload()" title="Actualiser">↻</span>
    <div class="s-label">Taux d'Embarquement</div>
    <div class="s-value orange">{{ $tauxEmbarquement }} %</div>
  </div>

  <div class="db-stat-card">
    <span class="refresh-icon" onclick="location.reload()" title="Actualiser">↻</span>
    <div class="s-label">Chiffre d'Affaires Total</div>
    <div class="s-value gold" style="font-size:1.35rem;">
      {{ number_format($chiffreAffaires, 0, ',', ' ') }} <span style="font-size:.8rem;font-weight:600;">CFA</span>
    </div>
  </div>

</div>

{{-- ── GRAPHIQUES ───────────────────────────────────────── --}}
<div class="db-charts">

  {{-- Évolution mensuelle --}}
  <div class="db-chart-card">
    <div class="db-chart-title">
      Évolution des Enregistrements et Embarquements
      <span class="refresh-icon" onclick="initCharts()" title="Recharger graphique">↻</span>
    </div>
    <div class="chart-container-box">
      <canvas id="chartEvolution"></canvas>
    </div>
  </div>

  {{-- Répartition par destination --}}
  <div class="db-chart-card">
    <div class="db-chart-title">
      Répartition des Recettes par Destination
      <span class="refresh-icon" onclick="initCharts()" title="Recharger graphique">↻</span>
    </div>
    <div class="chart-container-box">
      <canvas id="chartDestinations"></canvas>
    </div>
  </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@php
  $labels   = $evolutionRaw->pluck('mois_label')->toArray();
  $totaux   = $evolutionRaw->pluck('total')->toArray();
  $embarqs  = $evolutionRaw->pluck('embarques')->toArray();
  $destLabels   = $parDestination->pluck('ville_arrivee')->toArray();
  $destRecettes = $parDestination->map(fn($d) => (int)$d->recettes)->toArray();
@endphp

const labelsEvol         = @json($labels);
const dataTotal          = @json($totaux);
const dataEmbarq         = @json($embarqs);
const labelsDestinations = @json($destLabels);
const dataRecettes       = @json($destRecettes);

// Palette moderne vibrante optimisée dark/light
const paletteDest = [
  '#ec4899', // Rose vif
  '#3b82f6', // Bleu
  '#f59e0b', // Ambre doré
  '#10b981', // Émeraude
  '#ef4444', // Rouge
  '#8b5cf6', // Violet
  '#06b6d4', // Cyan
  '#14b8a6', // Teal
  '#f97316'  // Orange
];

function getThemeConfig() {
  const isDay = document.body.classList.contains('day-mode');
  return {
    isDay: isDay,
    textColor: isDay ? '#5a6a7e' : '#7a90a8',
    titleColor: isDay ? '#1a2332' : '#eef2f7',
    gridColor: isDay ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.06)',
    tooltipBg: isDay ? 'rgba(255,255,255,0.95)' : 'rgba(14,22,33,0.95)',
    tooltipText: isDay ? '#1a2332' : '#eef2f7',
    tooltipBorder: isDay ? 'rgba(0,0,0,0.1)' : 'rgba(201,168,76,0.3)',
  };
}

let chartEvolInstance = null;
let chartDestInstance = null;

function initCharts() {
  const cfg = getThemeConfig();

  // 1. Évolution des Enregistrements et Embarquements
  const ctxEvol = document.getElementById('chartEvolution');
  if (ctxEvol) {
    if (chartEvolInstance) chartEvolInstance.destroy();
    chartEvolInstance = new Chart(ctxEvol, {
      type: 'bar',
      data: {
        labels: labelsEvol,
        datasets: [
          {
            label: 'Bagages Enregistrés',
            data: dataTotal,
            backgroundColor: 'rgba(201, 168, 76, 0.55)',
            borderColor: '#c9a84c',
            borderWidth: 1.5,
            borderRadius: 6,
            order: 2,
          },
          {
            label: 'Bagages Embarqués',
            data: dataEmbarq,
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34, 197, 94, 0.15)',
            borderWidth: 2.5,
            type: 'line',
            fill: false,
            tension: 0.35,
            pointBackgroundColor: '#22c55e',
            pointBorderColor: cfg.isDay ? '#ffffff' : '#0e1621',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            order: 1,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: cfg.titleColor,
              font: { family: 'Outfit, sans-serif', size: 12, weight: '500' },
              usePointStyle: true,
              pointStyle: 'circle',
              padding: 16
            }
          },
          tooltip: {
            backgroundColor: cfg.tooltipBg,
            titleColor: '#c9a84c',
            bodyColor: cfg.tooltipText,
            borderColor: cfg.tooltipBorder,
            borderWidth: 1,
            padding: 12,
            cornerRadius: 8,
            titleFont: { weight: '700' }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: cfg.textColor,
              font: { family: 'Space Mono, monospace', size: 10 },
              stepSize: 2
            },
            grid: { color: cfg.gridColor }
          },
          x: {
            ticks: {
              color: cfg.textColor,
              font: { family: 'Outfit, sans-serif', size: 11 }
            },
            grid: { display: false }
          }
        }
      }
    });
  }

  // 2. Répartition par destination
  const ctxDest = document.getElementById('chartDestinations');
  if (ctxDest) {
    if (chartDestInstance) chartDestInstance.destroy();
    chartDestInstance = new Chart(ctxDest, {
      type: 'bar',
      data: {
        labels: labelsDestinations,
        datasets: [{
          label: 'Recettes',
          data: dataRecettes,
          backgroundColor: paletteDest.slice(0, labelsDestinations.length),
          borderRadius: 6,
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: cfg.tooltipBg,
            titleColor: '#c9a84c',
            bodyColor: cfg.tooltipText,
            borderColor: cfg.tooltipBorder,
            borderWidth: 1,
            padding: 12,
            cornerRadius: 8,
            callbacks: {
              label: ctx => ' Recettes: ' + parseInt(ctx.parsed.y || 0).toLocaleString('fr-FR') + ' FCFA'
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: cfg.textColor,
              font: { family: 'Space Mono, monospace', size: 10 },
              callback: function(v) {
                if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M';
                if (v >= 1000) return (v / 1000).toLocaleString('fr-FR') + 'k';
                return v;
              }
            },
            grid: { color: cfg.gridColor }
          },
          x: {
            ticks: {
              color: cfg.textColor,
              font: { family: 'Outfit, sans-serif', size: 11 }
            },
            grid: { display: false }
          }
        }
      }
    });
  }
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', initCharts);

// Ré-initialisation dynamique si bascule de thème
window.addEventListener('diarra-theme-changed', initCharts);
</script>
@endsection
