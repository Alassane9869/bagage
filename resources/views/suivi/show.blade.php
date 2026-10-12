@extends('layouts.app')
@section('title','Suivi Bagage '.$bagage->code_bagage)

@section('content')
<div class="page-header">
  <h1><i class="bi bi-search"></i> Suivi Colis</h1>
  <p>Historique de traçabilité du colis</p>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;" class="info-grid">
  <div class="card-dt">
    <div class="card-title"><i class="bi bi-box-seam"></i> Informations Colis</div>
    <table style="width:100%;font-size:.85rem;">
      <tr><td style="color:var(--text2);padding:5px 0;width:40%">N° Bagage</td><td><span class="mono">{{ $bagage->code_bagage }}</span></td></tr>
      <tr><td style="color:var(--text2);padding:5px 0">Voyageur</td><td style="color:var(--text)">{{ $bagage->client?->nom_complet ?? '—' }}</td></tr>
      <tr><td style="color:var(--text2);padding:5px 0">Trajet</td><td style="color:var(--text)">{{ $bagage->voyage?->trajet ?? '—' }}</td></tr>
      <tr><td style="color:var(--text2);padding:5px 0">Nature</td><td style="color:var(--text)">{{ $bagage->nature ?? '—' }}</td></tr>
      <tr><td style="color:var(--text2);padding:5px 0">Nb Colis</td><td style="color:var(--text)">{{ $bagage->nombre_colis }}</td></tr>
      <tr><td style="color:var(--text2);padding:5px 0">Statut actuel</td>
        <td><span class="badge-dt badge-{{ $bagage->statut_color }}">{{ $bagage->statut_label }}</span></td>
      </tr>
    </table>
  </div>

  <div class="card-dt" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;">
    <div style="font-size:.8rem;color:var(--text2)">QR Code du Bagage</div>
    <div>{!! $qrCode !!}</div>
    <div style="font-family:'Space Mono',monospace;font-size:.72rem;color:var(--gold);">
      {{ $bagage->code_bagage }}
    </div>
  </div>
</div>

{{-- Timeline --}}
<div class="card-dt">
  <div class="card-title">📍 Historique de Traçabilité</div>
  <div style="position:relative;padding-left:24px;">
    @forelse($bagage->suivis as $s)
    <div style="position:relative;padding:0 0 20px 20px;border-left:2px solid {{ $s->couleur }};">
      <div style="position:absolute;left:-7px;top:4px;width:12px;height:12px;border-radius:50%;background:{{ $s->couleur }};"></div>
      <div style="font-size:.75rem;color:var(--text2)">{{ $s->created_at->format('d/m/Y H:i') }}</div>
      <div style="margin-top:3px;">
        <span class="badge-dt badge-{{ \App\Models\Bagage::STATUTS[$s->statut]['color'] ?? 'secondary' }}">
          {{ \App\Models\Bagage::STATUTS[$s->statut]['label'] ?? $s->statut }}
        </span>
        @if($s->localisation)
          <span style="font-size:.8rem;color:var(--text2);margin-left:8px;">📍 {{ $s->localisation }}</span>
        @endif
      </div>
      @if($s->commentaire)
        <div style="font-size:.8rem;color:var(--text2);margin-top:3px;">{{ $s->commentaire }}</div>
      @endif
      @if($s->user)
        <div style="font-size:.72rem;color:var(--text2);margin-top:2px;"><i class="bi bi-person"></i> {{ $s->user->nom_complet }}</div>
      @endif
    </div>
    @empty
    <p style="color:var(--text2);">Aucun historique disponible.</p>
    @endforelse
  </div>
</div>
@endsection

@section('styles')
<style>
@media(max-width:640px){.info-grid{grid-template-columns:1fr !important;}}
</style>
@endsection
