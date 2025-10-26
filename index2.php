<?php
require_once __DIR__ . '/header.php';

// POST: Produkt zum Warenkorb
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'], $_POST['pid'])) {
  cart_add((int)$_POST['pid'], max(1, (int)($_POST['qty'] ?? 1)));
  header('Location: cart.php'); 
  exit;
}

// Suche
$q = trim($_GET['q'] ?? '');
$params = [];
$sql = "SELECT ProduktID, ProduktName, ProduktBild, ProduktPreis, ProduktMenge, ProduktKommentar FROM produkte";

if ($q !== '') {
  $sql .= " WHERE ProduktName LIKE ?";
  $params[] = "%{$q}%";
}
$sql .= " ORDER BY ProduktID DESC";
$produkte = $db->select($sql, $params);
?>

<h1 class="mb-4 text-center">Unsere Produkte</h1>

<style>
.product-card {
  border: none;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 14px rgba(0,0,0,0.08);
  transition: all .28s ease;
  background: #fff;
}
.product-card:hover {
  transform: translateY(-7px);
  box-shadow: 0 14px 32px rgba(0,0,0,0.20);
}

.product-card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  background: #ddd;
}

.product-card .card-body {
  padding: 18px;
}

.product-card h5 {
  font-weight: 600;
  margin-bottom: 6px;
}

.product-desc {
  font-size: 0.9rem;
  min-height: 42px;
  color: #6a6a6a;
  margin-bottom: 10px;
}

.product-price {
  font-size: 1.35rem;
  font-weight: 600;
  margin-bottom: 14px;
}

.btn-black {
  background: #111;
  color: #fff;
  border: none;
  width: 100%;
  transition: 0.25s;
}
.btn-black:hover {
  background: #000;
  color: #fff;
}
</style>

<?php if ($q !== ''): ?>
  <p class="text-muted text-center">Suche nach: <strong><?= htmlspecialchars($q) ?></strong></p>
<?php endif; ?>

<?php if (!$produkte): ?>
  <div class="alert alert-info text-center">Keine Produkte gefunden.</div>
<?php else: ?>
  <div class="row g-4">
    <?php foreach ($produkte as $p): ?>
      <div class="col-12 col-sm-6 col-md-6 col-lg-4">
        <div class="card product-card h-100">

          <?php $img = $p['ProduktBild'] ?: 'https://via.placeholder.com/600x400?text=Kein+Bild'; ?>
          <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['ProduktName']) ?>">

          <div class="card-body d-flex flex-column">
            <h5><?= htmlspecialchars($p['ProduktName']) ?></h5>

            <div class="product-desc">
              <?= nl2br(htmlspecialchars($p['ProduktKommentar'] ?? '')) ?>
            </div>

            <div class="product-price"><?= euro($p['ProduktPreis']) ?></div>

            <div class="mt-auto">
              <form method="post" class="d-flex gap-2">
                <input type="hidden" name="pid" value="<?= (int)$p['ProduktID'] ?>">
                <input type="number" name="qty" value="1" min="1" class="form-control" style="max-width:90px">
                <button class="btn btn-black" name="add_to_cart" value="1" type="submit">In den Warenkorb</button>
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
