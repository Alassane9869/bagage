@extends('layouts.app')
@section('title','Bagages Embarqués')

@section('content')
<div class="page-header">
  <h1>✅ Bagages Embarqués</h1>
  <p>Bagages chargés dans les bus – vérification par véhicule</p>
</div>

<div class="card-dt">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div class="card-title" style="margin-bottom:0">Bagages dans les Bus</div>
    <form method="GET" action="{{ route('gestion.embarques') }}">
      <select name="bus" class="form-select" style="width:auto;" onchange="this.form.submit()">
        <option value="">Tous les bus</option>
        @foreach($buses as $bus)
          <option value="{{ $bus->numero_bus }}" {{ request('bus')==$bus->numero_bus?'selected':'' }}>
            Bus {{ $bus->numero_bus }} – {{ $bus->programme ?? '' }}
          </option>
        @endforeach
      </select>
    </form>
  </div>

  <div style="overflow-x:auto;">
    <table class="table-dt">
      <thead>
        <tr>
          <th>N° Bagage</th><th>Voyageur</th><th>Trajet</th>
          <th>Bus</th><th>Départ</th><th>Statut</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bagages as $b)
        <tr>
          <td><span class="mono">{{ $b->code_bagage }}</span></td>
          <td>
            <div style="color:var(--text);font-weight:500">{{ $b->client?->nom_complet ?? '—' }}</div>
            <div style="font-size:.72rem;color:var(--text2)">{{ $b->client?->telephone ?? '' }}</div>
          </td>
          <td>{{ $b->voyage?->trajet ?? '—' }}</td>
          <td>{{ $b->voyage?->numero_bus ?? '—' }}</td>
          <td>{{ $b->voyage?->date_depart?->format('d/m/Y') ?? '—' }}</td>
          <td><span class="badge-dt badge-{{ $b->statut_color }}">{{ $b->statut_label }}</span></td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text2);">Aucun bagage embarqué</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px;">{{ $bagages->links() }}</div>
</div>
@endsection
