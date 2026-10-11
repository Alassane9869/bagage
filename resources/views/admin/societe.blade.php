@extends('layouts.app')
@section('title','Paramètres')

@section('styles')
<style>
/* ── Tabs ───────────────────────────── */
.param-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:24px;
  border-bottom:1px solid var(--border);padding-bottom:0;}
.param-tab{
  padding:9px 18px;border-radius:8px 8px 0 0;font-size:.83rem;font-weight:500;
  cursor:pointer;border:1px solid transparent;border-bottom:none;
  color:var(--text2);background:none;transition:.15s;margin-bottom:-1px;
  font-family:inherit;
}
.param-tab:hover{color:var(--text);background:var(--dark3);}
.param-tab.active{
  color:var(--gold);background:var(--dark2);
  border-color:var(--border);border-bottom-color:var(--dark2);font-weight:600;
}
.param-section{display:none;}
.param-section.active{display:block;}
/* ── Cards param ────────────────────── */
.param-card{
  background:var(--dark2);border:1px solid var(--border);
  border-radius:12px;padding:22px 24px;margin-bottom:20px;
}
.param-card-title{
  font-size:.82rem;font-weight:700;color:var(--gold);
  letter-spacing:.06em;text-transform:uppercase;
  margin-bottom:18px;display:flex;align-items:center;gap:8px;
  padding-bottom:12px;border-bottom:1px solid var(--border);
}
/* ── Info display (lecture seule) ───── */
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
@media(max-width:640px){.info-grid{grid-template-columns:1fr;}}
.info-item{
  background:var(--dark3);border-radius:8px;padding:12px 14px;
  border:1px solid var(--border);
}
.info-item .lbl{font-size:.7rem;color:var(--text2);text-transform:uppercase;
  letter-spacing:.06em;margin-bottom:4px;}
.info-item .val{font-size:.9rem;color:var(--text);font-weight:500;}
.info-item .val.mono{font-family:'Space Mono',monospace;color:var(--gold);font-size:.82rem;}
/* ── Stat mini ──────────────────────── */
.mini-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;}
@media(max-width:640px){.mini-stats{grid-template-columns:1fr;}}
.mini-stat{
  background:var(--dark3);border:1px solid var(--border);border-radius:10px;
  padding:14px 16px;text-align:center;
}
.mini-stat .ms-val{font-size:1.6rem;font-weight:700;font-family:'Space Mono',monospace;color:var(--gold);}
.mini-stat .ms-lbl{font-size:.72rem;color:var(--text2);margin-top:2px;}
</style>
@endsection

@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
  <div>
    <h1>⚙️ Paramètres</h1>
    <p>Configuration centrale de l'application Diarra Transport</p>
  </div>
  <div style="font-size:.78rem;color:var(--text2);">
    Dernière modification :
    <span style="color:var(--gold);">
      {{ $societe->updated_at ? $societe->updated_at->locale('fr')->diffForHumans() : 'jamais' }}
    </span>
  </div>
</div>

{{-- ── ONGLETS ──────────────────────────────────────── --}}
<div class="param-tabs">
  <button class="param-tab active" onclick="showTab('identite',this)">🏢 Identité</button>
  <button class="param-tab"        onclick="showTab('contact',this)">📞 Contact</button>
  <button class="param-tab"        onclick="showTab('juridique',this)">📋 Juridique</button>
  <button class="param-tab"        onclick="showTab('appli',this)">🔧 Application</button>
  <button class="param-tab"        onclick="showTab('stats',this)">📊 Statistiques</button>
</div>

<form id="mainParamForm" method="POST" action="{{ route('admin.societe.update') }}">
@csrf @method('PUT')

