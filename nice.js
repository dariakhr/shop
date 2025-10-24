let __currentPos = null;

function FelderEditieren(pos)
{
    __currentPos = pos;

    const zeileID = document.getElementsByName("zeileID")[0];
    const id = document.getElementById(`id-${pos}`);
    zeileID.value = id.innerText;

    // NAME
    let copy;
    const name = document.getElementById(`name-${pos}`);
    copy = name.innerText;
    name.innerHTML = `<input type="text" name="name" value="${escapeAttr(copy)}">`;

    // PRICE
    const price = document.getElementById(`price-${pos}`);
    copy = price.innerText;
    price.innerHTML = `<input type="number" step="0.01" name="price" value="${escapeAttr(copy)}">`;

    // AMOUNT
    const amount = document.getElementById(`amount-${pos}`);
    copy = amount.innerText;
    amount.innerHTML = `<input type="number" name="amount" value="${escapeAttr(copy)}">`;

    // COMMENT 
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

    if (confirm("Soll die Zeile wirklich gelöscht werden?")) {
        const entfernen = document.getElementsByName("entfernen")[0];
        entfernen.value = "ja";
        document.getElementById("editForm").submit();
    }
}

// abrechnen
function cancelEdit()
{
    window.location.reload();
}

/*  klammern in input */
function escapeAttr(s) {
    return String(s).replaceAll('"', '&quot;');
}

/* HTML in textarea */
function escapeHTML(s) {
    return String(s)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;");
}
