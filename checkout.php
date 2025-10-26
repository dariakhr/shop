<?php
require_once __DIR__ . '/header.php';

// ADMIN darf niemals zur Kasse
if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header("Location: admin/produkte_admin.php");
    exit;
}

// USER check + Autofill
$userData = null;
$isUser = (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'user');

if ($isUser && !empty($_SESSION['user_id'])) {
    $userData = $db->selectOne(
        "SELECT Vorname, Nachname, Email FROM users WHERE UserID = ?",
        [$_SESSION['user_id']]
    );
}

// Warenkorb leer → zurück
if (cart_count() === 0) {
    echo '<div class="alert alert-warning">Ihr Warenkorb ist leer.</div>
          <a class="btn btn-primary" href="index2.php">Weiter einkaufen</a>';
    require_once __DIR__ . '/footer.php';
    exit;
}

// GAST muss erst auswählen (Login / Registrierung / Gast)
$guestContinue = isset($_GET['guest']);

if (!$isUser && !$guestContinue) {
    ?>
    <h1 class="mb-4">Zur Kasse</h1>
    <div class="card p-4">
      <p class="mb-3">Sie sind nicht eingeloggt. Wie möchten Sie fortfahren?</p>

      <div class="d-grid gap-2">
        <a href="checkout.php?guest=1" class="btn btn-secondary">Als Gast fortfahren</a>
        <a href="login.php?from=checkout" class="btn btn-primary">Anmelden</a>
        <a href="registrierung.php" class="btn btn-outline-primary">Registrieren</a>
      </div>
    </div>
    <?php
    require_once __DIR__ . '/footer.php';
    exit;
}

// Bestellung absenden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $vn = trim($_POST['vorname'] ?? '');
    $nn = trim($_POST['nachname'] ?? '');
    $em = trim($_POST['email'] ?? '');

    if ($vn === '' || $nn === '' || $em === '') {
        echo '<div class="alert alert-danger">Bitte alle Felder ausfüllen.</div>';
    } else {
        // Bestellung speichern
        $db->execute(
          "INSERT INTO bestellungen (KundeVorname, KundeNachname, KundeEmail) VALUES (?, ?, ?)",
          [$vn, $nn, $em]
        );
        $orderId = (int)$db->lastId();

        // Positionen speichern
        if (!empty($_SESSION['cart'])) {
            $ids = array_keys($_SESSION['cart']);
            $ph  = implode(',', array_fill(0, count($ids), '?'));
            $rows = $db->select("SELECT ProduktID, ProduktName, ProduktPreis FROM produkte WHERE ProduktID IN ($ph)", $ids);

            $map = [];
            foreach ($rows as $r) { $map[$r['ProduktID']] = $r; }

            foreach ($_SESSION['cart'] as $pid => $qty) {
                if ($qty <= 0 || !isset($map[$pid])) continue;
                $db->execute(
                  "INSERT INTO bestellpositionen (BestellungID, ProduktID, ProduktName, ProduktPreis, Menge)
                   VALUES (?, ?, ?, ?, ?)",
                  [$orderId, $pid, $map[$pid]['ProduktName'], $map[$pid]['ProduktPreis'], $qty]
                );
            }
        }

        cart_clear();
        header('Location: checkout_success.php?order=' . $orderId);
        exit;
    }
}
?>

<h1 class="mb-4">Zur Kasse</h1>

<div class="row g-4">
  <div class="col-md-7">
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Vorname</label>
        <input type="text" name="vorname" class="form-control"
               value="<?= $userData['Vorname'] ?? '' ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Nachname</label>
        <input type="text" name="nachname" class="form-control"
               value="<?= $userData['Nachname'] ?? '' ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">E-Mail</label>
        <input type="email" name="email" class="form-control"
               value="<?= $userData['Email'] ?? '' ?>" required>
      </div>

      <button class="btn btn-success" name="place_order" value="1">Jetzt bestellen</button>
      <a class="btn btn-link" href="cart.php">Zurück zum Warenkorb</a>
    </form>
  </div>

  <!-- Bestellübersicht -->
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

          $map=[]; 
          foreach ($rows as $r) { $map[$r['ProduktID']] = $r; }

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

<?php require_once __DIR__ . '/footer.php'; ?>