{{-- ══ ONGLET 1 — IDENTITÉ ══════════════════════════════ --}}
<div class="param-section active" id="tab-identite">

  {{-- ── BLOC LOGO ─────────────────────────────────── --}}
  <div class="param-card">
    <div class="param-card-title">🖼️ Logo de la Société</div>

    <div style="display:flex;align-items:flex-start;gap:24px;flex-wrap:wrap;">

      {{-- Aperçu logo actuel --}}
      <div style="text-align:center;">
        <div id="logoPreviewWrap" style="width:120px;height:120px;border-radius:50%;
             background:var(--dark3);border:2px solid var(--border);
             display:flex;align-items:center;justify-content:center;overflow:hidden;
             margin-bottom:10px;">
          @if($societe->logo_path)
            <img id="logoPreview" src="{{ asset('storage/'.$societe->logo_path) }}"
                 style="width:100%;height:100%;object-fit:contain;border-radius:50%;"
                 alt="Logo actuel">
          @else
            <div id="logoPreview">
              @include('partials.logo-svg', ['size'=>100])
            </div>
          @endif
        </div>
        <div style="font-size:.72rem;color:var(--text2);">Aperçu actuel</div>
      </div>

      {{-- Zone upload --}}
      <div style="flex:1;min-width:260px;">
        <div style="font-size:.8rem;color:var(--text2);margin-bottom:12px;line-height:1.6;">
          Le logo apparaît dans la topbar, la page de connexion,
          les reçus imprimés et les étiquettes bagage.
        </div>

        {{-- Zone de drop --}}
        <div id="dropZone"
             style="border:2px dashed var(--border);border-radius:10px;
                    padding:24px;text-align:center;cursor:pointer;
                    background:var(--dark3);transition:.2s;margin-bottom:12px;"
             onclick="document.getElementById('logoFileInput').click()"
             ondragover="event.preventDefault();this.style.borderColor='var(--gold)'"
             ondragleave="this.style.borderColor='var(--border)'"
             ondrop="handleLogoDrop(event)">
          <div style="font-size:2rem;margin-bottom:6px;">📁</div>
          <div style="font-size:.85rem;color:var(--text);font-weight:500;">
            Cliquer ou glisser-déposer une image
          </div>
          <div style="font-size:.72rem;color:var(--text2);margin-top:4px;">
            PNG, JPG, SVG, WEBP · Max 2 Mo · Recommandé : 200×200px
          </div>
        </div>

        <input type="file" id="logoFileInput" accept="image/png,image/jpeg,image/svg+xml,image/webp"
               style="display:none;" onchange="handleLogoChange(this)">

        {{-- Boutons action --}}
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <button type="button" class="btn-gold" id="btnUploadLogo"
                  onclick="uploadLogo()" style="display:none;padding:8px 18px;">
            ⬆️ Envoyer ce logo
          </button>
          @if($societe->logo_path)
          <button type="button" id="btnDeleteLogo"
                  style="background:rgba(192,57,43,.15);border:1px solid rgba(192,57,43,.3);
                         color:#f87171;border-radius:8px;padding:8px 14px;
                         font-size:.82rem;cursor:pointer;font-family:inherit;"
                  onclick="deleteLogo()">
            🗑️ Supprimer le logo
          </button>
          @endif
        </div>

        {{-- Barre de progression --}}
        <div id="uploadProgress" style="display:none;margin-top:12px;">
          <div style="background:var(--dark3);border-radius:4px;height:6px;overflow:hidden;">
            <div id="progressBar" style="height:100%;background:var(--gold);width:0%;transition:.3s;border-radius:4px;"></div>
          </div>
          <div id="uploadMsg" style="font-size:.75rem;color:var(--text2);margin-top:4px;"></div>
        </div>

      </div>
    </div>
  </div>

  {{-- ── BLOC INFOS IDENTITÉ ─────────────────────── --}}
  <div class="param-card">
    <div class="param-card-title">🏢 Identité de la Société</div>
    <div style="display:grid;gap:16px;">

      <div>
        <label class="form-label">Raison Sociale *</label>
        <input type="text" name="raison_sociale" class="form-control"
               value="{{ old('raison_sociale', $societe->raison_sociale ?? 'DIARRA TRANSPORT') }}"
               required placeholder="DIARRA TRANSPORT"
               style="font-size:1rem;font-weight:600;">
        <div style="font-size:.72rem;color:var(--text2);margin-top:4px;">
          Apparaît sur les reçus, étiquettes et tous les documents imprimés.
        </div>
      </div>

      <div class="info-grid">
        <div>
          <label class="form-label">Ville</label>
          <input type="text" name="ville" class="form-control"
                 value="{{ old('ville', $societe->ville ?? 'Bamako') }}" placeholder="Bamako">
        </div>
        <div>
          <label class="form-label">Pays</label>
          <input type="text" name="pays" class="form-control"
                 value="{{ old('pays', $societe->pays ?? 'Mali') }}" placeholder="Mali">
        </div>
      </div>

      <div>
        <label class="form-label">Adresse complète</label>
        <input type="text" name="adresse" class="form-control"
               value="{{ old('adresse', $societe->adresse ?? 'Près de la maternité Sogoniko – Bamako, Mali') }}"
               placeholder="Près de la maternité Sogoniko – Bamako, Mali">
      </div>

      <div>
        <label class="form-label">Mentions pied de reçu</label>
        <textarea name="mentions_recu" class="form-control" rows="2"
                  placeholder="Texte affiché en bas des reçus clients...">{{ old('mentions_recu', $societe->mentions_recu ?? 'Informer le guichet pour toute annulation ou report de voyage avant le départ. Guichet 20802142. La validité du billet est de deux semaines.') }}</textarea>
        <div style="font-size:.72rem;color:var(--text2);margin-top:4px;">
          Ce texte apparaît dans la section "AVIS IMPORTANT" sur les reçus imprimés.
        </div>
      </div>

    </div>
  </div>
