@extends('layouts.app')
@section('title','Bagages Enregistrés')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-clipboard-data"></i> Colis Enregistrés</h1>
  <p>Liste de tous les colis enregistrés avec leur numéro d'identification</p>
</div>

<div class="card-dt">
  {{-- Filtres --}}
  <form method="GET" action="{{ route('bagages.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
    <input type="text" name="search" class="form-control" style="max-width:280px;"
           placeholder="Rechercher N° Bagage, billet, voyageur…" value="{{ request('search') }}">
    <select name="statut" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
      <option value="">Tous les Bagages</option>
      <option value="enregistre" {{ request('statut')==='enregistre' ? 'selected':'' }}>Enregistré</option>
      <option value="embarque"   {{ request('statut')==='embarque'   ? 'selected':'' }}>Embarqué</option>
    </select>
    <button type="submit" class="btn-gold" style="padding:8px 16px;"><i class="bi bi-search"></i> Filtrer</button>
    @if(request('search') || request('statut'))
      <a href="{{ route('bagages.index') }}" class="btn-reset" style="padding:8px 14px;display:flex;align-items:center;"><i class="bi bi-x-lg"></i> Effacer</a>
    @endif
  </form>

  <div style="overflow-x:auto;">
    <table class="table-dt">
      <thead>
        <tr>
          <th>N° Bagage</th>
          <th>Voyageur</th>
          <th>Contact</th>
          <th>Trajet</th>
          <th>Colis</th>
          <th>Tarif</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bagages as $b)
        <tr>
          <td><span class="mono">{{ $b->code_bagage }}</span></td>
          <td>
            <div style="font-weight:500;color:var(--text)">{{ $b->client?->nom_complet ?? '—' }}</div>
            @if($b->numero_billet)
              <div style="font-size:.72rem;color:var(--text2)">Billet: {{ $b->numero_billet }}</div>
            @endif
          </td>
          <td>{{ $b->client?->telephone ?? '—' }}</td>
          <td>
            <div style="color:var(--text)">{{ $b->voyage?->trajet ?? '—' }}</div>
            @if($b->voyage)
              <div style="font-size:.72rem;color:var(--text2)">{{ $b->voyage->date_depart?->format('d/m/Y') }}</div>
            @endif
          </td>
          <td style="text-align:center">{{ $b->nombre_colis }}</td>
          <td>
            <span class="mono">{{ $b->montant_fcfa }}</span>
            @if($b->remise > 0)
              <div style="font-size:.72rem;color:#f87171">-{{ $b->remise_fcfa }}</div>
            @endif
          </td>
          <td>
            <span class="badge-dt badge-{{ $b->statut_color }}">{{ $b->statut_label }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <button class="topbar-btn" title="Ré-imprimer reçu"
                      onclick="fetchAndPrintRecu({{ $b->id }})"><i class="bi bi-printer"></i></button>
              @if($b->statut === 'enregistre')
                <a href="{{ route('embarquement.index', ['bagage' => $b->code_bagage]) }}"
                   class="topbar-btn" title="Embarquer"><i class="bi bi-truck-front"></i></a>
              @endif
              <a href="{{ route('suivi.show', $b->code_bagage) }}"
                 class="topbar-btn" title="Suivi"><i class="bi bi-search"></i></a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text2);">Aucun bagage trouvé</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:16px;">
    {{ $bagages->links() }}
  </div>
</div>

@include('bagages.partials.modal-recu')

@endsection

@section('scripts')
@include('bagages.partials.modal-recu-js')
<script>
function fetchAndPrintRecu(id) {
  fetch(`/bagages/${id}/receipt`)
    .then(r => r.json())
    .then(data => openModalRecu(data.bagage, data.qr_code))
    .catch(() => showToast('error','Impossible de charger le reçu'));
}
</script>
@endsection
