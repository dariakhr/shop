<?php
require 'db.php';


$db = new DB('localhost', 'testdb', 'root', 'root');

/* CREATE  */
if (isset($_GET["aktion"]) && $_GET["aktion"] === "1") {
    $name    = trim($_POST["name"] ?? "");
    $price   = $_POST["price"] ?? null;
    $amount  = $_POST["amount"] ?? null;
    $comment = trim($_POST["comment"] ?? "");

    $db->execute(
        "INSERT INTO produkte (ProduktName, ProduktPreis, ProduktMenge, ProduktKommentar) VALUES (?, ?, ?, ?)",
        [$name, $price, $amount, $comment]
    );
}

/* UPDATE / DELETE */
if (isset($_GET["aktion"]) && $_GET["aktion"] === "2") {
    $rowId = $_POST["zeileID"] ?? null;

    if (!empty($_POST["entfernen"]) && $_POST["entfernen"] === "ja") {
        $db->execute("DELETE FROM produkte WHERE ProduktID = ?", [$rowId]);

    } else {
        $name    = trim($_POST["name"] ?? "");
        $price   = $_POST["price"] ?? null;
        $amount  = $_POST["amount"] ?? null;
        $comment = trim($_POST["comment"] ?? "");

        $db->execute(
            "UPDATE produkte SET ProduktName = ?, ProduktPreis = ?, ProduktMenge = ?, ProduktKommentar = ? WHERE ProduktID = ?",
            [$name, $price, $amount, $comment, $rowId]
        );
    }
}

/* READ */
$produkte = $db->select("SELECT * FROM produkte ORDER BY ProduktID DESC");
?>
<!DOCTYPE HTML>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Produktverwaltung</title>
    <script src="nice.js"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 85%; border-collapse: collapse; border: 1px solid #ccc; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background: #f3f3f3; }
        textarea { resize: vertical; }
        .comment-cell { width: 300px; white-space: pre-wrap; }
        .top-form input, .top-form textarea { width: 260px; margin: 4px 0; }
        .actions button { margin-right: 6px; }
        button { cursor: pointer; }
    </style>
</head>
<body>

<h2>Produktverwaltung</h2>

<p><a href="kunden.php">→ Zur Kundenverwaltung</a></p>


<!-- NEUES PRODUKT -->
<form class="top-form" action="index.php?aktion=1" method="post" autocomplete="off">
    <input type="text" name="name" placeholder="Produktname" required><br>
    <input type="number" step="0.01" name="price" placeholder="Preis" required><br>
    <input type="number" name="amount" placeholder="Menge" required><br>
    <textarea name="comment" placeholder="Kommentar (optional)" rows="3"></textarea><br>
    <button type="submit">Hinzufügen</button>
</form>

<!-- UPDATE / DELETE FORM -->
<form id="editForm" action="index.php?aktion=2" method="post">
    <input type="hidden" name="zeileID" value="">
    <input type="hidden" name="entfernen" value="">

    <table>
        <tr>
            <th style="width:60px;">ID</th>
            <th>Produktname</th>
            <th style="width:110px;">Preis</th>
            <th style="width:110px;">Menge</th>
            <th class="comment-cell">Kommentar</th>
            <th style="width:160px;"></th>
        </tr>

        <?php
        $pos = 1;
        foreach ($produkte as $p):
        ?>
        <tr>
            <td id="id-<?= $pos ?>"><?= $p['ProduktID'] ?></td>
            <td id="name-<?= $pos ?>"><?= htmlspecialchars($p['ProduktName']) ?></td>
            <td id="price-<?= $pos ?>"><?= htmlspecialchars($p['ProduktPreis']) ?></td>
            <td id="amount-<?= $pos ?>"><?= htmlspecialchars($p['ProduktMenge']) ?></td>
            <td id="comment-<?= $pos ?>" class="comment-cell"><?= htmlspecialchars($p['ProduktKommentar']) ?></td>
            <td class="actions" id="ed-<?= $pos ?>">
                <button type="button" onclick="FelderEditieren(<?= $pos ?>)">Bearbeiten</button>
                <button type="button" onclick="ZeileEntfernen(<?= $pos ?>)">Entfernen</button>
            </td>
        </tr>
        <?php
            $pos++;
        endforeach;
        ?>
    </table>
</form>

</body>
</html>
