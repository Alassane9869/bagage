@extends('layouts.app')
@section('title','Logs Système')

@section('content')
<div class="page-header">
  <h1>📄 Logs Système</h1>
  <p>Journal des activités du système</p>
</div>

<div class="card-dt">
  <div style="overflow-x:auto;">
    <table class="table-dt">
      <thead>
        <tr><th>Date/Heure</th><th>Utilisateur</th><th>Action</th><th>Détail</th></tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td style="white-space:nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
          <td style="color:var(--text);font-weight:500">{{ $log->user?->nom_complet ?? 'Système' }}</td>
          <td>
            <span class="badge-dt badge-{{ $log->badge_color }}">{{ $log->action }}</span>
          </td>
          <td style="font-size:.8rem;max-width:320px;">{{ $log->detail }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;padding:28px;color:var(--text2)">Aucun log</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px;">{{ $logs->links() }}</div>
</div>
@endsection
