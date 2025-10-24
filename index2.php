<?php

require_once __DIR__ . '/header.php';

// POST: Produkt zum Warenkorb
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'], $_POST['pid'])) {
  cart_add((int)$_POST['pid'], max(1, (int)($_POST['qty'] ?? 1)));
  header('Location: cart.php'); // nach Warenkorb weiterleiten
  exit;
}

// Optional: Suche
$q = trim($_GET['q'] ?? '');
$params = [];
$sql = "SELECT ProduktID, ProduktName, ProduktBild, ProduktPreis, ProduktMenge FROM produkte";
if ($q !== '') {
  $sql .= " WHERE ProduktName LIKE ?";
  $params[] = "%{$q}%";
}
$sql .= " ORDER BY ProduktID DESC";
$produkte = $db->select($sql, $params);
?>
<h1 class="mb-4">Produkte</h1>

<?php if ($q !== ''): ?>
  <p class="text-muted">Suche nach: <strong><?= htmlspecialchars($q) ?></strong></p>
<?php endif; ?>

<?php if (!$produkte): ?>
  <div class="alert alert-info">Keine Produkte gefunden.</div>
<?php else: ?>
  <div class="row g-4">
    <?php foreach ($produkte as $p): ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card product-card h-100">
          <?php $img = $p['ProduktBild'] ?: 'https://via.placeholder.com/600x400?text=Kein+Bild'; ?>
          <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['ProduktName']) ?>">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= htmlspecialchars($p['ProduktName']) ?></h5>
            <div class="mb-2 fw-semibold"><?= euro($p['ProduktPreis']) ?></div>
            <div class="mt-auto">
              <form method="post" class="d-flex gap-2">
                <input type="hidden" name="pid" value="<?= (int)$p['ProduktID'] ?>">
                <input type="number" name="qty" value="1" min="1" class="form-control" style="max-width:90px">
                <button class="btn btn-primary" name="add_to_cart" value="1" type="submit">In den Warenkorb</button>
              </form>
              <?php if ((int)$p['ProduktMenge'] <= 0): ?>
                <div class="text-danger mt-2 small">Nicht auf Lager</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
