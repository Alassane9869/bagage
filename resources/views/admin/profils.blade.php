@extends('layouts.app')
@section('title','Profils Utilisateurs')

@section('content')
<div class="page-header">
  <h1>🔑 Profils Utilisateurs</h1>
  <p>Gestion des rôles et permissions</p>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
  @php
  $roleIcons = [
    'administrateur' => ['🛡','Administrateur','Accès complet à tous les modules','danger'],
    'agent_bagages'  => ['📦','Agent Bagages','Enregistrement et embarquement bagages','success'],
    'controleur'     => ['🔍','Contrôleur','Consultation et suivi des bagages','secondary'],
  ];
  @endphp

  @foreach($roles as $role)
  @php $info = $roleIcons[$role->slug] ?? ['🔑', $role->nom, '', 'secondary']; @endphp
  <div class="card-dt" style="display:flex;gap:14px;align-items:flex-start;">
    <div style="width:48px;height:48px;border-radius:12px;background:var(--dark3);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
      {{ $info[0] }}
    </div>
    <div style="flex:1">
      <div style="font-weight:600;color:var(--text);margin-bottom:4px;">{{ $info[1] }}</div>
      <div style="font-size:.8rem;color:var(--text2);margin-bottom:8px;">{{ $info[2] }}</div>
      <div style="font-size:.75rem;color:var(--text2)">
        <span class="badge-dt badge-{{ $info[3] }}">{{ $role->users_count }} utilisateur(s)</span>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