</div>

{{-- ══ ONGLET 2 — CONTACT ═══════════════════════════════ --}}
<div class="param-section" id="tab-contact">
  <div class="param-card">
    <div class="param-card-title">📞 Coordonnées & Contact</div>
    <div style="display:grid;gap:16px;">

      <div class="info-grid">
        <div>
          <label class="form-label">Téléphone Principal</label>
          <input type="text" name="telephone" class="form-control"
                 value="{{ old('telephone', $societe->telephone ?? '(223) 20802142') }}"
                 placeholder="(223) 20802142">
          <div style="font-size:.72rem;color:var(--text2);margin-top:4px;">Affiché sur les reçus et étiquettes.</div>
        </div>
        <div>
          <label class="form-label">Numéro Guichet</label>
          <input type="text" name="guichet_principal" class="form-control"
                 value="{{ old('guichet_principal', $societe->guichet_principal ?? '20802142') }}"
                 placeholder="20802142">
          <div style="font-size:.72rem;color:var(--text2);margin-top:4px;">Numéro de guichet de paiement.</div>
        </div>
      </div>

      <div class="info-grid">
        <div>
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control"
                 value="{{ old('email', $societe->email ?? 'contact@diarratransport.ml') }}"
                 placeholder="contact@diarratransport.ml">
        </div>
        <div>
          <label class="form-label">Site Web</label>
          <input type="url" name="site_web" class="form-control"
                 value="{{ old('site_web', $societe->site_web) }}"
                 placeholder="https://www.diarratransport.ml">
        </div>
      </div>

    </div>
  </div>
</div>

{{-- ══ ONGLET 3 — JURIDIQUE ═════════════════════════════ --}}
<div class="param-section" id="tab-juridique">
  <div class="param-card">
    <div class="param-card-title">📋 Informations Juridiques & Fiscales</div>
    <div style="display:grid;gap:16px;">

      <div class="info-grid">
        <div>
          <label class="form-label">N° RCCM <span style="font-size:.7rem;color:var(--text2);">(Registre du Commerce)</span></label>
          <input type="text" name="numero_rccm" class="form-control"
                 value="{{ old('numero_rccm', $societe->numero_rccm) }}"
                 placeholder="MA-BAM-2010-B-XXXXX">
        </div>
        <div>
          <label class="form-label">N° IFU <span style="font-size:.7rem;color:var(--text2);">(Identifiant Fiscal Unique)</span></label>
          <input type="text" name="numero_ifu" class="form-control"
                 value="{{ old('numero_ifu', $societe->numero_ifu) }}"
                 placeholder="0000000000000">
        </div>
      </div>

      {{-- Aperçu infos juridiques --}}
      @if($societe->numero_rccm || $societe->numero_ifu)
      <div style="background:var(--dark3);border-radius:8px;padding:14px;border:1px solid var(--border);">
        <div style="font-size:.72rem;color:var(--text2);margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em;">Aperçu document officiel</div>
        <div style="font-size:.85rem;color:var(--text);line-height:1.8;">
          <strong>{{ $societe->raison_sociale ?? 'DIARRA TRANSPORT' }}</strong><br>
          @if($societe->numero_rccm) RCCM : {{ $societe->numero_rccm }}<br>@endif
          @if($societe->numero_ifu)  IFU  : {{ $societe->numero_ifu }}<br>@endif
          {{ $societe->adresse ?? '' }}
        </div>
      </div>
      @endif

    </div>
  </div>
