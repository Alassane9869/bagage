@extends('layouts.app')
@section('title','État Global des Caisses')

@section('styles')
<style>
/* ═══ BANDEAU BLEU STATS (style images 1&2) ═════════════════ */
.eg-topbar{
  background:linear-gradient(135deg,#1a6bbf 0%,#1256a0 100%);
  border-radius:14px;padding:0;margin-bottom:22px;overflow:hidden;
}
.eg-topbar-stats{
  display:grid;grid-template-columns:auto 1fr 1fr 1fr auto;
  align-items:stretch;min-height:72px;
}
@media(max-width:900px){.eg-topbar-stats{grid-template-columns:1fr 1fr;}}
@media(max-width:600px){
  .eg-topbar-stats{grid-template-columns:1fr;}
  .eg-stat, .eg-stat-nb{border-right:none !important;border-bottom:1px solid rgba(255,255,255,.15);}
  .eg-topbar-actions .btn-print-white{width:100%;justify-content:center;min-height:42px;}
}
.eg-stat{
  padding:14px 22px;border-right:1px solid rgba(255,255,255,.15);
  display:flex;flex-direction:column;justify-content:center;
}
.eg-stat:last-child{border-right:none;}
.eg-stat-label{font-size:.68rem;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px;}
.eg-stat-value{font-size:1.3rem;font-weight:700;color:#fff;font-family:'Space Mono',monospace;line-height:1;}
.eg-stat-sub{font-size:.7rem;color:rgba(255,255,255,.6);margin-top:2px;}
.eg-stat-nb{
  padding:14px 20px;border-right:1px solid rgba(255,255,255,.15);
  display:flex;flex-direction:column;justify-content:center;min-width:100px;
}
.eg-stat-nb .nb-label{font-size:.65rem;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.06em;}
.eg-stat-nb .nb-value{font-size:1.8rem;font-weight:700;color:#fff;font-family:'Space Mono',monospace;line-height:1.1;}
.eg-topbar-actions{
  padding:14px 18px;display:flex;flex-direction:column;justify-content:center;gap:6px;
}
.btn-print-white{
  background:#fff;color:#1a6bbf;border:none;border-radius:7px;
  padding:8px 16px;font-size:.8rem;font-weight:700;cursor:pointer;
  font-family:inherit;white-space:nowrap;transition:.15s;
  display:flex;align-items:center;gap:5px;
}
.btn-print-white:hover{background:#e8f0fc;transform:translateY(-1px);}

/* ═══ BARRE DATE ═══════════════════════════════════════════ */
.eg-date-bar{
  display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;
}
.eg-btn-nav{
  width:34px;height:34px;border-radius:8px;background:var(--dark3);
  border:1px solid var(--border);color:var(--text2);
  display:flex;align-items:center;justify-content:center;
  text-decoration:none;font-size:1.1rem;transition:.15s;
}
.eg-btn-nav:hover{border-color:var(--gold);color:var(--gold);}
.eg-btn-nav.off{opacity:.3;pointer-events:none;}
.eg-date-input{
  height:36px;padding:0 12px;border-radius:8px;font-size:.82rem;
  border:1px solid var(--border);background:var(--dark3);color:var(--text);
  font-family:inherit;cursor:pointer;outline:none;color-scheme:dark;
}
.eg-date-input:focus{border-color:var(--gold);}
.eg-shortcut{
  height:32px;padding:0 12px;border-radius:7px;background:var(--dark3);
  border:1px solid var(--border);color:var(--text2);font-size:.78rem;
  text-decoration:none;display:flex;align-items:center;transition:.15s;white-space:nowrap;
}
.eg-shortcut:hover{border-color:var(--gold);color:var(--gold);}
.eg-shortcut.on{border-color:var(--gold);color:var(--gold);background:rgba(201,168,76,.1);font-weight:600;}
.eg-date-label{font-size:.82rem;color:var(--text2);}

/* ═══ TABLEAU AGENTS ═══════════════════════════════════════ */
.eg-table-wrap{overflow-x:auto;}
.eg-table{width:100%;border-collapse:collapse;font-size:.84rem;}
.eg-table thead th{
  padding:10px 14px;background:var(--dark3);color:var(--text2);
  font-size:.72rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;
  border-bottom:1px solid var(--border);
}
.eg-table tbody tr{border-bottom:1px solid rgba(255,255,255,.04);}
.eg-table tbody tr:hover{background:rgba(255,255,255,.025);}
.eg-table tbody td{padding:11px 14px;color:var(--text2);vertical-align:middle;}
.eg-table tfoot td{
  padding:10px 14px;border-top:2px solid var(--border);
  font-weight:700;font-size:.82rem;
}
.eg-badge-nb{
  background:rgba(255,255,255,.08);color:var(--text);
  border-radius:20px;padding:2px 12px;font-size:.78rem;font-weight:600;
}
@media(max-width:768px){.eg-topbar-stats{grid-template-columns:1fr 1fr!important;}}
</style>
@endsection

@section('content')
@php
  use Carbon\Carbon;
  $dc      = Carbon::parse($date);
  $isToday = $dc->isToday();
  $isYest  = $dc->isYesterday();
  $prev    = $dc->copy()->subDay()->toDateString();
  $next    = $dc->copy()->addDay()->toDateString();
  $today   = now()->toDateString();
  $yday    = now()->subDay()->toDateString();
  $d7      = now()->subDays(7)->toDateString();
  $s       = $stats;
  $nbCaissiers = $parAgent->count();
@endphp

{{-- ══ BANDEAU BLEU STATS ═══════════════════════════════════ --}}
<div class="eg-topbar">
  <div class="eg-topbar-stats">

    {{-- Nb Caissiers --}}
    <div class="eg-stat-nb">
      <div class="nb-label">Nombre Caissiers</div>
      <div class="nb-value">{{ $nbCaissiers }}</div>
    </div>

    {{-- Billets Vendus → Bagages Enregistrés --}}
    <div class="eg-stat">
      <div class="eg-stat-label">Bagages Enregistrés</div>
      <div class="eg-stat-value">{{ number_format($s->recettes_totales ?? 0, 0, ',', ' ') }} CFA</div>
    </div>

    {{-- Billets Annulés → Bagages Remboursés --}}
    <div class="eg-stat">
      <div class="eg-stat-label">Bagages Remboursés</div>
      <div class="eg-stat-value">{{ number_format($s->remises_totales ?? 0, 0, ',', ' ') }} CFA</div>
    </div>

    {{-- Total Règlements --}}
    <div class="eg-stat">
      <div class="eg-stat-label">Total Règlements</div>
      <div class="eg-stat-value">0 CFA</div>
    </div>

    {{-- Total Net --}}
    <div class="eg-stat">
      <div class="eg-stat-label">Total</div>
      <div class="eg-stat-value">{{ number_format($s->net_total ?? 0, 0, ',', ' ') }} CFA</div>
      <div class="eg-stat-sub">{{ $dc->locale('fr')->isoFormat('D MMM YYYY') }}</div>
    </div>

    {{-- Boutons impression --}}
    <div class="eg-topbar-actions">
      <button class="btn-print-white" onclick="imprimerEtatGlobal()">
        🖨️ Imprimer Etat Global
      </button>
    </div>

  </div>
</div>

{{-- ══ BARRE DATE ════════════════════════════════════════════ --}}
<div class="eg-date-bar">
  <a href="{{ route('caisse.global', ['date'=>$prev]) }}" class="eg-btn-nav" title="Jour précédent">‹</a>

  <input type="date" class="eg-date-input" value="{{ $date }}" max="{{ $today }}"
         onchange="window.location.href='{{ route('caisse.global') }}?date='+this.value">

  <a href="{{ route('caisse.global', ['date'=>$next]) }}"
     class="eg-btn-nav {{ $isToday ? 'off' : '' }}" title="Jour suivant">›</a>

  <div style="width:1px;height:22px;background:var(--border);margin:0 4px;"></div>

  <a href="{{ route('caisse.global', ['date'=>$today]) }}"
     class="eg-shortcut {{ $isToday ? 'on' : '' }}">Aujourd'hui</a>
  <a href="{{ route('caisse.global', ['date'=>$yday]) }}"
     class="eg-shortcut {{ $isYest ? 'on' : '' }}">Hier</a>
  <a href="{{ route('caisse.global', ['date'=>$d7]) }}"
     class="eg-shortcut {{ $date===$d7 ? 'on' : '' }}">–7 jours</a>

  <span class="eg-date-label">
    {{ $dc->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
  </span>
</div>

{{-- ══ TABLEAU PAR CAISSIER ══════════════════════════════════ --}}
<div class="card-dt">
  <div class="card-title" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
    <span>Caissiers du {{ $dc->locale('fr')->isoFormat('D MMMM YYYY') }}</span>
    @if($parAgent->count() > 0)
      <span style="font-size:.75rem;font-weight:400;color:var(--text2);">
        {{ $parAgent->count() }} caissier(s) actif(s)
      </span>
    @endif
  </div>

  @if($parAgent->isEmpty())
    <div style="text-align:center;padding:48px;color:var(--text2);">
      <div style="font-size:2rem;margin-bottom:10px;">📭</div>
      Aucune activité enregistrée le {{ $dc->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
    </div>
  @else
  <div class="eg-table-wrap">
    <table class="eg-table">
      <thead>
        <tr>
          <th style="text-align:left;">Caissier</th>
          <th style="text-align:right;">Total Vente</th>
          <th style="text-align:right;">Total Annulation</th>
          <th style="text-align:right;">Total Règlement</th>
          <th style="text-align:right;">A Rendre</th>
        </tr>
      </thead>
      <tbody>
        @foreach($parAgent as $a)
        <tr>
          <td>
            <div style="color:var(--text);font-weight:600;">{{ strtoupper(trim($a->nom_complet)) ?: '—' }}</div>
            <div style="font-size:.72rem;color:var(--text2);">{{ $a->agence ?? '' }}
              &nbsp;·&nbsp;
              <span class="eg-badge-nb">{{ $a->nb_bagages }} bag.</span>
            </div>
          </td>
          <td style="text-align:right;">
            <span class="mono" style="color:var(--text);">{{ number_format($a->recettes ?? 0, 0, ',', ' ') }} CFA</span>
          </td>
          <td style="text-align:right;">
            @if(($a->remises ?? 0) > 0)
              <span style="color:#f87171;">{{ number_format($a->remises, 0, ',', ' ') }} CFA</span>
            @else
              <span style="color:var(--text2);">0 CFA</span>
            @endif
          </td>
          <td style="text-align:right;color:var(--text2);">0 CFA</td>
          <td style="text-align:right;">
            <span class="mono" style="color:#4ade80;font-weight:600;">{{ number_format($a->net ?? 0, 0, ',', ' ') }} CFA</span>
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td style="color:var(--gold);">TOTAL ({{ $nbCaissiers }} caissier(s))</td>
          <td style="text-align:right;"><span class="mono" style="font-weight:700;">{{ number_format($s->recettes_totales ?? 0, 0, ',', ' ') }} CFA</span></td>
          <td style="text-align:right;"><span style="color:#f87171;font-weight:700;">{{ number_format($s->remises_totales ?? 0, 0, ',', ' ') }} CFA</span></td>
          <td style="text-align:right;color:var(--text2);">0 CFA</td>
          <td style="text-align:right;"><span class="mono" style="color:#4ade80;font-weight:700;">{{ number_format($s->net_total ?? 0, 0, ',', ' ') }} CFA</span></td>
        </tr>
      </tfoot>
    </table>
  </div>
  @endif
</div>

@endsection

@section('scripts')
<script>
/* ══════════════════════════════════════════════════════════════
   IMPRESSION ÉTAT GLOBAL – Fiche A4
   Fidèle aux images 3 & 4 : tableau caissiers + Total + Signature
   ══════════════════════════════════════════════════════════════ */
function imprimerEtatGlobal() {
  @php
    $agentsJS = [];
    foreach($parAgent as $a) {
      $agentsJS[] = [
        'nom'    => strtoupper(trim($a->nom_complet)) ?: '—',
        'vente'  => (int)($a->recettes ?? 0),
        'remise' => (int)($a->remises  ?? 0),
        'net'    => (int)($a->net      ?? 0),
      ];
    }
    $totalNet   = (int)($s->net_total        ?? 0);
    $totalVente = (int)($s->recettes_totales ?? 0);
    $totalRem   = (int)($s->remises_totales  ?? 0);
  @endphp

  try {
    const agents     = @json($agentsJS);
    const totalNet   = {{ $totalNet }};
    const totalVente = {{ $totalVente }};
    const totalRem   = {{ $totalRem }};
    const dateLabel  = '{{ $dc->format("d/m/y") }}';
    const dateLong   = '{{ $dc->locale("fr")->isoFormat("D MMMM YYYY") }}';

    // Date/heure courante formatée proprement
    const nowDate = new Date();
    const nowStr  = nowDate.toLocaleDateString('fr-FR', {
                      day:'2-digit', month:'long', year:'numeric'
                    }) + ' ' + nowDate.toLocaleTimeString('fr-FR', {
                      hour:'2-digit', minute:'2-digit'
                    });

    const fmt = n => Number(n).toLocaleString('fr-FR');

    // ── Lignes agents ──
    let rows = '';
    if (agents.length === 0) {
      rows = '<tr><td colspan="5" style="text-align:center;padding:12px;color:#888;font-style:italic;">Aucune donnée pour cette date</td></tr>';
    } else {
      agents.forEach(a => {
        rows += `<tr>
          <td style="font-weight:bold;">${a.nom}</td>
          <td style="text-align:right;">${fmt(a.vente)}</td>
          <td style="text-align:right;">${a.remise > 0 ? fmt(a.remise) : '0'}</td>
          <td style="text-align:right;">0</td>
          <td style="text-align:right;font-weight:bold;">${fmt(a.net)}</td>
        </tr>`;
      });
    }

    const html = `<!DOCTYPE html>
<html lang="fr"><head>
<meta charset="UTF-8">
<title>Etat de caisse du ${dateLabel}</title>
<style>
  @page { size:A4 portrait; margin:20mm 22mm; }
  *{ box-sizing:border-box; margin:0; padding:0;
     -webkit-print-color-adjust:exact; print-color-adjust:exact; }
  body{ font-family:Arial,Helvetica,sans-serif; font-size:11px; color:#000; background:#fff; }
  .company{ font-size:10px; color:#666; margin-bottom:10px; }
  .doc-title{ font-size:22px; font-weight:bold; text-align:center; margin:0 0 24px; }
  .doc-sub{ font-size:11px; margin-bottom:16px; }
  table{ width:100%; border-collapse:collapse; margin-bottom:12px; }
  th{ border:1px solid #000; padding:8px 10px; font-size:11px; font-weight:bold; text-align:center; }
  td{ border:1px solid #000; padding:7px 10px; font-size:11px; }
  tfoot td{ font-weight:bold; background:#f5f5f5; border:1px solid #000; }
  .total-line{ text-align:right; font-size:12px; font-weight:bold; margin:14px 0 38px; }
  .sign{ text-align:right; margin-bottom:10px; }
  .sign .s-label{ font-size:12px; font-weight:bold; display:block; margin-bottom:40px; }
  .sign .s-line{ display:inline-block; width:200px; border-bottom:1px solid #000; }
  .footer{ text-align:right; font-size:9px; color:#777; margin-top:44px; }
</style>
</head><body>

<div class="company">
  DIARRA TRANSPORT &nbsp;&bull;&nbsp; Près de la maternité Sogoniko – Bamako &nbsp;&bull;&nbsp; Tél : (223) 20802142
</div>

<div class="doc-title">Etat de caisse du ${dateLabel}</div>

<div class="doc-sub">Etat de caisse Globale</div>

<table>
  <thead>
    <tr>
      <th style="width:36%;text-align:left;">Nom prenom du caissier</th>
      <th style="width:20%;">Vente Billets</th>
      <th style="width:18%;">Remboursements</th>
      <th style="width:13%;">Reglements</th>
      <th style="width:13%;">A Verser</th>
    </tr>
  </thead>
  <tbody>${rows}</tbody>
  <tfoot>
    <tr>
      <td><strong>TOTAL</strong></td>
      <td style="text-align:right;">${fmt(totalVente)}</td>
      <td style="text-align:right;">${fmt(totalRem)}</td>
      <td style="text-align:right;">0</td>
      <td style="text-align:right;font-size:12px;">${fmt(totalNet)}</td>
    </tr>
  </tfoot>
</table>

<div class="total-line">Total a verser : ${fmt(totalNet)}</div>

<div class="sign">
  <span class="s-label">Signature</span>
  <div class="s-line"></div>
</div>

<div class="footer">${dateLong} ${nowStr.split(' ').slice(-1)[0]}</div>

<script>
  window.onload = function() {
    setTimeout(function() { window.print(); }, 500);
  };
<\/script>
</body></html>`;

    const w = window.open('', '_blank', 'width=900,height=750,scrollbars=yes');
    if (!w) {
      showToast('error', 'Popup bloqué – autorisez les popups pour ce site.');
      return;
    }
    w.document.open();
    w.document.write(html);
    w.document.close();

  } catch(err) {
    console.error('Erreur impression:', err);
    showToast('error', 'Erreur impression : ' + err.message);
  }
}
</script>
@endsection
