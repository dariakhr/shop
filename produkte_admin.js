let __p_pos = null;

function ProdEdit(pos) {
  __p_pos = pos;

  // ID в hidden
  const id = document.getElementById(`id-${pos}`).innerText.trim();
  document.getElementsByName("zeileID")[0].value = id;

  // NAME
  const nameCell = document.getElementById(`name-${pos}`);
  const nameVal  = nameCell.innerText;
  nameCell.innerHTML = `<input class="form-control form-control-sm" type="text" name="name" value="${escapeAttr(nameVal)}">`;

  // PRICE
  const priceCell = document.getElementById(`price-${pos}`);
  const priceVal  = priceCell.innerText;
  priceCell.innerHTML = `<input class="form-control form-control-sm" type="number" step="0.01" name="price" value="${escapeAttr(priceVal)}">`;

  // AMOUNT
  const amountCell = document.getElementById(`amount-${pos}`);
  const amountVal  = amountCell.innerText;
  amountCell.innerHTML = `<input class="form-control form-control-sm" type="number" name="amount" value="${escapeAttr(amountVal)}">`;

  // COMMENT
  const commentCell = document.getElementById(`comment-${pos}`);
  const commentVal  = commentCell.innerText;
  commentCell.innerHTML = `<textarea class="form-control form-control-sm" name="comment" rows="3">${escapeHTML(commentVal)}</textarea>`;

  // IMG upload + запоминаем старый путь в hidden old_image
  const imgCell = document.getElementById(`imgcell-${pos}`);
  const imgTag  = imgCell.querySelector('img');
  const oldSrc  = imgTag ? imgTag.getAttribute('src') : '';
  document.getElementsByName('old_image')[0].value = oldSrc;

  imgCell.innerHTML = `
    <div class="d-flex align-items-center gap-2">
      <img src="${escapeAttr(oldSrc || 'https://via.placeholder.com/60?text=Bild')}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;" alt="">
      <input class="form-control form-control-sm" type="file" name="bild" accept=".jpg,.jpeg,.png,.webp" style="max-width:180px;">
    </div>
  `;

  // Кнопки
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
    const entfernen = document.getElementsByName("entfernen")[0];
    entfernen.value = "ja";
    document.getElementById("editForm").submit();
  }
}

function ProdCancel() {
  // Быстрый и чистый откат
  window.location.reload();
}

// helpers
function escapeAttr(s) { return String(s).replaceAll('"','&quot;'); }
function escapeHTML(s) {
  return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;');
}
