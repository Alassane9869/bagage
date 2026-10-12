{{-- ══ MODAL IMPRESSION REÇU + ÉTIQUETTES ══════════════════════ --}}
<style>
.modal-print-overlay{
  display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);
  z-index:400;align-items:center;justify-content:center;padding:16px;
}
.modal-print-overlay.show{display:flex;}
.modal-print-box{
  background:var(--dark2);border:1px solid var(--border);
  border-radius:14px;width:100%;max-width:960px;max-height:90vh;
  display:flex;flex-direction:column;overflow:hidden;
}
.modal-print-header{
  display:flex;justify-content:space-between;align-items:center;
  padding:16px 20px;border-bottom:1px solid var(--border);flex-shrink:0;
}
.modal-print-header h2{font-size:1rem;font-weight:600;color:var(--gold);}
.modal-print-close{
  background:none;border:none;color:var(--text2);font-size:1.3rem;
  cursor:pointer;padding:4px;border-radius:6px;
}
.modal-print-close:hover{background:var(--dark3);color:var(--text);}
.modal-print-body{
  display:grid;grid-template-columns:1fr 1fr;gap:16px;
  padding:16px 20px;overflow-y:auto;flex:1;
}
.modal-print-footer{
  padding:14px 20px;border-top:1px solid var(--border);
  display:flex;gap:10px;flex-wrap:wrap;flex-shrink:0;
}
@media(max-width:700px){
  .modal-print-overlay{padding:8px;}
  .modal-print-box{max-height:94vh;border-radius:12px;}
  .modal-print-header{padding:12px 14px;}
  .modal-print-body{grid-template-columns:1fr;padding:12px 14px;}
  .modal-print-footer{padding:12px 14px;flex-direction:column;gap:8px;}
  .modal-print-footer button{width:100% !important;min-height:44px;justify-content:center;margin-left:0 !important;}
}

/* ── Reçu (fond blanc) ────────────────── */
.recu-client{
  background:#fff;color:#1a1a1a;border-radius:8px;
  padding:16px;font-size:12px;font-family:Arial,sans-serif;
}
.recu-header{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;}
.recu-header .r-brand{flex:1;}
.recu-header .r-brand h3{font-size:13px;font-weight:700;color:#0e1621;margin:0;}
.recu-header .r-brand p{font-size:10px;color:#555;margin:2px 0 0;}
.recu-code{
  font-family:'Space Mono',monospace;font-size:13px;font-weight:700;
  color:#c9a84c;background:#f9f6ef;border-radius:6px;
  padding:6px 12px;text-align:center;margin:8px 0;letter-spacing:.1em;
}
.recu-trajet{text-align:center;font-size:11px;font-weight:600;color:#333;margin:4px 0 8px;}
.recu-table{width:100%;border-collapse:collapse;margin:8px 0;}
.recu-table td{padding:5px 6px;border-bottom:1px solid #f0f0f0;font-size:11px;}
.recu-table td:first-child{color:#777;width:35%;}
.recu-montant{
  background:#f9f9f9;border-radius:6px;padding:10px;
  text-align:center;margin:10px 0;
}
.recu-montant .rm-label{font-size:10px;color:#777;text-transform:uppercase;letter-spacing:.05em;}
.recu-montant .rm-val{font-size:16px;font-weight:700;color:#0e1621;}
.recu-avis{background:#fffbe6;border-radius:4px;padding:7px;font-size:10px;color:#7a6000;margin:8px 0;}
.recu-talon{border-top:1px dashed #ccc;margin-top:10px;padding-top:10px;font-size:10px;color:#555;}
.recu-talon-row{display:flex;justify-content:space-between;margin:2px 0;}
/* QR code en bas du reçu – taille maîtrisée dans la preview */
.recu-client .recu-qr svg,
.recu-client .recu-qr img { width:80px !important; height:80px !important; }

/* ── Étiquettes ───────────────────────── */
.etiquettes-wrap{display:flex;flex-direction:column;gap:10px;}
.etiquette{
  border:2px dashed #c9a84c;background:#fffdf5;border-radius:8px;overflow:hidden;
  font-family:Arial,sans-serif;
}
.etiq-head{
  background:#0e1621;padding:8px 10px;display:flex;align-items:center;gap:8px;
}
.etiq-head .e-brand{flex:1;}
.etiq-head .e-brand span{font-size:10px;font-weight:700;color:#c9a84c;display:block;}
.etiq-head .e-brand small{font-size:9px;color:#7a90a8;}
.etiq-code{
  background:#c9a84c;color:#0e1621;text-align:center;
  font-family:'Space Mono',monospace;font-size:12px;font-weight:700;
  padding:5px;letter-spacing:.08em;
}
.etiq-body{padding:8px 10px;font-size:10px;color:#333;}
.etiq-body-row{display:flex;justify-content:space-between;margin:2px 0;}
/* Voyageur et téléphone en gros dans la preview */
.etiq-body-row.etiq-big{font-size:13px;font-weight:700;border-bottom:1px solid #e8e8e8;padding-bottom:3px;margin-bottom:4px;}
.etiq-route{
  background:#f5f0e0;text-align:center;padding:6px;margin:6px 0;
  font-size:12px;font-weight:700;color:#0e1621;border-radius:4px;
}
.etiq-cut{font-size:9px;color:#999;text-align:center;padding:3px;border-top:1px dashed #ccc;}
</style>

<div class="modal-print-overlay" id="modalPrint">
  <div class="modal-print-box">
    <div class="modal-print-header">
      <h2><i class="bi bi-printer"></i> Documents à Imprimer</h2>
      <button class="modal-print-close" onclick="closeModalRecu()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-print-body">

      {{-- Reçu client --}}
      <div>
        <div style="font-size:.75rem;color:var(--text2);margin-bottom:8px;font-weight:500;"><i class="bi bi-file-text"></i> Reçu Client (1 exemplaire)</div>
        <div class="recu-client" id="recuHtml"></div>
      </div>

      {{-- Étiquettes --}}
      <div>
        <div style="font-size:.75rem;color:var(--text2);margin-bottom:8px;font-weight:500;"><i class="bi bi-tags"></i> Étiquettes à Coller</div>
        <div class="etiquettes-wrap" id="etiqHtml"></div>
      </div>

    </div>
    <div class="modal-print-footer">
      <button class="btn-gold" onclick="printRecu()"><i class="bi bi-printer"></i> Imprimer Reçu Client</button>
      <button class="btn-outline-gold" style="background:rgba(26,107,191,.15);border-color:var(--blue);color:#60a5fa;" onclick="printEtiq()"><i class="bi bi-tags"></i> Imprimer Étiquettes</button>
      <button class="btn-outline-gold" style="background:rgba(43,122,68,.15);border-color:var(--green);color:#4ade80;" onclick="printTout()"><i class="bi bi-printer-fill"></i> Imprimer Tout <small style="font-size:.7rem;opacity:.8;">(séquentiel)</small></button>
      <button class="btn-reset" onclick="closeModalRecu()" style="margin-left:auto">Fermer</button>
    </div>
  </div>
</div>
