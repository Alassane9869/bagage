@extends('layouts.app')
@section('title','Mon État de Caisse')

@section('styles')
<style>
/* ── Page header bleu ─────────────────────── */
.mc-header{
  background:linear-gradient(135deg,#1a6bbf 0%,#1558a0 100%);
  border-radius:14px;padding:22px 28px;margin-bottom:22px;
  display:flex;justify-content:space-between;align-items:center;
  flex-wrap:wrap;gap:14px;
}
.mc-header-left h1{font-size:1.5rem;font-weight:700;color:#fff;margin:0;}
.mc-header-left .mc-date{
  font-size:.9rem;color:rgba(255,255,255,.8);margin-top:5px;
  display:flex;align-items:center;gap:10px;flex-wrap:wrap;
}
.mc-date-nav{display:flex;align-items:center;gap:6px;}
.mc-btn-nav{
  width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.2);
  border:1px solid rgba(255,255,255,.3);color:#fff;
  display:flex;align-items:center;justify-content:center;
  text-decoration:none;font-size:1rem;transition:.15s;cursor:pointer;
}
.mc-btn-nav:hover{background:rgba(255,255,255,.35);}
.mc-btn-nav.disabled{opacity:.3;pointer-events:none;}
.mc-date-input{
  background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);
  color:#fff;border-radius:8px;padding:4px 10px;font-size:.82rem;
  font-family:inherit;outline:none;color-scheme:dark;cursor:pointer;
}
.mc-date-input:focus{background:rgba(255,255,255,.25);}
.mc-btn-print{
  background:#fff;color:#1a6bbf;border:none;border-radius:8px;
  padding:10px 20px;font-size:.85rem;font-weight:700;cursor:pointer;
  display:flex;align-items:center;gap:6px;transition:.15s;font-family:inherit;
  white-space:nowrap;
}
.mc-btn-print:hover{background:#e8f0fc;transform:translateY(-1px);}
/* ── Section Etat de la Caisse ─────────────── */
.mc-section-title{
  font-size:.7rem;font-weight:700;color:var(--text2);
  letter-spacing:.12em;text-transform:uppercase;
  margin-bottom:12px;display:flex;align-items:center;gap:8px;
}
.mc-section-title::before{
  content:'';width:3px;height:14px;background:var(--gold);
  border-radius:2px;display:inline-block;
}
/* ── Cards caisse ───────────────────────────── */
.mc-cards{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:24px;}
@media(max-width:720px){
  .mc-header{padding:16px 14px;gap:12px;}
  .mc-header-left h1{font-size:1.25rem;}
  .mc-btn-print{width:100%;justify-content:center;}
  .mc-cards{grid-template-columns:1fr;}
}
.mc-card{
  background:var(--dark2);border:1px solid var(--border);
  border-radius:12px;padding:20px 22px;
}
.mc-card .mc-card-label{
  font-size:.75rem;color:var(--text2);font-weight:500;margin-bottom:8px;
}
.mc-card .mc-card-value{
  font-size:1.6rem;font-weight:700;
  font-family:'Space Mono',monospace;
}
.mc-card .mc-card-sub{font-size:.72rem;color:var(--text2);margin-top:4px;}
.mc-card .mc-card-list{margin-top:12px;display:flex;flex-direction:column;gap:5px;}
.mc-card .mc-card-list-item{
  display:flex;justify-content:space-between;align-items:center;
  font-size:.8rem;color:var(--text2);
  padding:5px 0;border-bottom:1px solid var(--border);
}
.mc-card .mc-card-list-item:last-child{border-bottom:none;}
.mc-card .mc-card-list-item span:last-child{font-weight:600;color:var(--text);}
/* Icone rond dans card */
.mc-icon{
  width:44px;height:44px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:1.2rem;margin-bottom:10px;
}
.mc-icon-green{background:rgba(43,122,68,.2);color:#4ade80;}
.mc-icon-red  {background:rgba(192,57,43,.2);color:#f87171;}
.mc-icon-blue {background:rgba(26,107,191,.2);color:#60a5fa;}
/* ── Table ───────────────────────────────────── */
.mc-empty{
  text-align:center;padding:48px 20px;color:var(--text2);
}
.mc-empty .mc-empty-icon{font-size:2.5rem;margin-bottom:10px;}
</style>
@endsection

@section('content')

@php
  $dc      = \Carbon\Carbon::parse($date);
  $isToday = $dc->isToday();
  $prev    = $dc->copy()->subDay()->toDateString();
  $next    = $dc->copy()->addDay()->toDateString();
  $today   = now()->toDateString();
  $agent   = auth()->user();
@endphp

{{-- ══ HEADER BLEU ══════════════════════════════════════════ --}}
<div class="mc-header">
  <div class="mc-header-left">
    <h1>Mon État de Caisse</h1>
    <div class="mc-date">
      <span>Date :</span>
      <strong style="color:#fff;">
        {{ $dc->locale('fr')->isoFormat('dddd, D MMMM YYYY') }}
      </strong>
      {{-- Navigation --}}
      <div class="mc-date-nav">
        <a href="{{ route('caisse.ma_caisse', ['date'=>$prev]) }}" class="mc-btn-nav" title="Jour précédent">‹</a>
        <input type="date" class="mc-date-input" value="{{ $date }}" max="{{ $today }}"
               onchange="window.location.href='{{ route('caisse.ma_caisse') }}?date='+this.value">
        <a href="{{ route('caisse.ma_caisse', ['date'=>$next]) }}"
           class="mc-btn-nav {{ $isToday ? 'disabled' : '' }}" title="Jour suivant">›</a>
        @if(!$isToday)
          <a href="{{ route('caisse.ma_caisse') }}"
             style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);
                    color:#fff;border-radius:6px;padding:3px 10px;font-size:.75rem;
                    text-decoration:none;white-space:nowrap;">
            Aujourd'hui
          </a>
        @endif
      </div>
    </div>
  </div>
  <button class="mc-btn-print" onclick="imprimerMaCaisse()">
    🖨️ Imprimer l'État de Caisse
  </button>
</div>

{{-- ══ ÉTAT DE LA CAISSE ═══════════════════════════════════ --}}
<div class="mc-section-title">État de la Caisse</div>

<div class="mc-cards">

  {{-- Card Ventes --}}
  <div class="mc-card">
    <div class="mc-icon mc-icon-green">💰</div>
    <div class="mc-card-label">Ventes</div>
    <div class="mc-card-value" style="color:#4ade80;">
      {{ number_format($recettes, 0, ',', ' ') }} <span style="font-size:.9rem;">CFA</span>
    </div>
    <div class="mc-card-sub">{{ $nb_bagages }} bagage(s) enregistré(s)</div>
    <div class="mc-card-list">
      <div class="mc-card-list-item">
        <span>{{ $nb_bagages }} Vente(s)</span>
        <span>{{ number_format($recettes, 0, ',', ' ') }} F</span>
      </div>
    </div>
  </div>

  {{-- Card Remboursements (Remises) --}}
  <div class="mc-card">
    <div class="mc-icon mc-icon-red">↩️</div>
    <div class="mc-card-label">Remboursement</div>
    <div class="mc-card-value" style="color:#f87171;">
      {{ number_format($remises, 0, ',', ' ') }} <span style="font-size:.9rem;">CFA</span>
    </div>
    <div class="mc-card-sub">Total remises accordées</div>
    <div class="mc-card-list">
      @if($remises > 0)
        <div class="mc-card-list-item">
          <span>Remises</span>
          <span style="color:#f87171;">{{ number_format($remises, 0, ',', ' ') }} F</span>
        </div>
      @else
        <div class="mc-card-list-item">
          <span style="color:var(--text2);font-style:italic;">0 Remboursement(s)</span>
          <span>0 F</span>
        </div>
      @endif
    </div>
  </div>

  {{-- Card Solde Net --}}
  <div class="mc-card">
    <div class="mc-icon mc-icon-blue">📊</div>
    <div class="mc-card-label">Solde</div>
    <div class="mc-card-value" style="color:#60a5fa;">
      {{ number_format($net, 0, ',', ' ') }} <span style="font-size:.9rem;">CFA</span>
    </div>
    <div class="mc-card-sub">Net à verser</div>
    <div class="mc-card-list">
      <div class="mc-card-list-item">
        <span>Recettes</span>
        <span>{{ number_format($recettes, 0, ',', ' ') }} F</span>
      </div>
      <div class="mc-card-list-item">
        <span>- Remises</span>
        <span style="color:#f87171;">{{ number_format($remises, 0, ',', ' ') }} F</span>
      </div>
      <div class="mc-card-list-item" style="border-top:1px solid var(--gold);margin-top:4px;padding-top:4px;">
        <span style="font-weight:700;color:var(--text);">Net</span>
        <span style="color:#4ade80;font-weight:700;">{{ number_format($net, 0, ',', ' ') }} F</span>
      </div>
    </div>
  </div>

</div>

{{-- ══ DÉTAIL TRANSACTIONS ══════════════════════════════════ --}}
<div class="mc-section-title">Détail des Transactions</div>

<div class="card-dt">
  @if($transactions->isEmpty())
    <div class="mc-empty">
      <div class="mc-empty-icon">📭</div>
      <div>Aucune transaction enregistrée</div>
      <div style="font-size:.8rem;margin-top:6px;">le {{ $dc->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</div>
    </div>
  @else
  <div class="table-responsive-dt">
    <table class="table-dt">
      <thead>
        <tr>
          <th>Heure</th>
          <th>N° Bagage</th>
          <th>Voyageur</th>
          <th>Trajet</th>
          <th>N° Billet</th>
          <th>Tarif</th>
          <th>Remise</th>
          <th>À Verser</th>
        </tr>
      </thead>
      <tbody>
        @foreach($transactions as $b)
        <tr>
          <td style="font-family:'Space Mono',monospace;font-size:.75rem;">
            {{ $b->date_enregistrement->format('H:i') }}
          </td>
          <td><span class="mono">{{ $b->code_bagage }}</span></td>
          <td style="color:var(--text);font-weight:500;">{{ $b->client?->nom_complet ?? '—' }}</td>
          <td style="font-size:.78rem;">{{ $b->voyage?->trajet ?? '—' }}</td>
          <td style="font-size:.75rem;color:var(--text2);">{{ $b->numero_billet ?? '—' }}</td>
          <td><span class="mono">{{ number_format($b->montant, 0, ',', ' ') }} F</span></td>
          <td>
            @if($b->remise > 0)
              <span style="color:#f87171;">{{ number_format($b->remise, 0, ',', ' ') }} F</span>
            @else
              <span style="color:var(--text2);">—</span>
            @endif
          </td>
          <td><span class="mono" style="color:#4ade80;font-weight:600;">{{ number_format($b->net, 0, ',', ' ') }} F</span></td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="border-top:2px solid var(--border);">
          <td colspan="5" style="padding:10px 14px;color:var(--gold);font-weight:700;font-size:.82rem;">
            TOTAL ({{ $nb_bagages }} bagage(s))
          </td>
          <td style="padding:10px 14px;"><span class="mono" style="font-weight:700;">{{ number_format($recettes, 0, ',', ' ') }} F</span></td>
          <td style="padding:10px 14px;"><span style="color:#f87171;font-weight:700;">{{ number_format($remises, 0, ',', ' ') }} F</span></td>
          <td style="padding:10px 14px;"><span class="mono" style="color:#4ade80;font-weight:700;">{{ number_format($net, 0, ',', ' ') }} F</span></td>
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
   IMPRESSION ÉTAT DE CAISSE – Format A4
   Fidèle à l'image de référence (tableau officiel de caisse)
   ══════════════════════════════════════════════════════════════ */
function imprimerMaCaisse() {
  const agent      = '{{ addslashes(auth()->user()->nom_complet) }}';
  const agence     = '{{ addslashes(auth()->user()->agence ?? "—") }}';
  const dateLabel  = '{{ $dc->format("d/m/Y") }}';
  const dateLong   = '{{ $dc->locale("fr")->isoFormat("dddd D MMMM YYYY") }}';
  const nbBagages  = {{ $nb_bagages }};
  const recettes   = {{ $recettes }};
  const remises    = {{ $remises }};
  const net        = {{ $net }};
  const now        = new Date().toLocaleString('fr-FR');

  const fmt = n => parseInt(n).toLocaleString('fr-FR');

  const html = `<!DOCTYPE html>
<html lang="fr"><head>
<meta charset="UTF-8">
<title>Etat de caisse du ${dateLabel}</title>
<style>
  @page { size: A4 portrait; margin: 20mm 22mm; }
  * { box-sizing:border-box; margin:0; padding:0;
      -webkit-print-color-adjust:exact; print-color-adjust:exact; }
  body { font-family:Arial,Helvetica,sans-serif; font-size:11px; color:#000; background:#fff; }
  .company { font-size:10px; color:#555; margin-bottom:6px; }
  .doc-title { font-size:22px; font-weight:bold; text-align:center; margin:0 0 28px; }
  .doc-subtitle { font-size:12px; margin-bottom:18px; }
  table { width:100%; border-collapse:collapse; margin-bottom:14px; }
  th { border:1px solid #000; padding:8px 10px; font-size:11px; font-weight:bold; text-align:center; }
  td { border:1px solid #000; padding:8px 10px; font-size:11px; }
  td.r { text-align:right; font-weight:bold; }
  .total-line { text-align:right; font-size:12px; font-weight:bold; margin:10px 0 32px; }
  .sign-block  { text-align:right; }
  .sign-label  { font-size:12px; font-weight:bold; margin-bottom:36px; display:block; }
  .sign-line   { display:inline-block; width:180px; border-bottom:1px solid #000; margin-top:4px; }
  .footer-date { text-align:right; font-size:9px; color:#555; margin-top:40px; }
  @media print { body{} }
</style>
</head><body>

<div class="company">DIARRA TRANSPORT &nbsp;·&nbsp; Sogoniko – Bamako &nbsp;·&nbsp; Tél : (223) 20802142</div>

<div class="doc-title">Etat de caisse du ${dateLabel}</div>

<div class="doc-subtitle">Etat de caisse de <strong>${agent}</strong> du ${dateLong}</div>

<table>
  <thead>
    <tr>
      <th style="width:38%;">Nom prénom du caissier</th>
      <th style="width:20%;">Vente Billets</th>
      <th style="width:18%;">Remboursements</th>
      <th style="width:13%;">Règlements</th>
      <th style="width:11%;">A Verser</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="font-weight:bold;">${agent}</td>
      <td class="r">${fmt(recettes)}</td>
      <td class="r">${fmt(remises)}</td>
      <td class="r">0</td>
      <td class="r" style="font-size:12px;">${fmt(net)}</td>
    </tr>
  </tbody>
</table>

<div class="total-line">Total a verser : ${fmt(net)}</div>

<div class="sign-block">
  <span class="sign-label">Signature</span>
  <div class="sign-line"></div>
</div>

<div class="footer-date">${new Date().toLocaleDateString('fr-FR',{day:'2-digit',month:'long',year:'numeric'})} ${new Date().toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'})}</div>

<script>window.onload=function(){setTimeout(function(){window.print();},400);};<\/script>
</body></html>`;

  const w = window.open('','_blank','width=900,height=750,scrollbars=yes');
  if (!w) {
    showToast('error','Popup bloqué – autorisez les popups pour ce site.');
    return;
  }
  w.document.open();
  w.document.write(html);
  w.document.close();
}
</script>
@endsection
