@extends('layouts.app')
@section('title','Mon Profil')

@section('content')
<div class="page-header">
  <h1>👤 Mon Profil</h1>
  <p>Informations de votre compte</p>
</div>

<div style="display:grid;grid-template-columns:auto 1fr;gap:20px;align-items:start;" class="profil-grid">

  {{-- Carte identité --}}
  <div class="card-dt" style="text-align:center;min-width:220px;">
    <div style="width:72px;height:72px;border-radius:50%;background:var(--gold);color:#0e1621;font-size:1.8rem;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
      {{ $user->initiale }}
    </div>
    <div style="font-weight:700;font-size:1rem;color:var(--text);">{{ $user->nom_complet }}</div>
    <div style="font-size:.8rem;color:var(--text2);margin-top:4px;">{{ $user->role?->nom ?? '—' }}</div>
    <div style="margin-top:16px;display:flex;flex-direction:column;gap:6px;text-align:left;font-size:.78rem;color:var(--text2);">
      <div>📍 Agence {{ $user->agence ?? '—' }}</div>
      @if($derniereConnexion)
        <div>📅 Dernière connexion le {{ $derniereConnexion->logged_at?->format('d/m/Y H:i') }}</div>
      @endif
      <div>🔐 Guichet : {{ $user->telephone ?? '—' }}</div>
    </div>
  </div>

  {{-- Infos + MDP --}}
  <div style="display:flex;flex-direction:column;gap:16px;">
    <div class="card-dt">
      <div class="card-title">Informations personnelles</div>
      <table style="width:100%;font-size:.85rem;">
        <tr><td style="color:var(--text2);padding:6px 0;width:35%">Prénom</td><td style="color:var(--text)">{{ $user->prenom }}</td></tr>
        <tr><td style="color:var(--text2);padding:6px 0">Nom</td><td style="color:var(--text)">{{ $user->nom }}</td></tr>
        <tr><td style="color:var(--text2);padding:6px 0">Login</td><td><span class="mono">{{ $user->email }}</span></td></tr>
        <tr><td style="color:var(--text2);padding:6px 0">Rôle</td><td style="color:var(--text)">{{ $user->role?->nom ?? '—' }}</td></tr>
        <tr><td style="color:var(--text2);padding:6px 0">Agence</td><td style="color:var(--text)">{{ $user->agence ?? '—' }}</td></tr>
        <tr><td style="color:var(--text2);padding:6px 0">Téléphone</td><td style="color:var(--text)">{{ $user->telephone ?? '—' }}</td></tr>
      </table>
    </div>

    <div class="card-dt">
      <div class="card-title">Modifier le mot de passe</div>
      <form method="POST" action="{{ route('profil.update_password') }}" style="display:grid;gap:12px;max-width:380px;">
        @csrf @method('PUT')
        <div>
          <label class="form-label">Mot de passe actuel *</label>
          <input type="password" name="current_password" class="form-control" required>
          @error('current_password')<div style="color:#f87171;font-size:.75rem;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="form-label">Nouveau mot de passe *</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div>
          <label class="form-label">Confirmer *</label>
          <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div><button type="submit" class="btn-gold" style="padding:9px 20px;">Modifier</button></div>
      </form>
    </div>
  </div>
</div>
@endsection
@section('styles')
<style>@media(max-width:640px){.profil-grid{grid-template-columns:1fr!important;}}</style>
@endsection
