<script>
/* ══════════════════════════════════════════════════════════════
   MODAL REÇU – Diarra Transport
   Fonctions : openModalRecu · closeModalRecu
               buildRecu · buildEtiquettes
               printRecu · printEtiq · printTout
   ══════════════════════════════════════════════════════════════ */

// ── Ouvre la modal ────────────────────────────────────────────
function openModalRecu(bagage, qrSvg) {
  if (!bagage || !bagage.code_bagage) {
    console.error('openModalRecu: données bagage manquantes', bagage);
    return;
  }
  // Stocker globalement pour les fonctions d'impression
  window._lastBagage = bagage;
  window._lastQrSvg  = qrSvg || '';

  buildRecu(bagage, qrSvg || '');
  buildEtiquettes(bagage);
  document.getElementById('modalPrint').classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeModalRecu() {
  document.getElementById('modalPrint').classList.remove('show');
  document.body.style.overflow = '';
}

// Fermer sur clic fond
document.getElementById('modalPrint').addEventListener('click', function(e) {
  if (e.target === this) closeModalRecu();
});

// ── SVG Logo Diarra Transport (vrai logo – tricolore Mali + cheval blanc) ──
function logoSvg(size) {
  size = size || 40;
  const id = 'lc' + size + Math.random().toString(36).slice(2,6);
  return `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 100 100">
    <defs><clipPath id="${id}"><circle cx="50" cy="50" r="46"/></clipPath></defs>
    <!-- Anneau vert foncé -->
    <circle cx="50" cy="50" r="49" fill="#0d5c2e"/>
    <circle cx="50" cy="50" r="46" fill="#fff"/>
    <!-- Bandes tricolores Mali -->
    <rect x="4"  y="4" width="30" height="92" clip-path="url(#${id})" fill="#169B62"/>
    <rect x="34" y="4" width="32" height="92" clip-path="url(#${id})" fill="#FCD116"/>
    <rect x="66" y="4" width="30" height="92" clip-path="url(#${id})" fill="#CE1126"/>
    <!-- Cheval blanc bondissant -->
    <g clip-path="url(#${id})">
      <ellipse cx="50" cy="56" rx="22" ry="14" fill="white" transform="rotate(-10,50,56)"/>
      <ellipse cx="72" cy="40" rx="9"  ry="7"  fill="white" transform="rotate(-20,72,40)"/>
      <polygon points="64,44 72,33 78,46 68,50" fill="white"/>
      <ellipse cx="68" cy="34" rx="5"  ry="8"  fill="white" transform="rotate(-30,68,34)"/>
      <polygon points="70,30 73,24 76,30"       fill="white"/>
      <rect x="62" y="62" width="5" height="16" rx="2" fill="white" transform="rotate(20,64,62)"/>
      <rect x="70" y="60" width="5" height="16" rx="2" fill="white" transform="rotate(35,72,60)"/>
      <rect x="30" y="62" width="5" height="14" rx="2" fill="white" transform="rotate(-20,32,62)"/>
      <rect x="38" y="64" width="5" height="14" rx="2" fill="white" transform="rotate(-10,40,64)"/>
      <path d="M29,54 Q18,48 16,56 Q18,65 26,62" fill="white"/>
      <circle cx="75" cy="38" r="1.5" fill="#169B62"/>
    </g>
    <!-- Texte arqué haut : DIARRA -->
    <path id="at${id}" d="M 15,50 A 35,35 0 0,1 85,50" fill="none"/>
    <text font-family="Arial Black,Arial,sans-serif" font-size="11" font-weight="900"
          fill="white" stroke="#0d5c2e" stroke-width=".5" letter-spacing="2">
      <textPath href="#at${id}" startOffset="12%">DIARRA</textPath>
    </text>
    <!-- Texte arqué bas : TRANSPORT -->
    <path id="ab${id}" d="M 12,54 A 38,38 0 0,0 88,54" fill="none"/>
    <text font-family="Arial Black,Arial,sans-serif" font-size="9" font-weight="900"
          fill="white" stroke="#0d5c2e" stroke-width=".4" letter-spacing="1">
      <textPath href="#ab${id}" startOffset="8%">TRANSPORT</textPath>
    </text>
  </svg>`;
}

// ── Helpers format de date fiables (évite les bugs 'Invalid Date') ──
function formatFrenchDate(dateVal) {
  if (!dateVal) return '—';
  try {
    const raw = String(dateVal).split('T')[0].split(' ')[0];
    const p = raw.split('-');
    if (p.length === 3) {
      return p[2] + '/' + p[1] + '/' + p[0];
    }
    return raw;
  } catch(e) {
    return String(dateVal);
  }
}

function formatFrenchDateLong(dateVal) {
  if (!dateVal) return '—';
  try {
    const raw = String(dateVal).split('T')[0].split(' ')[0];
    const p = raw.split('-');
    if (p.length === 3) {
      const ms = ['janvier','février','mars','avril','mai','juin','juillet',
                  'août','septembre','octobre','novembre','décembre'];
      const mIdx = parseInt(p[1], 10) - 1;
      return parseInt(p[2], 10) + ' ' + (ms[mIdx] || p[1]) + ' ' + p[0];
    }
    return raw;
  } catch(e) {
    return String(dateVal);
  }
}

// ── Construit le reçu client ─────────────────────────────────
function buildRecu(b, qrSvg) {
  const cl   = b.client  || {};
  const voy  = b.voyage  || {};
  const usr  = b.user    || {};
  const net  = (parseFloat(b.montant) || 0) - (parseFloat(b.remise) || 0);
  const nom  = ((cl.prenom || '') + ' ' + (cl.nom || '')).trim() || '—';
  const dep  = (voy.ville_depart  || '—').toUpperCase();
  const arr  = (voy.ville_arrivee || '—').toUpperCase();
  const trajet = dep + ' → ' + arr;
  const dateStr = formatFrenchDate(voy.date_depart || b.date_depart);

  const agentNom = ((usr.prenom||'') + ' ' + (usr.nom||'')).trim() || '—';
  const montantF = parseInt(b.montant || 0).toLocaleString('fr-FR');
  const remiseF  = parseInt(b.remise  || 0).toLocaleString('fr-FR');
  const netF     = parseInt(net).toLocaleString('fr-FR');

  const colisArr = b.colis || [];
  let colisRows = '';
  if (colisArr.length > 0) {
    colisArr.forEach((c, i) => {
      const val = c.valeur ? ` (Val: ${c.valeur})` : '';
      const px  = parseInt(c.prix || 0).toLocaleString('fr-FR') + ' F';
      colisRows += `<tr><td>Colis ${i+1}</td><td><strong>${c.nature || '—'}</strong>${val} &mdash; <span style="color:#169B62;font-weight:600;">${px}</span></td></tr>`;
    });
  } else {
    colisRows = `<tr><td>Nature</td><td><strong>${b.nature || '—'}</strong></td></tr>`;
  }

  document.getElementById('recuHtml').innerHTML = `
    <div class="recu-header">
      <div style="flex-shrink:0;">${logoSvg(42)}</div>
      <div class="r-brand">
        <h3>DIARRA TRANSPORT</h3>
        <p>Près de la maternité Sogoniko – Bamako, Mali</p>
        <p>Tél : (223) 20802142</p>
      </div>
    </div>

    <div class="recu-code">N° ${b.code_bagage}</div>
    <div class="recu-trajet">${trajet} &nbsp;|&nbsp; ${dateStr}</div>

    <table class="recu-table">
      <tr><td>Voyageur</td>     <td><strong style="font-size:14px;">${nom}</strong></td></tr>
      <tr><td>Téléphone</td>    <td><strong style="font-size:13px;">${cl.telephone || '—'}</strong></td></tr>
      <tr><td>Nb Colis</td>     <td><strong>${b.nombre_colis || 1}</strong></td></tr>
      ${colisRows}
      <tr><td>N° Billet</td>    <td>${b.numero_billet ? 'Billet N\u00B0 ' + b.numero_billet : '—'}</td></tr>
      <tr><td>Programme</td>    <td>${voy.programme || '—'}</td></tr>
      <tr><td>Bus</td>          <td>${voy.numero_bus || '—'}</td></tr>
      <tr><td>Agent</td>        <td>${agentNom}</td></tr>
      <tr><td>Caissier(e)</td>  <td>${agentNom}</td></tr>
    </table>

    <div class="recu-montant">
      <div class="rm-label">Montant Total</div>
      <div class="rm-val">${montantF} FCFA</div>
      <div style="font-size:10px;color:#888;margin-top:4px;">
        Tarif&nbsp;: ${montantF}&nbsp;F &nbsp;|&nbsp; Remise&nbsp;: ${remiseF}&nbsp;F &nbsp;|&nbsp;
        <strong style="color:#0e1621;">Net&nbsp;: ${netF}&nbsp;FCFA</strong>
      </div>
    </div>

    <div class="recu-avis">
      <strong>AVIS TRÈS IMPORTANT :</strong> Ce reçu fait foi. Présentez-le obligatoirement
      pour récupérer vos colis. Diarra Transport décline toute responsabilité en cas de perte
      de ce reçu. Guichet 20802142 – La validité du colis est garantie jusqu'à destination.
    </div>

    ${qrSvg ? `<div class="recu-qr" style="text-align:center;margin-top:10px;padding-top:8px;border-top:1px dashed #ccc;">
      <div style="font-size:9px;color:#999;margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em;">Scanner pour vérification</div>
      <div style="display:inline-block;">${qrSvg}</div>
      <div style="font-size:9px;color:#aaa;margin-top:3px;font-family:'Space Mono',monospace;">${b.code_bagage}</div>
    </div>` : ''}`;
}
</script>
<script>
// ── Construit les étiquettes ─────────────────────────────────
function buildEtiquettes(b) {
  const cl       = b.client  || {};
  const voy      = b.voyage  || {};
  const nb       = parseInt(b.nombre_colis) || 1;
  const nom      = ((cl.prenom||'') + ' ' + (cl.nom||'')).trim() || '—';
  const dep      = (voy.ville_depart  || '—').toUpperCase();
  const arr      = (voy.ville_arrivee || '—').toUpperCase();
  const colisArr = b.colis || [];

  let html = '';
  for (let i = 1; i <= nb; i++) {
    const numColis  = b.code_bagage + '-C' + i;
    const colisData = colisArr[i - 1] || {};
    const nature    = colisData.nature || b.nature || '—';
    html += `
    <div class="etiquette">
      <div class="etiq-head">
        ${logoSvg(32)}
        <div class="e-brand">
          <span>DIARRA TRANSPORT</span>
          <small>Gestion Bagages</small>
        </div>
      </div>
      <div class="etiq-code">${numColis}</div>
      <div class="etiq-body">
        <div class="etiq-body-row"><span>Voyageur :</span><span><strong>${nom}</strong></span></div>
        <div class="etiq-body-row"><span>Téléphone :</span><span>${cl.telephone || '—'}</span></div>
        <div class="etiq-body-row"><span>Nature :</span><span>${nature}</span></div>
        <div class="etiq-body-row"><span>Billet :</span><span>${b.numero_billet ? 'N\u00B0 ' + b.numero_billet : '—'}</span></div>
        <div class="etiq-route">${dep} &#x279C; ${arr}</div>
      </div>
      <div class="etiq-cut">&#9986; À coller sur le colis &nbsp;·&nbsp; Colis ${i}/${nb}</div>
    </div>`;
  }

  document.getElementById('etiqHtml').innerHTML = html;
}

/* ══════════════════════════════════════════════════════════════
   IMPRESSION
   Cible : EPSON TM-T20III – papier thermique 80mm (76mm imprimable)
   ══════════════════════════════════════════════════════════════ */

// ── Styles thermique reçu 80mm ────────────────────────────────
function getRecuPrintStyles() {
  return `<style>
    @page { size: 80mm auto; margin: 3mm 2mm; }
    * { box-sizing: border-box; margin: 0; padding: 0;
        -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body {
      width: 76mm; max-width: 76mm; margin: 0 auto; padding: 2mm;
      font-family: 'Courier New', Courier, monospace;
      font-size: 11px; color: #000; background: #fff;
    }
    .thermal-wrap { width: 76mm; max-width: 76mm; }
    /* En-tête */
    .tt-header   { text-align: center; margin-bottom: 6px; }
    .tt-company  { font-size: 13px; font-weight: bold; letter-spacing: .5px; }
    .tt-address  { font-size: 9px; line-height: 1.5; margin-top: 2px; }
    /* Numéro billet */
    .tt-billet   { text-align: center; font-size: 14px; font-weight: bold;
                   margin: 8px 0 2px; }
    .tt-route    { text-align: center; font-size: 11px; font-weight: bold; }
    .tt-date     { text-align: center; font-size: 10px; margin-bottom: 6px; }
    /* Séparateurs */
    .tt-separator { border: none; border-top: 1px solid #000; margin: 5px 0; }
    .tt-dashed    { border: none; border-top: 1px dashed #000; margin: 6px 0; }
    /* Lignes infos */
    .tt-row {
      display: flex; justify-content: space-between;
      margin: 3px 0; font-size: 11px;
    }
    .tt-row .lbl { font-weight: bold; flex-shrink: 0; min-width: 90px; }
    .tt-row .val { text-align: right; font-weight: bold; flex: 1; }
    /* Lignes importantes : destination, client, téléphone */
    .tt-big { font-size: 14px !important; border-top: 1px solid #eee; border-bottom: 1px solid #eee; margin: 4px 0; padding: 3px 0; }
    .tt-big .lbl { font-size: 12px !important; }
    .tt-big .val { font-size: 14px !important; font-weight: 900 !important; }
    /* Avis */
    .tt-avis-title {
      text-align: center; font-size: 13px; font-weight: bold;
      text-decoration: underline; margin: 8px 0 4px;
    }
    .tt-avis-text { font-size: 10px; line-height: 1.6; }
    /* Caissier */
    .tt-caissier {
      font-size: 9px; display: flex; justify-content: space-between;
      margin-top: 3px; color: #333;
    }
    /* QR Code – TOUJOURS EN DERNIER, centré, grand */
    .tt-qr { text-align: center; margin-top: 12px; padding-top: 8px; }
    .tt-qr svg { width: 120px !important; height: 120px !important;
                 display: block; margin: 0 auto; }
    @media print {
      body { width: 76mm; max-width: 76mm; padding: 0; }
      .no-print { display: none !important; }
    }
  </style>`;
}

// ── Styles thermique étiquettes 80mm ─────────────────────────
function getEtiqPrintStyles() {
  return `<style>
    @page { size: 80mm auto; margin: 3mm 2mm; }
    * { box-sizing: border-box; margin: 0; padding: 0;
        -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body {
      width: 76mm; max-width: 76mm; margin: 0 auto; padding: 2mm;
      font-family: 'Courier New', Courier, monospace;
      font-size: 11px; color: #000; background: #fff;
    }
    .etiquette {
      width: 76mm; max-width: 76mm;
      border: 2px dashed #c9a84c; border-radius: 4px;
      background: #fffdf5; margin-bottom: 8mm;
      page-break-after: always; page-break-inside: avoid;
      overflow: hidden;
    }
    .etiq-head {
      background: #0e1621; padding: 6px 8px;
      display: flex; align-items: center; gap: 6px;
    }
    .e-brand span  { font-size: 10px; font-weight: bold;
                     color: #c9a84c; display: block; }
    .e-brand small { font-size: 8px; color: #7a90a8; }
    .etiq-code {
      background: #c9a84c; color: #0e1621; text-align: center;
      font-family: 'Courier New', monospace; font-size: 12px;
      font-weight: bold; padding: 5px; letter-spacing: .08em;
    }
    .etiq-body { padding: 6px 8px; font-size: 10px; color: #000; }
    .etiq-body-row {
      display: flex; justify-content: space-between;
      margin: 2px 0; gap: 6px;
    }
    /* Voyageur et téléphone en gros */
    .etiq-big { font-size: 13px !important; font-weight: 900; margin: 4px 0; padding: 2px 0; border-bottom: 1px solid #e0e0e0; }
    .etiq-route {
      background: #f5f0e0; text-align: center; padding: 5px;
      margin: 5px 0; font-size: 12px; font-weight: bold;
      color: #0e1621; border-radius: 3px;
    }
    .etiq-cut {
      font-size: 9px; color: #999; text-align: center;
      padding: 3px; border-top: 1px dashed #ccc;
    }
    @media print {
      body { width: 76mm; padding: 0; }
      .etiquette { page-break-after: always; }
    }
  </style>`;
}

// ── Construit le HTML thermique du reçu ──────────────────────
function buildThermalRecu(b, qrSvg) {
  const cl   = b.client  || {};
  const voy  = b.voyage  || {};
  const usr  = b.user    || {};
  const montant = parseInt(b.montant || 0);
  const remise  = parseInt(b.remise  || 0);
  const net     = montant - remise;
  const nom     = ((cl.prenom||'') + ' ' + (cl.nom||'')).trim().toUpperCase() || '—';
  const dep     = (voy.ville_depart  || '—').toUpperCase();
  const arr     = (voy.ville_arrivee || '—').toUpperCase();
  const prog    = (voy.programme     || (dep + ' → ' + arr)).toUpperCase();
  const agentNom = ((usr.prenom||'') + ' ' + (usr.nom||'')).trim().toUpperCase() || '—';
  const now      = new Date();
  const dateHeure = now.toLocaleDateString('fr-FR') + ' ' + now.toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'});

  const dateDepStr = formatFrenchDateLong(voy.date_depart || b.date_depart);

  const billetDisplay = b.numero_billet
    ? 'Billet N\u00B0 ' + b.numero_billet
    : 'N\u00B0 ' + b.code_bagage;

  const montantFmt = montant.toLocaleString('fr-FR') + 'F';
  const remiseFmt  = remise.toLocaleString('fr-FR')  + 'F';
  const netFmt     = net.toLocaleString('fr-FR')     + 'F';

  let colisThermalRows = '';
  const colisList = b.colis || [];
  if (colisList.length > 0) {
    colisList.forEach((c, i) => {
      const px = parseInt(c.prix || 0).toLocaleString('fr-FR') + 'F';
      const val = c.valeur ? ` (Val: ${c.valeur})` : '';
      colisThermalRows += `<div class="tt-row"><span class="lbl">COLIS ${i+1}:</span><span class="val">${c.nature || '—'}${val} - ${px}</span></div>`;
    });
  } else {
    colisThermalRows = `<div class="tt-row"><span class="lbl">NATURE:</span><span class="val">${b.nature||'—'}</span></div>`;
  }

  return `
  <div class="thermal-wrap">

    <!-- 1. EN-TÊTE -->
    <div class="tt-header">
      <div class="tt-company">DIARRA TRANSPORT</div>
      <div class="tt-address">
        Près de la maternité Sogoniko-Bamako-Mali Tél.<br>(223) 20802142
      </div>
    </div>

    <!-- 2. NUMÉRO BILLET -->
    <div class="tt-billet">${billetDisplay}</div>

    <!-- 3. TRAJET + DATE -->
    <div class="tt-route">${dep} --&gt; ${arr}</div>
    <div class="tt-date">${dateDepStr}</div>

    <!-- 4. SÉPARATEUR -->
    <hr class="tt-separator">

    <!-- 5. INFOS VOYAGE -->
    <div class="tt-row"><span class="lbl">DEPART:</span>      <span class="val">${prog}</span></div>
    <div class="tt-row tt-big"><span class="lbl">DESTINATION:</span> <span class="val">${arr}</span></div>
    <div class="tt-row tt-big"><span class="lbl">CLIENT:</span>      <span class="val">${nom}</span></div>
    <div class="tt-row tt-big"><span class="lbl">TEL:</span>         <span class="val">${cl.telephone || '—'}</span></div>
    <div class="tt-row"><span class="lbl">NB COLIS:</span>    <span class="val">${b.nombre_colis||1}</span></div>
    ${colisThermalRows}
    <div class="tt-row">
      <span class="lbl">TARIF:</span><span class="val">${montantFmt}</span>
      <span class="lbl" style="margin-left:8px">REMISE:</span><span class="val">${remiseFmt}</span>
    </div>
    <div class="tt-row"><span class="lbl">MONTANT:</span>     <span class="val">${netFmt}</span></div>

    <!-- 6. CAISSIER + DATE/HEURE -->
    <div class="tt-caissier">
      <span>Caissier(e): ${agentNom}</span>
      <span>${dateHeure}</span>
    </div>

    <!-- 7. SÉPARATEUR -->
    <hr class="tt-separator">

    <!-- 8. AVIS TRES IMPORTANT -->
    <div class="tt-avis-title">AVIS TRES IMPORTANT</div>
    <div class="tt-avis-text">
      Informer le guichet pour toute annulation ou report de voyage avant le départ.
      Guichet 20802142 La validité du billet est de deux semaines.
    </div>

    <!-- 9. QR CODE – EN DERNIER -->
    <div class="tt-qr">${qrSvg || ''}</div>

  </div>`;
}

// ── Impression reçu thermique 80mm ───────────────────────────
function printRecu() {
  const b     = window._lastBagage || null;
  const qrSvg = window._lastQrSvg  || '';

  if (!b || !b.code_bagage) {
    showToast('error','Données bagage manquantes.'); return;
  }

  const w = window.open('', '_blank', 'width=340,height=900,scrollbars=yes');
  if (!w) { showToast('error','Popup bloqué – autorisez les popups pour ce site.'); return; }
  w.document.open();
  w.document.write(`<!DOCTYPE html><html lang="fr"><head>
    <meta charset="UTF-8">
    <title>Reçu – Diarra Transport</title>
    ${getRecuPrintStyles()}
  </head><body>
    ${buildThermalRecu(b, qrSvg)}
    <script>window.onload=function(){setTimeout(function(){window.print();},300);};<\/script>
  </body></html>`);
  w.document.close();
}

// ── Construit le HTML thermique d'une seule étiquette ────────
function buildThermalEtiquette(b, index) {
  const cl       = b.client  || {};
  const voy      = b.voyage  || {};
  const nb       = parseInt(b.nombre_colis) || 1;
  const nom      = ((cl.prenom||'') + ' ' + (cl.nom||'')).trim().toUpperCase() || '—';
  const dep      = (voy.ville_depart  || '—').toUpperCase();
  const arr      = (voy.ville_arrivee || '—').toUpperCase();
  const colisArr = b.colis || [];
  const colisData = colisArr[index - 1] || {};
  const nature    = (colisData.nature || b.nature || '—').toUpperCase();
  const numColis  = b.code_bagage + '-C' + index;

  return `
  <div class="etiquette">
    <div class="etiq-head">
      <div class="e-brand">
        <span>DIARRA TRANSPORT</span>
        <small>Gestion Bagages – Bamako, Mali</small>
      </div>
    </div>
    <div class="etiq-code">${numColis}</div>
    <div class="etiq-body">
      <div class="etiq-body-row etiq-big"><span>Voyageur :</span><span><strong>${nom}</strong></span></div>
      <div class="etiq-body-row etiq-big"><span>Téléphone :</span><span><strong>${cl.telephone || '—'}</strong></span></div>
      <div class="etiq-body-row"><span>Nature :</span><span>${nature}</span></div>
      <div class="etiq-body-row"><span>Billet :</span><span>${b.numero_billet ? 'N° ' + b.numero_billet : '—'}</span></div>
      <div class="etiq-route">${dep} &#x279C; ${arr}</div>
    </div>
    <div class="etiq-cut">Colis ${index}/${nb} &nbsp;·&nbsp; Coller sur le colis</div>
  </div>`;
}

// ── Impression étiquettes (toutes d'un coup) ─────────────────
function printEtiq() {
  const b = window._lastBagage || null;
  if (!b || !b.code_bagage) { showToast('error','Données bagage manquantes.'); return; }

  const nb = parseInt(b.nombre_colis) || 1;
  let allEtiq = '';
  for (let i = 1; i <= nb; i++) {
    allEtiq += buildThermalEtiquette(b, i);
  }

  const w = window.open('', '_blank', 'width=340,height=900,scrollbars=yes');
  if (!w) { showToast('error','Popup bloqué – autorisez les popups pour ce site.'); return; }
  w.document.open();
  w.document.write(`<!DOCTYPE html><html lang="fr"><head>
    <meta charset="UTF-8">
    <title>Étiquettes – Diarra Transport</title>
    ${getEtiqPrintStyles()}
  </head><body>
    ${allEtiq}
    <script>window.onload=function(){setTimeout(function(){window.print();},300);};<\/script>
  </body></html>`);
  w.document.close();
}

// ── Impression séquentielle dans UNE SEULE fenêtre ──────────
// Reçu → 2s → Étiquette 1 → 2s → Étiquette 2 …
function printTout() {
  const b     = window._lastBagage || null;
  const qrSvg = window._lastQrSvg  || '';

  if (!b || !b.code_bagage) { showToast('error','Données bagage manquantes.'); return; }

  const nb    = parseInt(b.nombre_colis) || 1;
  const DELAI = 2000; // 2 secondes entre chaque impression

  // Ouvrir une seule fenêtre dès le clic (pas de blocage navigateur)
  const w = window.open('', '_blank', 'width=340,height=900,scrollbars=yes');
  if (!w) {
    showToast('error', 'Popup bloqué – autorisez les popups pour ce site dans la barre d\'adresse.');
    return;
  }

  // Construire la séquence : [reçu, etiq1, etiq2, ...]
  const sequence = [];
  sequence.push({ label: 'Reçu', html: buildThermalRecu(b, qrSvg), styles: getRecuPrintStyles() });
  for (let i = 1; i <= nb; i++) {
    sequence.push({ label: `Étiquette colis ${i}/${nb}`, html: buildThermalEtiquette(b, i), styles: getEtiqPrintStyles() });
  }

  showToast('info', `Impression : reçu + ${nb} étiquette(s) – 2 sec entre chaque`);

  // Écrire la structure de base une seule fois
  w.document.open();
  w.document.write(`<!DOCTYPE html><html lang="fr"><head>
    <meta charset="UTF-8">
    <title>Impression – Diarra Transport</title>
    <style>
      * { box-sizing:border-box; margin:0; padding:0; }
      body { background:#fff; }
      @media print { .no-print { display:none !important; } }
    </style>
  </head><body><div id="printContent"></div></body></html>`);
  w.document.close();

  // Fonction récursive : affiche le contenu, imprime, puis passe au suivant
  function imprimerEtape(index) {
    if (index >= sequence.length) {
      // Tout terminé – fermer la modal de prévisualisation
      closeModalRecu();
      showToast('success', 'Impression terminée');
      return;
    }

    const etape = sequence[index];

    // Injecter les styles + contenu dans la fenêtre
    try {
      // Remplacer les styles dans le head
      const oldStyle = w.document.getElementById('printStyle');
      if (oldStyle) oldStyle.remove();
      const styleEl = w.document.createElement('div');
      styleEl.id = 'printStyle';
      styleEl.innerHTML = etape.styles;
      w.document.head.appendChild(styleEl);

      // Remplacer le contenu
      w.document.getElementById('printContent').innerHTML = etape.html;
    } catch(e) {
      showToast('error', 'Erreur fenêtre : ' + e.message);
      return;
    }

    // Lancer l'impression
    setTimeout(() => {
      try {
        w.focus();
        const estDerniere = (index === sequence.length - 1);

        if (estDerniere) {
          // Injecter afterprint directement dans la fenêtre enfant (plus fiable)
          try {
            const scriptEl = w.document.createElement('script');
            scriptEl.textContent = 'window.onafterprint = function(){ window.close(); };';
            w.document.body.appendChild(scriptEl);
          } catch(e) {}
        }

        w.print();
        showToast('success', etape.label + ' envoyé à l\'imprimante');
      } catch(e) {
        showToast('error', 'Erreur impression : ' + e.message);
      }
      // Passer à l'étape suivante après le délai
      setTimeout(() => imprimerEtape(index + 1), DELAI);
    }, 400);
  }

  // Démarrer la séquence une fois la fenêtre chargée
  setTimeout(() => imprimerEtape(0), 300);
}

// ── Réimpression depuis la liste des bagages ─────────────────
function fetchAndPrintRecu(id) {
  fetch('/bagages/' + id + '/receipt', {
    headers: { 'Accept': 'application/json' }
  })
  .then(r => {
    if (!r.ok) throw new Error('HTTP ' + r.status);
    return r.json();
  })
  .then(data => {
    if (data.bagage) {
      openModalRecu(data.bagage, data.qr_code || '');
    } else {
      showToast('error', 'Données du bagage introuvables.');
    }
  })
  .catch(err => showToast('error', 'Impossible de charger le reçu : ' + err.message));
}
</script>
