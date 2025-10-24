<?php
require_once __DIR__ . '/header.php';

$oid = (int)($_GET['id'] ?? 0);
if ($oid <= 0) {
  echo '<div class="alert alert-warning">Keine Bestellung gewählt.</div><a class="btn btn-primary" href="bestellungen.php">Zurück</a>';
  require_once __DIR__ . '/footer.php'; exit;
}

$order = $db->selectOne("SELECT * FROM bestellungen WHERE BestellungID = ?", [$oid]);
if (!$order) {
  echo '<div class="alert alert-warning">Bestellung nicht gefunden.</div><a class="btn btn-primary" href="bestellungen.php">Zurück</a>';
  require_once __DIR__ . '/footer.php'; exit;
}

$pos = $db->select("SELECT * FROM bestellpositionen WHERE BestellungID = ?", [$oid]);

$total = 0.0; $items = 0;
foreach ($pos as $p) { $total += (float)$p['ProduktPreis'] * (int)$p['Menge']; $items += (int)$p['Menge']; }
?>
<h1 class="mb-3">Bestellung #<?= (int)$order['BestellungID'] ?></h1>

<div class="card mb-4">
  <div class="card-body">
    <div><strong>Kunde:</strong> <?= htmlspecialchars($order['KundeVorname'] . ' ' . $order['KundeNachname']) ?></div>
    <div><strong>E-Mail:</strong> <?= htmlspecialchars($order['KundeEmail']) ?></div>
    <div><strong>Datum:</strong> <?= htmlspecialchars($order['Bestelldatum']) ?></div>
    <div class="mt-2"><strong>Artikel:</strong> <?= (int)$items ?> &nbsp; | &nbsp; <strong>Gesamt:</strong> <?= euro($total) ?></div>
  </div>
</div>

<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th style="width:90px;">Produkt-ID</th>
        <th>Produkt</th>
        <th style="width:140px;">Preis</th>
        <th style="width:120px;">Menge</th>
        <th style="width:140px;">Summe</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pos as $p): 
        $sum = (float)$p['ProduktPreis'] * (int)$p['Menge'];
      ?>
      <tr>
        <td><?= (int)$p['ProduktID'] ?></td>
        <td><?= htmlspecialchars($p['ProduktName']) ?></td>
        <td><?= euro($p['ProduktPreis']) ?></td>
        <td><?= (int)$p['Menge'] ?></td>
        <td><?= euro($sum) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<a href="bestellungen.php" class="btn btn-secondary mt-3">Zurück</a>

<?php require_once __DIR__ . '/footer.php'; ?>
