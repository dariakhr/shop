function KundEdit(pos) {
  // ID ins Hidden-Feld
  document.getElementsByName("zeileID")[0].value =
    document.getElementById(`id-${pos}`).innerText.trim();

  // Vorname
  const vn = document.getElementById(`vn-${pos}`);
  const vnVal = vn.innerText;
  vn.innerHTML = `<input class="form-control form-control-sm" name="vorname" value="${escAttr(vnVal)}">`;

  // Nachname
  const nn = document.getElementById(`nn-${pos}`);
  const nnVal = nn.innerText;
  nn.innerHTML = `<input class="form-control form-control-sm" name="nachname" value="${escAttr(nnVal)}">`;

  // Email
  const em = document.getElementById(`em-${pos}`);
  const emVal = em.innerText;
  em.innerHTML = `<input class="form-control form-control-sm" type="email" name="email" value="${escAttr(emVal)}">`;

  // Telefon
  const tel = document.getElementById(`tel-${pos}`);
  const telVal = tel.innerText;
  tel.innerHTML = `<input class="form-control form-control-sm" name="telefon" value="${escAttr(telVal)}">`;

  // Kommentar
  const com = document.getElementById(`comment-${pos}`);
  const comVal = com.innerText;
  com.innerHTML = `<textarea class="form-control form-control-sm" name="comment" rows="3">${escHtml(comVal)}</textarea>`;

  // Buttons
  const ed = document.getElementById(`ed-${pos}`);
  ed.innerHTML = `
    <button type="submit" class="btn btn-sm btn-success">Ändern</button>
    <button type="button" class="btn btn-sm btn-secondary" onclick="location.reload()">Abbrechen</button>
  `;
}

function KundRemove(pos) {
  document.getElementsByName("zeileID")[0].value =
    document.getElementById(`id-${pos}`).innerText.trim();
  if (confirm("Kunde wirklich löschen?")) {
    document.getElementsByName("entfernen")[0].value = "ja";
    document.getElementById("editForm").submit();
  }
}

// Helpers
function escAttr(s) { return String(s).replaceAll('"','&quot;'); }
function escHtml(s) {
  return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;');
}
