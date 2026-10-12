@extends('layouts.app')
@section('title','Enregistrement Bagage')

@section('styles')
<style>
.fc{background:var(--dark2);border:1px solid var(--border);border-radius:12px;padding:22px 24px;margin-bottom:20px;}
.fc .fc-title{font-size:.9rem;font-weight:600;color:var(--gold);margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.fc-grid{display:grid;gap:14px;}
.fc-cols-2{grid-template-columns:1fr 1fr;}
.fc-cols-3{grid-template-columns:1fr 1fr 1fr;}
@media(max-width:768px){
  .fc{padding:16px 14px;margin-bottom:14px;}
  .fc-cols-2,.fc-cols-3{grid-template-columns:1fr;}
  .form-actions-bar{flex-direction:column;width:100%;}
  .form-actions-bar button{width:100% !important;justify-content:center;}
  .tarif-box{padding:14px;}
  .tarif-box .tarif-value{font-size:1.6rem;}
}
.client-search-wrap{display:none;}
.client-search-wrap.show{display:block;}
.client-suggestions{
  position:absolute;top:100%;left:0;right:0;z-index:300;
  background:var(--dark3);border:1px solid var(--border);
  border-radius:0 0 8px 8px;max-height:200px;overflow-y:auto;
}
.client-suggestions .sug-item{
  padding:9px 14px;cursor:pointer;font-size:.82rem;
  border-bottom:1px solid rgba(255,255,255,.04);
  display:flex;justify-content:space-between;align-items:center;
}
.client-suggestions .sug-item:hover{background:var(--dark2);}
.btn-reset{background:var(--dark3);border:1px solid var(--border);color:var(--text2);border-radius:8px;padding:10px 18px;font-size:.85rem;cursor:pointer;transition:.15s;display:inline-flex;align-items:center;justify-content:center;}
.btn-reset:hover{border-color:var(--gold);color:var(--gold);}
/* Spinner dans le bouton submit */
.btn-gold .spinner{display:none;width:14px;height:14px;border:2px solid rgba(0,0,0,.3);border-top-color:#0e1621;border-radius:50%;animation:spin .6s linear infinite;margin-left:8px;vertical-align:middle;}
.btn-gold.loading .spinner{display:inline-block;}
.btn-gold.loading{opacity:.8;cursor:not-allowed;}
@keyframes spin{to{transform:rotate(360deg);}}
/* ── Blocs colis dynamiques ── */
.colis-block{
  border:1px solid var(--border);border-radius:10px;
  padding:14px 16px;margin-bottom:12px;background:var(--dark3);
}
.colis-block-header{
  font-size:.8rem;font-weight:600;color:var(--gold);
  margin-bottom:10px;display:flex;align-items:center;gap:8px;
}
</style>
@endsection

@section('content')
<div class="page-header">
  <h1><i class="bi bi-box-seam"></i> Enregistrement Colis</h1>
  <p>Créer un nouveau colis accompagné et générer le numéro d'identification</p>
</div>

<form id="bagageForm" autocomplete="off">
@csrf

{{-- ── SECTION VOYAGE ─────────────────────────── --}}
<div class="fc">
  <div class="fc-title"><i class="bi bi-geo-alt"></i> Le Voyage</div>
  <div class="fc-grid fc-cols-3">
    <div>
      <label class="form-label">Date de Départ *</label>
      <input type="date" id="dateDepart" name="date_depart" class="form-control"
             value="{{ date('Y-m-d') }}" required onchange="updateProgrammes()">
    </div>
    <div>
      <label class="form-label">Départ *</label>
      {{-- Champ départ : rempli automatiquement selon la destination --}}
      <input type="text" id="villeDepart" name="ville_depart" class="form-control"
             placeholder="Sélectionnez une destination →" readonly required
             style="cursor:default;background:var(--dark3);">
    </div>
    <div>
      <label class="form-label">Destination *</label>
      <select name="ville_arrivee" id="villeArrivee" class="form-select" required
              onchange="onDestinationChange(this.value)">
        <option value="">-- Choisir la destination --</option>
        @foreach(array_keys($programmesByDest) as $dest)
          <option value="{{ $dest }}">{{ $dest }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="fc-grid fc-cols-3" style="margin-top:14px">
    <div>
      <label class="form-label">Programme / Heure de Départ</label>
      <select name="programme" id="selectProgramme" class="form-select" required>
        <option value="">-- Choisir la destination d'abord --</option>
      </select>
    </div>
    <div>
      <label class="form-label">N° Billet Voyageur</label>
      <input type="text" name="numero_billet" class="form-control" placeholder="Ex: 29544739 / 44">
    </div>
    <div>
      <label class="form-label">N° Bus</label>
      <input type="text" name="numero_bus" class="form-control" placeholder="ex: BM-001">
    </div>
  </div>
</div>

{{-- ── SECTION VOYAGEUR ───────────────────────── --}}
<div class="fc">
  <div class="fc-title"><i class="bi bi-person"></i> Le Voyageur</div>

  <div class="checkbox-known" style="margin-bottom:14px;">
    <label>
      <input type="checkbox" id="clientConnu" onchange="toggleClientConnu(this)">
      &nbsp;Client Connu ?
    </label>
  </div>

  <div class="client-search-wrap" id="clientSearchWrap" style="position:relative;margin-bottom:14px;">
    <label class="form-label">Rechercher un client</label>
    <input type="text" id="clientSearch" class="form-control" placeholder="Nom, prénom ou téléphone…" autocomplete="off">
    <input type="hidden" name="client_id" id="clientIdInput">
    <div class="client-suggestions" id="clientSuggestions" style="display:none;"></div>
  </div>

  <div class="fc-grid fc-cols-3">
    <div>
      <label class="form-label">Prénom *</label>
      <input type="text" name="prenom" id="fPrenom" class="form-control" placeholder="Prénom" required>
    </div>
    <div>
      <label class="form-label">Nom</label>
      <input type="text" name="nom" id="fNom" class="form-control" placeholder="Nom (optionnel)">
    </div>
    <div>
      <label class="form-label">Contact (Tél)</label>
      <input type="text" name="telephone" id="fTel" class="form-control" placeholder="+223 XX XX XX XX">
    </div>
  </div>
</div>

{{-- ── SECTION COLIS ──────────────────────────── --}}
<div class="fc">
  <div class="fc-title"><i class="bi bi-box-seam"></i> Les Colis</div>
  <div class="fc-grid" style="grid-template-columns:1fr;max-width:280px;">
    <div>
      <label class="form-label">Nombre de colis *</label>
      <input type="number" name="nombre_colis" id="nbCourriers" class="form-control" min="1" max="50" value="1" required
             oninput="genererColis()">
    </div>
  </div>
  <div id="colisContainer" style="margin-top:14px;">
    {{-- Les blocs colis sont générés dynamiquement par JS --}}
  </div>
</div>

{{-- ── SECTION PAIEMENT ───────────────────────── --}}
<div class="fc">
  <div class="fc-title"><i class="bi bi-cash-coin"></i> Le Paiement</div>
  <div class="fc-grid fc-cols-3">
    <div>
      <label class="form-label">Montant Total (FCFA) *</label>
      <input type="number" name="montant" id="fMontant" class="form-control" min="0" placeholder="0"
             readonly required style="cursor:default;opacity:.85;">
    </div>
    <div>
      <label class="form-label">Remise (FCFA)</label>
      <input type="number" name="remise" id="fRemise" class="form-control" min="0" placeholder="0" value="0"
             oninput="calcRendu()">
    </div>
    <div>
      <label class="form-label">Net à Payer (FCFA)</label>
      <div class="tarif-box">
        <div class="tarif-label">NET À PAYER</div>
        <div class="tarif-value" id="renduDisplay">0 FCFA</div>
      </div>
    </div>
  </div>

  {{-- Erreurs de validation --}}
  <div id="formErrors" style="display:none;margin-top:14px;background:rgba(192,57,43,.15);border:1px solid rgba(192,57,43,.3);border-radius:8px;padding:12px 16px;">
    <div style="color:#f87171;font-size:.82rem;font-weight:600;margin-bottom:6px;">Erreurs de saisie :</div>
    <ul id="formErrorsList" style="color:#f87171;font-size:.8rem;padding-left:16px;margin:0;"></ul>
  </div>

  <div class="form-actions-bar" style="display:flex;gap:12px;margin-top:20px;flex-wrap:wrap;align-items:center;">
    <button type="button" id="btnConfirmer" class="btn-gold" onclick="soumettreBagage()">
      <i class="bi bi-box-seam"></i> CONFIRMER COLIS
      <span class="spinner"></span>
    </button>
    <button type="button" class="btn-reset" onclick="resetForm()"><i class="bi bi-arrow-counterclockwise"></i> Réinitialiser</button>
  </div>
</div>

</form>

{{-- ══ MODAL REÇU ══════════════════════════════ --}}
@include('bagages.partials.modal-recu')

@endsection

@section('scripts')

{{-- ── Logique formulaire ───────────────────── --}}
<script>
const STORE_URL   = '{{ route("bagages.store") }}';
const CSRF_TOKEN  = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── Client connu toggle ──────────────
function toggleClientConnu(cb) {
  document.getElementById('clientSearchWrap').classList.toggle('show', cb.checked);
  if (!cb.checked) {
    document.getElementById('clientIdInput').value = '';
    document.getElementById('fPrenom').value = '';
    document.getElementById('fNom').value    = '';
    document.getElementById('fTel').value    = '';
  }
}

// ── Recherche client AJAX ─────────────
const searchInput = document.getElementById('clientSearch');
const suggestions = document.getElementById('clientSuggestions');
let searchTimer;

if (searchInput) {
  searchInput.addEventListener('input', function () {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 2) { suggestions.style.display = 'none'; return; }
    searchTimer = setTimeout(() => {
      fetch('{{ route("clients.search") }}?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
          if (!data.length) { suggestions.style.display = 'none'; return; }
          suggestions.innerHTML = data.map(c =>
            `<div class="sug-item" onclick="selectClient(${c.id},'${escapeHtml(c.prenom)}','${escapeHtml(c.nom)}','${escapeHtml(c.telephone||'')}')">
              <span>${escapeHtml(c.prenom)} ${escapeHtml(c.nom)}</span>
              <span style="color:var(--text2);font-size:.75rem">${escapeHtml(c.telephone||'')}</span>
             </div>`
          ).join('');
          suggestions.style.display = 'block';
        })
        .catch(() => { suggestions.style.display = 'none'; });
    }, 300);
  });
  document.addEventListener('click', e => {
    if (!searchInput.contains(e.target) && !suggestions.contains(e.target))
      suggestions.style.display = 'none';
  });
}

function selectClient(id, prenom, nom, tel) {
  document.getElementById('clientIdInput').value = id;
  document.getElementById('fPrenom').value        = prenom;
  document.getElementById('fNom').value           = nom;
  document.getElementById('fTel').value           = tel;
  document.getElementById('clientSearch').value   = prenom + ' ' + nom;
  suggestions.style.display = 'none';
}

function escapeHtml(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Calcul net ─────────────────────
function calcRendu() {
  const m   = parseFloat(document.getElementById('fMontant').value) || 0;
  const r   = parseFloat(document.getElementById('fRemise').value)  || 0;
  const net = m - r;
  const el  = document.getElementById('renduDisplay');
  el.textContent = net.toLocaleString('fr-FR') + ' FCFA';
  el.style.color = net >= 0 ? '#4ade80' : '#f87171';
}
calcRendu();

// ── Natures disponibles (injectées depuis PHP) ──
const NATURES_COLIS = @json($natures);

// ── Génération dynamique des blocs colis ──
function genererColis() {
  const nb = parseInt(document.getElementById('nbCourriers').value) || 1;
  const container = document.getElementById('colisContainer');
  container.innerHTML = '';
  for (let i = 0; i < nb; i++) {
    container.insertAdjacentHTML('beforeend', buildColisBlock(i));
  }
  calcMontantTotal();
}

function buildColisBlock(index) {
  const n = index + 1;
  const opts = NATURES_COLIS.map(nat =>
    `<option value="${escapeHtml(nat)}">${escapeHtml(nat)}</option>`
  ).join('');

  return `
  <div class="colis-block">
    <div class="colis-block-header"><i class="bi bi-box"></i> Colis ${n}</div>
    <div class="fc-grid fc-cols-3">
      <div>
        <label class="form-label">Nature du colis *</label>
        <select name="colis[${index}][nature]" class="form-select" required>
          <option value="">-- Type --</option>
          ${opts}
        </select>
      </div>
      <div>
        <label class="form-label">Valeur (optionnelle)</label>
        <input type="text" name="colis[${index}][valeur]" class="form-control" placeholder="Valeur déclarée...">
      </div>
      <div>
        <label class="form-label">Prix (FCFA) *</label>
        <input type="number" name="colis[${index}][prix]" class="form-control colis-prix" min="0" placeholder="0" required oninput="calcMontantTotal()">
      </div>
    </div>
  </div>`;
}

// ── Calcul automatique du montant total ──
function calcMontantTotal() {
  const prixInputs = document.querySelectorAll('.colis-prix');
  let total = 0;
  prixInputs.forEach(input => { total += parseFloat(input.value) || 0; });
  document.getElementById('fMontant').value = total;
  calcRendu();
}

// ── Initialisation : générer le premier bloc colis au chargement ──
genererColis();

// ── Données programmes par destination (injectées depuis PHP) ─
const PROGRAMMES_PAR_DEST = @json($programmesByDest);

/**
 * Quand l'utilisateur choisit une destination :
 * 1. Rempli automatiquement le champ DÉPART = "Bamako" (ville de départ des lignes)
 * 2. Met à jour le dropdown PROGRAMME avec les horaires de cette destination
 * 3. Formate chaque option : "BAMAKO-KAYES 06H00 du 14/06/2026"
 */
function onDestinationChange(dest) {
  const depInput   = document.getElementById('villeDepart');
  const progSelect = document.getElementById('selectProgramme');
  const dateVal    = document.getElementById('dateDepart').value;

  // Réinitialiser
  depInput.value = '';
  progSelect.innerHTML = '';

  if (!dest) {
    progSelect.innerHTML = '<option value="">-- Choisir la destination d\'abord --</option>';
    return;
  }

  // Toutes les lignes partent de Bamako (ou ville de départ de la ligne)
  // On déduit la ville de départ depuis le programme
  const progs = PROGRAMMES_PAR_DEST[dest] || [];

  if (!progs.length) {
    depInput.value = 'Bamako';
    progSelect.innerHTML = '<option value="">-- Aucun départ disponible --</option>';
    return;
  }

  // Extraire la ville de départ depuis le premier programme (ex: "BAMAKO-KAYES 06H" → "Bamako")
  const firstProg = progs[0].programme;
  const parts = firstProg.split('-');
  // Capitaliser la ville de départ
  const villeDep = parts[0].charAt(0).toUpperCase() + parts[0].slice(1).toLowerCase();
  depInput.value = villeDep;

  // Formater la date pour l'affichage
  const dateFmt = dateVal ? formatDateFR(dateVal) : '';

  // Construire les options du programme
  const firstOpt = document.createElement('option');
  firstOpt.value = '';
  firstOpt.textContent = '-- Choisir l\'heure de départ --';
  progSelect.appendChild(firstOpt);

  progs.forEach(p => {
    const opt = document.createElement('option');
    opt.value = p.programme;
    opt.textContent = p.programme + (dateFmt ? ' du ' + dateFmt : '');
    progSelect.appendChild(opt);
  });

  // Auto-sélectionner le premier si un seul horaire
  if (progs.length === 1) {
    progSelect.selectedIndex = 1;
  }
}

/** Met à jour les labels de date dans le dropdown programme sans changer la sélection */
function updateProgrammes() {
  const dest    = document.getElementById('villeArrivee').value;
  if (!dest) return;
  const current = document.getElementById('selectProgramme').value;
  onDestinationChange(dest);
  // Restaurer la sélection si possible
  if (current) {
    const sel = document.getElementById('selectProgramme');
    for (let i = 0; i < sel.options.length; i++) {
      if (sel.options[i].value === current) {
        sel.selectedIndex = i;
        break;
      }
    }
  }
}

/** Formate une date ISO (YYYY-MM-DD) en JJ/MM/AAAA */
function formatDateFR(iso) {
  if (!iso) return '';
  const [y, m, d] = iso.split('-');
  return d + '/' + m + '/' + y;
}

// ── Reset ─────────────────────────
function resetForm() {
  document.getElementById('bagageForm').reset();
  document.getElementById('renduDisplay').textContent = '0 FCFA';
  document.getElementById('renduDisplay').style.color = '#4ade80';
  document.getElementById('clientSearchWrap').classList.remove('show');
  document.getElementById('clientConnu').checked = false;
  // Réinitialiser les champs voyage dynamiques
  document.getElementById('villeDepart').value = '';
  document.getElementById('selectProgramme').innerHTML =
    '<option value="">-- Choisir la destination d\'abord --</option>';
  // Régénérer les blocs colis (1 bloc par défaut)
  document.getElementById('nbCourriers').value = 1;
  genererColis();
  hideErrors();
}

// ── Erreurs form ─────────────────
function showErrors(msgs) {
  const box  = document.getElementById('formErrors');
  const list = document.getElementById('formErrorsList');
  list.innerHTML = msgs.map(m => `<li>${m}</li>`).join('');
  box.style.display = 'block';
  box.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
function hideErrors() {
  document.getElementById('formErrors').style.display = 'none';
  document.getElementById('formErrorsList').innerHTML = '';
}

// ── Soumission AJAX principale ────
async function soumettreBagage() {
  const btn  = document.getElementById('btnConfirmer');
  const form = document.getElementById('bagageForm');

  // Validation HTML5 native
  if (!form.reportValidity()) return;

  hideErrors();
  btn.classList.add('loading');
  btn.disabled = true;

  const formData = new FormData(form);

  try {
    const resp = await fetch(STORE_URL, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept':       'application/json',
      },
      body: formData,
    });

    const data = await resp.json();

    if (resp.status === 422) {
      // Erreurs de validation Laravel
      const msgs = Object.values(data.errors || {}).flat();
      showErrors(msgs.length ? msgs : ['Données invalides, veuillez vérifier le formulaire.']);
      return;
    }

    if (!resp.ok || !data.success) {
      showErrors([data.message || 'Une erreur est survenue.']);
      return;
    }

    // ✅ Succès → ouvrir la modal reçu
    showToast('success', data.message);
    openModalRecu(data.bagage, data.qr_code || '');
    resetForm();

  } catch (err) {
    showErrors(['Erreur réseau : ' + err.message]);
  } finally {
    btn.classList.remove('loading');
    btn.disabled = false;
  }
}
</script>

{{-- ── JS Modal Reçu & Impression ─────────────── --}}
@include('bagages.partials.modal-recu-js')

@endsection
