<?php
require_once __DIR__ . '/auth.php'; 
require_once __DIR__ . '/../header.php'; 

// CREATE
if (isset($_GET["aktion"]) && $_GET["aktion"] === "1") {
    $vn = trim($_POST["vorname"] ?? "");
    $nn = trim($_POST["nachname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $tel = trim($_POST["telefon"] ?? "");
    $comment = trim($_POST["comment"] ?? "");

    $db->execute(
        "INSERT INTO kunden (Vorname, Nachname, Email, Telefon, Kommentar) VALUES (?, ?, ?, ?, ?)",
        [$vn, $nn, $email, $tel, $comment]
    );
    header("Location: kunden.php");
    exit;
}

// UPDATE / DELETE
if (isset($_GET["aktion"]) && $_GET["aktion"] === "2") {
    $rowId = $_POST["zeileID"] ?? null;

    if (!empty($_POST["entfernen"]) && $_POST["entfernen"] === "ja") {
        $db->execute("DELETE FROM kunden WHERE KundenID = ?", [$rowId]);

    } else {
        $vn = trim($_POST["vorname"] ?? "");
        $nn = trim($_POST["nachname"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $tel = trim($_POST["telefon"] ?? "");
        $comment = trim($_POST["comment"] ?? "");

        $db->execute(
            "UPDATE kunden SET Vorname=?, Nachname=?, Email=?, Telefon=?, Kommentar=? WHERE KundenID=?",
            [$vn, $nn, $email, $tel, $comment, $rowId]
        );
    }
    header("Location: kunden.php");
    exit;
}

$kunden = $db->select("SELECT * FROM kunden ORDER BY KundenID DESC");
?>

<h1 class="mb-4">Kundenverwaltung</h1>

<!-- Neuer Kunde Button -->
<div class="mb-3">
  <button class="btn btn-success" onclick="document.getElementById('addBox').classList.toggle('d-none');">
    + Neuer Kunde
  </button>
</div>

<!-- Formular für neuen Kunden -->
<div id="addBox" class="card p-3 mb-4 d-none">
  <form action="kunden.php?aktion=1" method="post">
    <div class="row g-2 mb-2">
      <div class="col"><input class="form-control" name="vorname" placeholder="Vorname" required></div>
      <div class="col"><input class="form-control" name="nachname" placeholder="Nachname" required></div>
    </div>
    <input class="form-control mb-2" name="email" placeholder="E-Mail" required>
    <input class="form-control mb-2" name="telefon" placeholder="Telefon">
    <textarea class="form-control mb-2" name="comment" placeholder="Kommentar" rows="3"></textarea>
    <button class="btn btn-primary">Speichern</button>
  </form>
</div>

<form id="editForm" action="kunden.php?aktion=2" method="post">
  <input type="hidden" name="zeileID" value="">
  <input type="hidden" name="entfernen" value="">

  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:60px;">ID</th>
          <th>Vorname</th>
          <th>Nachname</th>
          <th>Email</th>
          <th>Telefon</th>
          <th style="width:300px;">Kommentar</th>
          <th style="width:160px;">Aktionen</th>
        </tr>
      </thead>
      <tbody>
      <?php $pos = 1; foreach ($kunden as $k): ?>
        <tr>
          <td id="id-<?= $pos ?>"><?= $k['KundenID'] ?></td>
          <td id="vn-<?= $pos ?>"><?= htmlspecialchars($k['Vorname']) ?></td>
          <td id="nn-<?= $pos ?>"><?= htmlspecialchars($k['Nachname']) ?></td>
          <td id="em-<?= $pos ?>"><?= htmlspecialchars($k['Email']) ?></td>
          <td id="tel-<?= $pos ?>"><?= htmlspecialchars($k['Telefon']) ?></td>
          <td id="comment-<?= $pos ?>" style="white-space:pre-wrap;"><?= htmlspecialchars($k['Kommentar']) ?></td>
          <td id="ed-<?= $pos ?>">
            <button type="button" class="btn btn-sm btn-primary" onclick="KundEdit(<?= $pos ?>)">Bearbeiten</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="KundRemove(<?= $pos ?>)">Löschen</button>
          </td>
        </tr>
      <?php $pos++; endforeach; ?>
      </tbody>
    </table>
  </div>
</form>

<script src="kunden.js"></script>
<?php require_once __DIR__ . '/../footer.php'; ?>

