@extends('layouts.app')
@section('title','Embarquement des Bagages')

@section('content')
<div class="page-header">
  <h1>🚌 Embarquement des Bagages</h1>
  <p>Vérifier et valider le chargement dans le bus</p>
</div>

{{-- Recherche par code --}}
<div class="card-dt" style="margin-bottom:16px;">
  <div class="card-title">🔍 Rechercher un bagage par numéro</div>
  <form method="GET" action="{{ route('embarquement.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;">
    <input type="text" name="search" class="form-control" style="max-width:320px;"
           placeholder="N° Bagage ou N° Billet…" value="{{ $search ?? '' }}">
    <button type="submit" class="btn-gold" style="padding:8px 18px;">Rechercher</button>
    @if($search)
      <a href="{{ route('embarquement.index') }}" class="btn-reset" style="padding:8px 14px;display:flex;align-items:center;">✕</a>
    @endif
  </form>

  @if($search)
    @if($found)
      <div style="margin-top:14px;background:var(--dark3);border-radius:8px;padding:14px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <div>
          <div class="mono">{{ $found->code_bagage }}</div>
          <div style="color:var(--text);font-weight:500;margin-top:4px;">{{ $found->client?->nom_complet ?? '—' }}</div>
          <div style="font-size:.78rem;color:var(--text2)">{{ $found->voyage?->trajet ?? '—' }} | {{ $found->nature }} | {{ $found->nombre_colis }} colis</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
          <span class="badge-dt badge-{{ $found->statut_color }}">{{ $found->statut_label }}</span>
          @if($found->statut === 'enregistre')
            <input type="text" id="busSearch" class="form-control" placeholder="N° Bus" style="width:110px;">
            <button type="button" class="btn-gold" style="padding:8px 14px;"
                    onclick="embarquerBagage({{ $found->id }}, document.getElementById('busSearch').value)">
              🚌 Embarquer
            </button>
          @endif
        </div>
      </div>
    @else
      <div style="margin-top:12px;color:#f87171;font-size:.85rem;">❌ Aucun bagage trouvé pour "{{ $search }}"</div>
    @endif
  @endif
</div>

{{-- Table bagages à embarquer --}}
<div class="card-dt">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div class="card-title" style="margin-bottom:0">Bagages à Embarquer</div>
    <form method="GET" action="{{ route('embarquement.index') }}">
      <select name="bus" class="form-select" style="width:auto;" onchange="this.form.submit()">
        <option value="">-- Filtrer par Bus --</option>
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
          <th>Bus Assigné</th><th>Colis</th><th>Statut</th><th>Action</th>
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
          <td>
            <div style="color:var(--text)">{{ $b->voyage?->trajet ?? '—' }}</div>
            <div style="font-size:.72rem;color:var(--text2)">{{ $b->voyage?->programme ?? '' }}</div>
          </td>
          <td>{{ $b->voyage?->numero_bus ?? '—' }}</td>
          <td style="text-align:center">{{ $b->nombre_colis }}</td>
          <td><span class="badge-dt badge-{{ $b->statut_color }}">{{ $b->statut_label }}</span></td>
          <td>
            <form method="POST" action="{{ route('embarquement.embarquer', $b->id) }}" style="display:flex;gap:6px;align-items:center;">
              @csrf @method('PATCH')
              <input type="text" name="numero_bus" class="form-control" placeholder="N° Bus"
                     value="{{ $b->voyage?->numero_bus ?? '' }}" style="width:90px;padding:5px 8px;font-size:.75rem;">
              <button type="submit" class="btn-gold" style="padding:6px 12px;font-size:.78rem;white-space:nowrap;">🚌 Embarquer</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text2);">Aucun bagage en attente d'embarquement</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px;">{{ $bagages->links() }}</div>
</div>

{{-- Formulaire caché pour embarquement AJAX-like --}}
<form id="embarquerForm" method="POST" style="display:none;">
  @csrf
  @method('PATCH')
  <input type="hidden" name="numero_bus" id="embarquerBus">
</form>

@endsection

@section('scripts')
<script>
function embarquerBagage(id, bus) {
  if (!bus.trim()) {
    showToast('warning', 'Veuillez saisir un numéro de bus.');
    return;
  }
  const form = document.getElementById('embarquerForm');
  form.action = '/embarquement/' + id;
  document.getElementById('embarquerBus').value = bus;
  form.submit();
}
</script>
@endsection
