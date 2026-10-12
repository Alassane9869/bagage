@extends('layouts.app')
@section('title','Utilisateurs Système')

@section('styles')
<style>
@media(max-width: 600px) {
  .user-modal-box { padding: 16px !important; max-height: 90vh; overflow-y: auto; }
  .user-modal-box .user-grid-2 { grid-template-columns: 1fr !important; }
}
</style>
@endsection

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
  <div>
    <h1>👥 Utilisateurs Système</h1>
    <p>Gestion des comptes utilisateurs</p>
  </div>
  <button class="btn-gold" onclick="document.getElementById('modalNewUser').style.display='flex'">+ Nouvel Utilisateur</button>
</div>

<div class="card-dt">
  <div class="table-responsive-dt">
    <table class="table-dt">
      <thead>
        <tr><th>Nom</th><th>Login</th><th>Rôle</th><th>Agence</th><th>Statut</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($users as $u)
        <tr>
          <td style="color:var(--text);font-weight:500">{{ $u->nom_complet }}</td>
          <td><span class="mono" style="font-size:.78rem">{{ $u->login }}</span></td>
          <td>
            <span class="badge-dt badge-{{ $u->role?->slug === 'administrateur' ? 'danger' : ($u->role?->slug === 'agent_bagages' ? 'success' : 'secondary') }}">
              {{ $u->role?->nom ?? '—' }}
            </span>
          </td>
          <td>{{ $u->agence ?? '—' }}</td>
          <td>
            <span class="badge-dt badge-{{ $u->statut === 'actif' ? 'success' : 'secondary' }}">
              {{ ucfirst($u->statut) }}
            </span>
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <button class="topbar-btn" title="Modifier"
                      onclick="openEditModal({{ $u->id }},'{{ addslashes($u->prenom) }}','{{ addslashes($u->nom) }}','{{ addslashes($u->login) }}','{{ addslashes($u->email ?? '') }}','{{ addslashes($u->telephone ?? '') }}','{{ $u->role_id }}','{{ addslashes($u->agence ?? '') }}','{{ $u->statut }}')">✏️</button>
              @if($u->id !== auth()->id())
              <form method="POST" action="{{ route('admin.utilisateurs.destroy', $u->id) }}"
                    onsubmit="return confirm('Supprimer cet utilisateur ?')">
                @csrf @method('DELETE')
                <button type="submit" class="topbar-btn" title="Supprimer">🗑</button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:28px;color:var(--text2)">Aucun utilisateur</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px;">{{ $users->links() }}</div>
</div>

{{-- Modal Nouvel Utilisateur --}}
<div id="modalNewUser" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:400;align-items:center;justify-content:center;padding:16px;">
  <div class="user-modal-box" style="background:var(--dark2);border:1px solid var(--border);border-radius:14px;width:100%;max-width:480px;padding:24px;">
    <div style="display:flex;justify-content:space-between;margin-bottom:20px;">
      <h3 style="color:var(--gold);font-size:1rem;">+ Nouvel Utilisateur</h3>
      <button onclick="document.getElementById('modalNewUser').style.display='none'" style="background:none;border:none;color:var(--text2);font-size:1.3rem;cursor:pointer;">✕</button>
    </div>
    <form method="POST" action="{{ route('admin.utilisateurs.store') }}">
      @csrf
      @if($errors->any())
      <div style="background:rgba(192,57,43,.15);border:1px solid rgba(192,57,43,.3);border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:.8rem;color:#f87171;">
        <strong>Erreurs :</strong>
        <ul style="margin:4px 0 0 14px;">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
      @endif
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
        <div><label class="form-label">Prénom *</label><input type="text" name="prenom" class="form-control" required></div>
        <div><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" placeholder="(optionnel)"></div>
      </div>
      <div style="margin-bottom:12px;"><label class="form-label">Login * <span style="font-size:.72rem;color:var(--text2);">(identifiant de connexion — lettres, chiffres, point…)</span></label><input type="text" name="login" class="form-control" required placeholder="Ex: yacou.diakite, agent01, admin…"></div>
      <div style="margin-bottom:12px;"><label class="form-label">Email <span style="font-size:.72rem;color:var(--text2);">(optionnel)</span></label><input type="email" name="email" class="form-control" placeholder="exemple@mail.com"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
        <div><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control"></div>
        <div><label class="form-label">Agence</label><input type="text" name="agence" class="form-control"></div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
        <div>
          <label class="form-label">Rôle *</label>
          <select name="role_id" class="form-select" required>
            @foreach($roles as $r)
              <option value="{{ $r->id }}">{{ $r->nom }}</option>
            @endforeach
          </select>
        </div>
        <div><label class="form-label">Mot de Passe *</label><input type="password" name="password" class="form-control" required></div>
      </div>
      <div style="display:flex;gap:10px;margin-top:16px;">
        <button type="submit" class="btn-gold">Créer</button>
        <button type="button" class="btn-reset" onclick="document.getElementById('modalNewUser').style.display='none'">Annuler</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Modifier --}}
