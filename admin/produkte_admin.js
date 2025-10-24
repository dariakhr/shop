// Einfache Inline-Bearbeitung für Produkte (Admin)
let __p_pos = null;

function ProdEdit(pos) {
  __p_pos = pos;

  // ID ins Hidden-Feld schreiben
  const id = document.getElementById(`id-${pos}`).innerText.trim();
  document.getElementsByName("zeileID")[0].value = id;

  // Name
  const nameCell = document.getElementById(`name-${pos}`);
  const nameVal  = nameCell.innerText;
  nameCell.innerHTML = `<input class="form-control form-control-sm" type="text" name="name" value="${escAttr(nameVal)}">`;

  // Preis
  const priceCell = document.getElementById(`price-${pos}`);
  const priceVal  = priceCell.innerText;
  priceCell.innerHTML = `<input class="form-control form-control-sm" type="number" step="0.01" name="price" value="${escAttr(priceVal)}">`;

  // Menge
  const amountCell = document.getElementById(`amount-${pos}`);
  const amountVal  = amountCell.innerText;
  amountCell.innerHTML = `<input class="form-control form-control-sm" type="number" name="amount" value="${escAttr(amountVal)}">`;

  // Kommentar
  const commentCell = document.getElementById(`comment-${pos}`);
  const commentVal  = commentCell.innerText;
  commentCell.innerHTML = `<textarea class="form-control form-control-sm" name="comment" rows="3">${escHtml(commentVal)}</textarea>`;

  // Bild (altes merken + Upload-Feld)
  const imgCell = document.getElementById(`imgcell-${pos}`);
  const oldSrc  = imgCell.querySelector('img')?.getAttribute('src') || '';
  document.getElementsByName('old_image')[0].value = oldSrc;

  imgCell.innerHTML = `
    <div class="d-flex align-items-center gap-2">
      <img src="${escAttr(oldSrc || 'https://via.placeholder.com/60?text=Bild')}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;" alt="">
      <input class="form-control form-control-sm" type="file" name="bild" accept=".jpg,.jpeg,.png,.webp" style="max-width:180px;">
    </div>
  `;

  // Buttons ändern
  const ed = document.getElementById(`ed-${pos}`);
  ed.innerHTML = `
    <button type="submit" class="btn btn-sm btn-success">Speichern</button>
    <button type="button" class="btn btn-sm btn-secondary" onclick="ProdCancel()">Abbrechen</button>
  `;
}

function ProdRemove(pos) {
  const id = document.getElementById(`id-${pos}`).innerText.trim();
  document.getElementsByName("zeileID")[0].value = id;

  if (confirm("Produkt wirklich löschen?")) {
    document.getElementsByName("entfernen")[0].value = "ja";
    document.getElementById("editForm").submit();
  }
}

function ProdCancel() { window.location.reload(); }

// Hilfsfunktionen
function escAttr(s) { return String(s).replaceAll('"','&quot;'); }
function escHtml(s) {
  return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;');
}
