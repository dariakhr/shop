<?php
require_once __DIR__ . '/header.php';

$orderId = (int)($_GET['order'] ?? 0);
if ($orderId <= 0) {
  echo '<div class="alert alert-warning">Keine Bestellung gefunden.</div><a class="btn btn-primary" href="index2.php">Zur Startseite</a>';
  require_once __DIR__ . '/footer.php'; exit;
}

// Bestellung laden
$order = $db->selectOne("SELECT * FROM bestellungen WHERE BestellungID = ?", [$orderId]);
if (!$order) {
  echo '<div class="alert alert-warning">Bestellung nicht gefunden.</div><a class="btn btn-primary" href="index2.php">Zur Startseite</a>';
  require_once __DIR__ . '/footer.php'; exit;
}

// Positionen & Summen
$pos = $db->select("SELECT * FROM bestellpositionen WHERE BestellungID = ?", [$orderId]);
$items = 0; $total = 0.0;
foreach ($pos as $p) {
  $items += (int)$p['Menge'];
  $total += (float)$p['ProduktPreis'] * (int)$p['Menge'];
}
?>
<div class="text-center py-5">
  <h1>Danke für Ihre Bestellung!</h1>
  <p class="lead mb-1">Bestellnummer: <strong>#<?= (int)$order['BestellungID'] ?></strong></p>
  <p class="text-muted">Datum: <?= htmlspecialchars($order['Bestelldatum']) ?></p>

  <div class="d-inline-block text-start mt-3">
    <div><strong>Artikel:</strong> <?= (int)$items ?></div>
    <div><strong>Gesamt:</strong> <?= euro($total) ?></div>
    <div><strong>Kunde:</strong> <?= htmlspecialchars($order['KundeVorname'] . ' ' . $order['KundeNachname']) ?> (<?= htmlspecialchars($order['KundeEmail']) ?>)</div>
  </div>

  <div class="mt-4">
    <a href="index2.php" class="btn btn-primary">Zurück zum Shop</a>
  </div>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
