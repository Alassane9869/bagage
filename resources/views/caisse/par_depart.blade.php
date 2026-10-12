@extends('layouts.app')
@section('title','Caisse par Départ')

@section('content')
<div class="page-header">
  <h1>🚌 Caisse par Départ</h1>
  <p>Recettes ventilées par programme de départ</p>
</div>

@php
  $dateCourante = \Carbon\Carbon::parse($date);
  $isToday      = $dateCourante->isToday();
@endphp
<div style="display:flex;gap:8px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
  <a href="{{ route('caisse.par_depart', ['date'=>$dateCourante->copy()->subDay()->toDateString()]) }}"
     style="width:34px;height:34px;border:1px solid var(--border);background:var(--dark3);
            color:var(--text2);border-radius:8px;display:flex;align-items:center;
            justify-content:center;text-decoration:none;">‹</a>

  <form method="GET" action="{{ route('caisse.par_depart') }}" style="display:flex;gap:6px;">
    <input type="date" name="date" class="form-control" style="max-width:180px;"
           value="{{ $date }}" onchange="this.form.submit()">
  </form>

  <a href="{{ route('caisse.par_depart', ['date'=>$dateCourante->copy()->addDay()->toDateString()]) }}"
     style="width:34px;height:34px;border:1px solid var(--border);background:var(--dark3);
            color:var(--text2);border-radius:8px;display:flex;align-items:center;
            justify-content:center;text-decoration:none;
            {{ $isToday ? 'opacity:.4;pointer-events:none;' : '' }}">›</a>

  @if(!$isToday)
    <a href="{{ route('caisse.par_depart') }}"
       style="background:var(--dark3);border:1px solid var(--border);color:var(--text2);
              border-radius:8px;padding:6px 12px;font-size:.78rem;text-decoration:none;">
      Aujourd'hui
    </a>
  @endif

  <span style="font-size:.82rem;color:var(--text2);">
    {{ $dateCourante->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
  </span>
</div>

<div class="card-dt">
  <div class="card-title">Par Programme</div>
  <div class="table-responsive-dt">
    <table class="table-dt">
      <thead>
        <tr><th>Programme</th><th>Nb Bagages</th><th>Recettes</th><th>Remises</th><th>Net</th></tr>
      </thead>
      <tbody>
        @forelse($lignes as $l)
        <tr>
          <td style="color:var(--text);font-weight:500">{{ $l['programme'] ?? '—' }}</td>
          <td style="text-align:center">{{ $l['nb_bagages'] }}</td>
          <td><span class="mono">{{ number_format($l['recettes'], 0, ',', ' ') }} FCFA</span></td>
          <td><span style="color:#f87171">{{ number_format($l['remises'], 0, ',', ' ') }} FCFA</span></td>
          <td><span class="mono" style="color:#4ade80">{{ number_format($l['net'], 0, ',', ' ') }} FCFA</span></td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;padding:28px;color:var(--text2)">Aucune donnée pour cette date</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