<div id="modalEditUser" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:400;align-items:center;justify-content:center;padding:16px;">
  <div class="user-modal-box" style="background:var(--dark2);border:1px solid var(--border);border-radius:14px;width:100%;max-width:480px;padding:24px;">
    <div style="display:flex;justify-content:space-between;margin-bottom:20px;">
      <h3 style="color:var(--gold);font-size:1rem;">✏️ Modifier Utilisateur</h3>
      <button onclick="document.getElementById('modalEditUser').style.display='none'" style="background:none;border:none;color:var(--text2);font-size:1.3rem;cursor:pointer;">✕</button>
    </div>
    <form method="POST" id="editUserForm">
      @csrf @method('PUT')
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
        <div><label class="form-label">Prénom *</label><input type="text" name="prenom" id="ep" class="form-control" required></div>
        <div><label class="form-label">Nom</label><input type="text" name="nom" id="en" class="form-control" placeholder="(optionnel)"></div>
      </div>
      <div style="margin-bottom:12px;"><label class="form-label">Login *</label><input type="text" name="login" id="el" class="form-control" required></div>
      <div style="margin-bottom:12px;"><label class="form-label">Email <span style="font-size:.72rem;color:var(--text2);">(optionnel)</span></label><input type="email" name="email" id="ee" class="form-control"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
        <div><label class="form-label">Téléphone</label><input type="text" name="telephone" id="et" class="form-control"></div>
        <div><label class="form-label">Agence</label><input type="text" name="agence" id="ea" class="form-control"></div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
        <div>
          <label class="form-label">Rôle *</label>
          <select name="role_id" id="er" class="form-select" required>
            @foreach($roles as $r)
              <option value="{{ $r->id }}">{{ $r->nom }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Statut</label>
          <select name="statut" id="es" class="form-select">
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
          </select>
        </div>
      </div>
      <div style="margin-bottom:12px;"><label class="form-label">Nouveau MDP (laisser vide = inchangé)</label><input type="password" name="password" class="form-control"></div>
      <div style="display:flex;gap:10px;margin-top:16px;">
        <button type="submit" class="btn-gold">Enregistrer</button>
        <button type="button" class="btn-reset" onclick="document.getElementById('modalEditUser').style.display='none'">Annuler</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Ouvrir automatiquement la modal si erreurs de validation
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('modalNewUser').style.display = 'flex';
});
@endif
function openEditModal(id, prenom, nom, login, email, tel, roleId, agence, statut) {
  document.getElementById('ep').value = prenom;
  document.getElementById('en').value = nom;
  document.getElementById('el').value = login;
  document.getElementById('ee').value = email;
  document.getElementById('et').value = tel || '';
  document.getElementById('ea').value = agence || '';
  document.getElementById('er').value = roleId;
  document.getElementById('es').value = statut;
  document.getElementById('editUserForm').action = `/admin/utilisateurs/${id}`;
  document.getElementById('modalEditUser').style.display = 'flex';
}
</script>
@endsection