</div>

{{-- ══ ONGLET 4 — APPLICATION ═══════════════════════════ --}}
<div class="param-section" id="tab-appli">
  <div class="param-card">
    <div class="param-card-title">🔧 Paramètres Application</div>
    <div style="display:grid;gap:16px;">

      <div class="info-grid">
        <div>
          <label class="form-label">Devise</label>
          <select name="devise" class="form-select">
            @foreach(['FCFA','XOF','EUR','USD'] as $d)
              <option value="{{ $d }}" {{ ($societe->devise ?? 'FCFA') === $d ? 'selected' : '' }}>{{ $d }}</option>
            @endforeach
          </select>
          <div style="font-size:.72rem;color:var(--text2);margin-top:4px;">Utilisée dans tous les montants affichés.</div>
        </div>
        <div>
          <label class="form-label">Fuseau Horaire</label>
          <select name="fuseau_horaire" class="form-select">
            @foreach(['Africa/Bamako','Africa/Abidjan','Africa/Dakar','Africa/Lagos','Europe/Paris'] as $tz)
              <option value="{{ $tz }}" {{ ($societe->fuseau_horaire ?? 'Africa/Bamako') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
            @endforeach
          </select>
          <div style="font-size:.72rem;color:var(--text2);margin-top:4px;">Affecte les heures dans les reçus et logs.</div>
        </div>
      </div>

      {{-- Infos système lecture seule --}}
      <div class="param-card-title" style="margin-top:8px;">ℹ️ Informations Système</div>
      <div class="info-grid">
        <div class="info-item">
          <div class="lbl">Version Laravel</div>
          <div class="val mono">{{ app()->version() }}</div>
        </div>
        <div class="info-item">
          <div class="lbl">Version PHP</div>
          <div class="val mono">{{ PHP_VERSION }}</div>
        </div>
        <div class="info-item">
          <div class="lbl">Environnement</div>
          <div class="val mono">{{ app()->environment() }}</div>
        </div>
        <div class="info-item">
          <div class="lbl">Base de données</div>
          <div class="val mono">{{ config('database.default') }}</div>
        </div>
        <div class="info-item">
          <div class="lbl">URL Application</div>
          <div class="val" style="font-size:.78rem;">{{ config('app.url') }}</div>
        </div>
        <div class="info-item">
          <div class="lbl">Locale</div>
          <div class="val mono">{{ config('app.locale') }}</div>
        </div>
      </div>

    </div>
  </div>
</div>

</form>{{-- fin formulaire --}}

{{-- ══ ONGLET 5 — STATISTIQUES (lecture seule, hors form) ══ --}}
<div class="param-section" id="tab-stats">
  @php
    $nbUsers   = \App\Models\User::count();
    $nbBagages = \App\Models\Bagage::count();
    $nbClients = \App\Models\Client::count();
    $nbVoyages = \App\Models\Voyage::count();
    $totalCA   = \App\Models\Bagage::sum('montant');
    $today     = \App\Models\Bagage::whereDate('date_enregistrement', today())->count();
  @endphp

  <div class="mini-stats">
    <div class="mini-stat">
      <div class="ms-val">{{ $nbUsers }}</div>
      <div class="ms-lbl">Utilisateurs actifs</div>
    </div>
    <div class="mini-stat">
      <div class="ms-val">{{ $nbBagages }}</div>
      <div class="ms-lbl">Bagages total</div>
    </div>
    <div class="mini-stat">
      <div class="ms-val">{{ $nbClients }}</div>
      <div class="ms-lbl">Clients enregistrés</div>
    </div>
    <div class="mini-stat">
      <div class="ms-val">{{ $nbVoyages }}</div>
      <div class="ms-lbl">Voyages créés</div>
    </div>
    <div class="mini-stat">
      <div class="ms-val" style="font-size:1rem;">{{ number_format($totalCA, 0, ',', ' ') }}</div>
      <div class="ms-lbl">FCFA chiffre d'affaires total</div>
    </div>
    <div class="mini-stat">
      <div class="ms-val">{{ $today }}</div>
      <div class="ms-lbl">Bagages enregistrés aujourd'hui</div>
    </div>
  </div>

  <div class="param-card">
    <div class="param-card-title">👥 Utilisateurs par Rôle</div>
    <div style="display:grid;gap:10px;">
      @foreach(\App\Models\Role::withCount('users')->get() as $r)
      <div style="display:flex;justify-content:space-between;align-items:center;
                  background:var(--dark3);border-radius:8px;padding:10px 14px;">
        <span style="color:var(--text);font-weight:500;">{{ $r->nom }}</span>
        <span style="background:rgba(201,168,76,.15);color:var(--gold);
                     border-radius:12px;padding:2px 12px;font-size:.78rem;font-weight:600;">
          {{ $r->users_count }} utilisateur(s)
        </span>
      </div>
      @endforeach
    </div>
  </div>

  <div class="param-card">
    <div class="param-card-title">📅 Activité Récente</div>
    <table class="table-dt">
      <thead>
        <tr><th>Date</th><th>Action</th><th>Utilisateur</th><th>Détail</th></tr>
      </thead>
      <tbody>
        @foreach(\App\Models\ActivityLog::with('user')->latest()->take(10)->get() as $log)
        <tr>
          <td style="font-size:.75rem;white-space:nowrap;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
          <td><span class="badge-dt badge-{{ $log->badge_color }}">{{ $log->action }}</span></td>
          <td style="color:var(--text);">{{ $log->user?->nom_complet ?? 'Système' }}</td>
          <td style="font-size:.78rem;">{{ Str::limit($log->detail, 60) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Bouton Save (affiché sauf onglet stats) ──────────────── --}}
<div id="btnSaveWrap" style="margin-top:4px;">
  <button type="button" id="btnSaveParams" class="btn-gold"
          onclick="document.getElementById('mainParamForm').submit()"
          style="padding:11px 28px;">
    💾 Enregistrer les Paramètres
  </button>
  @if(session('success'))
    <span style="margin-left:14px;color:#4ade80;font-size:.85rem;">✅ {{ session('success') }}</span>
  @endif
</div>

@endsection

@section('scripts')
<script>
/* ══ LOGO UPLOAD ════════════════════════════════════════════ */
const CSRF      = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const UPLOAD_URL  = '{{ route("admin.societe.logo") }}';
const DELETE_URL  = '{{ route("admin.societe.logo.delete") }}';

let selectedFile = null;

// Sélection via input file
function handleLogoChange(input) {
  if (input.files && input.files[0]) {
    selectedFile = input.files[0];
    previewFile(selectedFile);
  }
}

// Glisser-déposer
function handleLogoDrop(e) {
  e.preventDefault();
  document.getElementById('dropZone').style.borderColor = 'var(--border)';
  const file = e.dataTransfer.files[0];
  if (file && file.type.startsWith('image/')) {
    selectedFile = file;
    previewFile(file);
  } else {
    showToast('error', 'Fichier non supporté. Utilisez PNG, JPG, SVG ou WEBP.');
  }
}

// Aperçu immédiat dans le cercle
function previewFile(file) {
  const reader = new FileReader();
  reader.onload = function(e) {
    const wrap = document.getElementById('logoPreview');
    // Remplacer le contenu par une image
    const img = document.createElement('img');
    img.src = e.target.result;
    img.style.cssText = 'width:100%;height:100%;object-fit:contain;border-radius:50%;';
    img.alt = 'Aperçu logo';
    img.id  = 'logoPreview';
    wrap.replaceWith(img);
  };
  reader.readAsDataURL(file);

  // Afficher bouton upload
  document.getElementById('btnUploadLogo').style.display = 'inline-flex';
  document.getElementById('dropZone').style.borderColor = 'var(--gold)';
}

// Envoi du logo au serveur
async function uploadLogo() {
  if (!selectedFile) { showToast('error', 'Aucun fichier sélectionné.'); return; }

  // Vérification taille
  if (selectedFile.size > 2 * 1024 * 1024) {
    showToast('error', 'Fichier trop lourd (max 2 Mo).');
    return;
  }

  const btn      = document.getElementById('btnUploadLogo');
  const progress = document.getElementById('uploadProgress');
  const bar      = document.getElementById('progressBar');
  const msg      = document.getElementById('uploadMsg');

  btn.disabled    = true;
  btn.textContent = '⏳ Envoi...';
  progress.style.display = 'block';
  bar.style.width = '20%';
  msg.textContent = 'Envoi en cours...';

  const formData = new FormData();
  formData.append('logo', selectedFile);

  try {
    // Simulation progression
    let pct = 20;
    const timer = setInterval(() => {
      pct = Math.min(pct + 15, 85);
      bar.style.width = pct + '%';
    }, 200);

    const resp = await fetch(UPLOAD_URL, {
      method:  'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body:    formData,
    });

    clearInterval(timer);
    const data = await resp.json();

    if (resp.ok && data.success) {
      bar.style.width   = '100%';
      bar.style.background = '#4ade80';
      msg.textContent   = '✅ ' + data.message;
      msg.style.color   = '#4ade80';

      // Mettre à jour TOUTES les instances du logo dans la page
      document.querySelectorAll('img[alt="Logo actuel"], img[alt="Aperçu logo"], img[alt="Logo Diarra Transport"]')
        .forEach(img => {
          img.src = data.url + '?t=' + Date.now();
        });

      selectedFile = null;
      btn.style.display  = 'none';
      document.getElementById('dropZone').style.borderColor = 'var(--border)';

      // Afficher bouton supprimer si pas encore affiché
      if (!document.getElementById('btnDeleteLogo')) {
        const delBtn = document.createElement('button');
        delBtn.type  = 'button';
        delBtn.id    = 'btnDeleteLogo';
        delBtn.textContent = '🗑️ Supprimer le logo';
        delBtn.style.cssText = 'background:rgba(192,57,43,.15);border:1px solid rgba(192,57,43,.3);color:#f87171;border-radius:8px;padding:8px 14px;font-size:.82rem;cursor:pointer;font-family:inherit;';
        delBtn.onclick = deleteLogo;
        btn.parentNode.appendChild(delBtn);
      }

      showToast('success', data.message);

      // Recharger la topbar logo (soft reload)
      setTimeout(() => window.location.reload(), 1500);

    } else {
      bar.style.background = '#f87171';
      msg.textContent = '❌ ' + (data.message || 'Erreur lors de l\'envoi.');
      msg.style.color = '#f87171';
      showToast('error', data.message || 'Erreur.');
    }
  } catch (err) {
    bar.style.background = '#f87171';
    msg.textContent = '❌ Erreur réseau : ' + err.message;
    showToast('error', 'Erreur réseau : ' + err.message);
  } finally {
    btn.disabled    = false;
    btn.textContent = '⬆️ Envoyer ce logo';
  }
}

// Suppression du logo
async function deleteLogo() {
  if (!confirm('Supprimer le logo ? Le logo SVG par défaut sera restauré.')) return;

  const btn = document.getElementById('btnDeleteLogo');
  if (btn) { btn.disabled = true; btn.textContent = '⏳...'; }

  try {
    const resp = await fetch(DELETE_URL, {
      method:  'DELETE',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await resp.json();

    if (resp.ok && data.success) {
      showToast('success', data.message);
      setTimeout(() => window.location.reload(), 800);
    } else {
      showToast('error', data.message || 'Erreur.');
      if (btn) { btn.disabled = false; btn.textContent = '🗑️ Supprimer le logo'; }
    }
  } catch (err) {
    showToast('error', 'Erreur réseau : ' + err.message);
    if (btn) { btn.disabled = false; btn.textContent = '🗑️ Supprimer le logo'; }
  }
}

/* ══ ONGLETS ══════════════════════════════════════════════ */
function showTab(name, btn) {
  document.querySelectorAll('.param-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.param-tab').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  btn.classList.add('active');
  document.getElementById('btnSaveWrap').style.display =
    name === 'stats' ? 'none' : 'block';
}

document.addEventListener('DOMContentLoaded', function() {
  const last = localStorage.getItem('param-tab');
  if (last) {
    const btn = document.querySelector(`.param-tab[onclick*="${last}"]`);
    if (btn) showTab(last, btn);
  }
  document.querySelectorAll('.param-tab').forEach(btn => {
    btn.addEventListener('click', function() {
      const match = this.getAttribute('onclick').match(/showTab\('(\w+)'/);
      if (match) localStorage.setItem('param-tab', match[1]);
    });
  });
});
</script>
@endsection
