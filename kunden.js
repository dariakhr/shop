let __currentPos = null;

function FelderEditieren(pos)
{
    __currentPos = pos;

    const zeileID = document.getElementsByName("zeileID")[0];
    const id = document.getElementById(`id-${pos}`);
    zeileID.value = id.innerText;

    // VORNAME
    const vn = document.getElementById(`vn-${pos}`);
    let copy = vn.innerText;
    vn.innerHTML = `<input type="text" name="vorname" value="${escapeAttr(copy)}">`;

    // NACHNAME
    const nn = document.getElementById(`nn-${pos}`);
    copy = nn.innerText;
    nn.innerHTML = `<input type="text" name="nachname" value="${escapeAttr(copy)}">`;

    // EMAIL
    const em = document.getElementById(`em-${pos}`);
    copy = em.innerText;
    em.innerHTML = `<input type="email" name="email" value="${escapeAttr(copy)}">`;

    // TELEFON
    const tel = document.getElementById(`tel-${pos}`);
    copy = tel.innerText;
    tel.innerHTML = `<input type="text" name="telefon" value="${escapeAttr(copy)}">`;

    // KOMMENTAR 
    const comment = document.getElementById(`comment-${pos}`);
    copy = comment.innerText;
    comment.innerHTML = `<textarea name="comment" rows="3">${escapeHTML(copy)}</textarea>`;

    // BUTTONS
    const ed = document.getElementById(`ed-${pos}`);
    ed.innerHTML = `
        <button type="submit">Ändern</button>
        <button type="button" onclick="cancelEdit()">Abbrechen</button>
    `;
}

function ZeileEntfernen(pos)
{
    const zeileID = document.getElementsByName("zeileID")[0];
    const id = document.getElementById(`id-${pos}`);
    zeileID.value = id.innerText;

    if (confirm("Soll der Kunde wirklich gelöscht werden?")) {
        const entfernen = document.getElementsByName("entfernen")[0];
        entfernen.value = "ja";
        document.getElementById("editForm").submit();
    }
    else  {
        document.getElementById("editForm").preventDefault();
    }
}

function cancelEdit()
{
    window.location.reload();
}

function escapeAttr(s) {
    return String(s).replaceAll('"', '&quot;');
}

function escapeHTML(s) {
    return String(s)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;");
}
