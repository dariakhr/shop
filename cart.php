<?php
require_once __DIR__ . '/header.php';

// Update quantities / remove / clear
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update']) && !empty($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $pid => $qty) { cart_set($pid, $qty); }
  }
  if (isset($_POST['remove']) && isset($_POST['pid'])) {
    cart_set((int)$_POST['pid'], 0);
  }
  if (isset($_POST['clear'])) { cart_clear(); }
  header('Location: cart.php'); exit;
}

// Build items from DB
$items = [];
$total = 0.0;

if (!empty($_SESSION['cart'])) {
  $ids = array_keys($_SESSION['cart']);
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $rows = $db->select("SELECT ProduktID, ProduktName, ProduktPreis, ProduktBild FROM produkte WHERE ProduktID IN ($placeholders)", $ids);
  $map = [];
  foreach ($rows as $r) { $map[$r['ProduktID']] = $r; }

  foreach ($_SESSION['cart'] as $pid => $qty) {
    $pid = (int)$pid; $qty = (int)$qty;
    if ($qty <= 0 || !isset($map[$pid])) continue;
    $row = $map[$pid];
    $sum = (float)$row['ProduktPreis'] * $qty;
    $total += $sum;
    $items[] = [
      'id'    => $pid,
      'name'  => $row['ProduktName'],
      'price' => $row['ProduktPreis'],
      'img'   => $row['ProduktBild'] ?: 'https://via.placeholder.com/120x80?text=Bild',
      'qty'   => $qty,
      'sum'   => $sum
    ];
  }
}
?>
<h1 class="mb-4">Warenkorb</h1>

<?php if (!$items): ?>
  <div class="alert alert-info">Dein Warenkorb ist leer.</div>
  <a href="index2.php" class="btn btn-primary">Weiter einkaufen</a>
<?php else: ?>
  <form method="post">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th style="width:90px;">Bild</th>
            <th>Produkt</th>
            <th style="width:140px;">Preis</th>
            <th style="width:120px;">Menge</th>
            <th style="width:140px;">Summe</th>
            <th style="width:120px;"></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><img src="<?= htmlspecialchars($it['img']) ?>" alt="" class="img-fluid" style="max-height:60px"></td>
            <td><?= htmlspecialchars($it['name']) ?></td>
            <td><?= euro($it['price']) ?></td>
            <td>
              <input type="number" class="form-control" name="qty[<?= (int)$it['id'] ?>]"
                     value="<?= (int)$it['qty'] ?>" min="0">
            </td>
            <td><?= euro($it['sum']) ?></td>
            <td>
              <button class="btn btn-outline-danger btn-sm" name="remove" value="1"
                      formaction="cart.php" formmethod="post"
                      onclick="this.form.pid.value='<?= (int)$it['id'] ?>'">Entfernen</button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <input type="hidden" name="pid" value="">
    <div class="d-flex justify-content-between align-items-center mt-3">
      <div>
        <button class="btn btn-outline-secondary" name="clear" value="1" type="submit">Warenkorb leeren</button>
        <button class="btn btn-outline-primary ms-2" name="update" value="1" type="submit">Mengen aktualisieren</button>
      </div>
      <div class="fs-5 fw-semibold">Gesamt: <?= euro($total) ?></div>
    </div>
  </form>

  <div class="mt-4">
    <a href="index2.php" class="btn btn-link">Weiter einkaufen</a>
    <a href="checkout.php" class="btn btn-success">Zur Kasse</a>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
