<?php
require_once __DIR__ . '/header.php';

// Bestellungen laden
$orders = $db->select("SELECT * FROM bestellungen ORDER BY BestellungID DESC");

// Summen für jede Bestellung holen
$totals = [];
if ($orders) {
  $ids = array_column($orders, 'BestellungID');
  $ph  = implode(',', array_fill(0, count($ids), '?'));
  $rows = $db->select(
    "SELECT BestellungID, SUM(ProduktPreis*Menge) AS summe, SUM(Menge) AS anzahl
     FROM bestellpositionen
     WHERE BestellungID IN ($ph)
     GROUP BY BestellungID",
     $ids
  );
  foreach ($rows as $r) {
    $totals[(int)$r['BestellungID']] = [
      'summe' => (float)$r['summe'],
      'anzahl'=> (int)$r['anzahl']
    ];
  }
}
?>
<h1 class="mb-4">Bestellungen</h1>

<?php if (!$orders): ?>
  <div class="alert alert-info">Noch keine Bestellungen.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:90px;">#</th>
          <th>Kunde</th>
          <th>E-Mail</th>
          <th style="width:190px;">Datum</th>
          <th style="width:140px;">Artikel</th>
          <th style="width:140px;">Gesamt</th>
          <th style="width:140px;"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): 
          $oid = (int)$o['BestellungID'];
          $sum = $totals[$oid]['summe'] ?? 0.0;
          $cnt = $totals[$oid]['anzahl'] ?? 0;
        ?>
        <tr>
          <td>#<?= $oid ?></td>
          <td><?= htmlspecialchars($o['KundeVorname'] . ' ' . $o['KundeNachname']) ?></td>
          <td><?= htmlspecialchars($o['KundeEmail']) ?></td>
          <td><?= htmlspecialchars($o['Bestelldatum']) ?></td>
          <td><?= (int)$cnt ?></td>
          <td><?= euro($sum) ?></td>
          <td><a class="btn btn-sm btn-primary" href="bestellung_details.php?id=<?= $oid ?>">Details anzeigen</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
