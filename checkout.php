<?php
require_once __DIR__ . '/header.php';

// Wenn leer -> zurück
if (cart_count() === 0) {
  echo '<div class="alert alert-warning">Warenkorb ist leer.</div><a class="btn btn-primary" href="index2.php">Produkte ansehen</a>';
  require_once __DIR__ . '/footer.php'; exit;
}

// Bestellung verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
  $vn = trim($_POST['vorname'] ?? '');
  $nn = trim($_POST['nachname'] ?? '');
  $em = trim($_POST['email'] ?? '');

  if ($vn === '' || $nn === '' || $em === '') {
    echo '<div class="alert alert-danger">Bitte alle Felder ausfüllen.</div>';
  } else {
  
    $db->execute(
      "INSERT INTO bestellungen (KundeVorname, KundeNachname, KundeEmail) VALUES (?, ?, ?)",
      [$vn, $nn, $em]
    );
    $orderId = (int)$db->lastId();

    // Positionen aus Session in DB schreiben
    $itemsCount = 0; $total = 0.0;
    if (!empty($_SESSION['cart'])) {
      $ids = array_keys($_SESSION['cart']);
      $ph  = implode(',', array_fill(0, count($ids), '?'));
      $rows = $db->select("SELECT ProduktID, ProduktName, ProduktPreis FROM produkte WHERE ProduktID IN ($ph)", $ids);
      $map=[]; foreach ($rows as $r) { $map[$r['ProduktID']] = $r; }

      foreach ($_SESSION['cart'] as $pid => $qty) {
        $pid = (int)$pid; $qty = (int)$qty;
        if ($qty <= 0 || !isset($map[$pid])) continue;
        $pname = $map[$pid]['ProduktName'];
        $pprice= (float)$map[$pid]['ProduktPreis'];

        $db->execute(
          "INSERT INTO bestellpositionen (BestellungID, ProduktID, ProduktName, ProduktPreis, Menge)
           VALUES (?, ?, ?, ?, ?)",
          [$orderId, $pid, $pname, $pprice, $qty]
        );
        $itemsCount += $qty;
        $total += $pprice * $qty;
      }
    }

    // Warenkorb leeren + weiter
    cart_clear();
    header('Location: checkout_success.php?order=' . $orderId);
    exit;
  }
}
?>
<h1 class="mb-3">Zur Kasse</h1>
<p class="text-muted">Bitte geben Sie Ihre Kontaktdaten ein. Es erfolgt keine Online-Zahlung.</p>

<div class="row g-4">
  <div class="col-md-7">
    <form method="post" class="needs-validation" novalidate>
      <div class="mb-3">
        <label class="form-label">Vorname</label>
        <input type="text" class="form-control" name="vorname" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Nachname</label>
        <input type="text" class="form-control" name="nachname" required>
      </div>
      <div class="mb-3">
        <label class="form-label">E-Mail</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <button class="btn btn-success" name="place_order" value="1">Jetzt bestellen</button>
      <a class="btn btn-link" href="cart.php">Zurück zum Warenkorb</a>
    </form>
  </div>

  <div class="col-md-5">
    <div class="card">
      <div class="card-header fw-semibold">Bestellübersicht</div>
      <div class="card-body">
        <?php
        $sum = 0.0; $cnt = 0;
        if (!empty($_SESSION['cart'])) {
          $ids = array_keys($_SESSION['cart']);
          $ph  = implode(',', array_fill(0, count($ids), '?'));
          $rows = $db->select("SELECT ProduktID, ProduktName, ProduktPreis FROM produkte WHERE ProduktID IN ($ph)", $ids);
          $map=[]; foreach ($rows as $r) { $map[$r['ProduktID']] = $r; }
          echo '<ul class="list-group list-group-flush">';
          foreach ($_SESSION['cart'] as $pid=>$qty) {
            if (!isset($map[$pid])) continue;
            $line = $map[$pid]['ProduktPreis'] * $qty;
            $sum += $line; $cnt += $qty;
            echo '<li class="list-group-item d-flex justify-content-between"><span>'
                 . htmlspecialchars($map[$pid]['ProduktName']) . ' × ' . (int)$qty
                 . '</span><span>' . euro($line) . '</span></li>';
          }
          echo '</ul>';
        }
        ?>
        <div class="d-flex justify-content-between mt-3">
          <span class="fw-semibold">Artikel:</span><span class="fw-semibold"><?= (int)$cnt ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="fw-semibold">Gesamt:</span><span class="fw-semibold"><?= euro($sum) ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Bootstrap-Formvalidierung
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
