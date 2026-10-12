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
  padding:18px 20px;position:relative;
}
.db-stat-card .refresh-icon{
  position:absolute;top:10px;right:12px;
  font-size:.85rem;color:var(--text2);cursor:pointer;
}
.db-stat-card .s-label{font-size:.75rem;color:var(--text2);margin-bottom:6px;}
.db-stat-card .s-value{
  font-size:1.7rem;font-weight:700;color:var(--gold);
  font-family:'Space Mono',monospace;line-height:1.1;
}
.db-stat-card .s-value.green{color:#4ade80;}
.db-stat-card .s-value.orange{color:#f59e0b;}

/* ── Graphiques ─── */
.db-charts{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px;}
@media(max-width:860px){.db-charts{grid-template-columns:1fr;}}
.db-chart-card{
  background:var(--dark2);border:1px solid var(--border);border-radius:12px;padding:16px 18px;
}
.db-chart-title{
  font-size:.85rem;font-weight:700;color:var(--text);margin-bottom:14px;
  display:flex;justify-content:space-between;align-items:center;
}
.db-chart-title .refresh-icon{font-size:.85rem;color:var(--text2);cursor:pointer;}

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
    <span class="refresh-icon">↻</span>
    <div class="s-label">Total Bagages Enregistrés</div>
    <div class="s-value">{{ number_format($totalEnregistres, 0, ',', ' ') }}</div>
  </div>

  <div class="db-stat-card">
    <span class="refresh-icon">↻</span>
    <div class="s-label">Total Bagages Embarqués</div>
    <div class="s-value green">{{ number_format($totalEmbarques, 0, ',', ' ') }}</div>
  </div>

  <div class="db-stat-card">
    <span class="refresh-icon">↻</span>
    <div class="s-label">Taux d'Embarquement</div>
    <div class="s-value orange">{{ $tauxEmbarquement }} %</div>
  </div>

  <div class="db-stat-card">
    <span class="refresh-icon">↻</span>
    <div class="s-label">Chiffre d'Affaires Total</div>
    <div class="s-value" style="font-size:1.2rem;">
      {{ number_format($chiffreAffaires, 0, ',', ' ') }} CFA
    </div>
  </div>

</div>

{{-- ── GRAPHIQUES ───────────────────────────────────────── --}}
<div class="db-charts">

  {{-- Évolution mensuelle --}}
  <div class="db-chart-card">
    <div class="db-chart-title">
      Évolution des Enregistrements et Embarquements
      <span class="refresh-icon">↻</span>
    </div>
    <canvas id="chartEvolution" height="180"></canvas>
  </div>

  {{-- Répartition par destination --}}
  <div class="db-chart-card">
    <div class="db-chart-title">
      Répartition des Recettes par Destination
      <span class="refresh-icon">↻</span>
    </div>
    <canvas id="chartDestinations" height="180"></canvas>
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
  $colors = ['#FF6B9D','#1a6bbf','#FCD116','#169B62','#CE1126','#e07b00','#9b59b6','#1abc9c','#e67e22','#95a5a6'];
@endphp

const labelsEvol         = @json($labels);
const dataTotal          = @json($totaux);
const dataEmbarq         = @json($embarqs);
const labelsDestinations = @json($destLabels);
const dataRecettes       = @json($destRecettes);
const colors             = @json($colors);

new Chart(document.getElementById('chartEvolution'), {
  type: 'bar',
  data: {
    labels: labelsEvol,
    datasets: [
      {
        label: 'Bagages Enregistrés',
        data: dataTotal,
        backgroundColor: 'rgba(252,209,22,0.6)',
        borderColor: '#FCD116',
        borderWidth: 1,
      },
      {
        label: 'Bagages Embarqués',
        data: dataEmbarq,
        backgroundColor: 'rgba(255,107,157,0.7)',
        borderColor: '#FF6B9D',
        borderWidth: 1,
        type: 'line',
        fill: false,
        tension: 0.3,
        pointRadius: 4,
      }
    ]
  },
  options: {
    responsive: true,
    interaction: { mode: 'index' },
    plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
    scales: {
      y: { beginAtZero: true, ticks: { font: { size: 11 } } },
      x: { ticks: { font: { size: 11 } } }
    }
  }
});

new Chart(document.getElementById('chartDestinations'), {
  type: 'bar',
  data: {
    labels: labelsDestinations,
    datasets: [{
      label: 'Recettes (CFA)',
      data: dataRecettes,
      backgroundColor: colors.slice(0, labelsDestinations.length),
      borderRadius: 4,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { callbacks: { label: ctx => ctx.parsed.y.toLocaleString('fr-FR') + ' CFA' } }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { font: { size: 10 }, callback: v => (v / 1000000).toFixed(1) + 'M' }
      },
      x: { ticks: { font: { size: 10 } } }
    }
  }
});
</script>
@endsection
