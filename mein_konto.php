<?php
require_once __DIR__ . '/header.php';

// Nur USER darf hier rein
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$msg = "";

// User-Daten laden
$user = $db->selectOne("SELECT * FROM users WHERE UserID = ?", [$userId]);

// ----- Persönliche Daten ändern -----
if (isset($_POST['update_name'])) {
    $vn = trim($_POST['vorname'] ?? '');
    $nn = trim($_POST['nachname'] ?? '');

    if ($vn !== '' && $nn !== '') {
        $db->execute("UPDATE users SET Vorname=?, Nachname=? WHERE UserID=?", [$vn, $nn, $userId]);
        $_SESSION['user_name'] = "$vn $nn";
        $msg = "Daten wurden erfolgreich aktualisiert.";
    }
}

// ----- E-Mail ändern -----
if (isset($_POST['update_email'])) {
    $email = trim($_POST['email'] ?? '');
    $pass = trim($_POST['passwort_check'] ?? '');

    if ($email !== '' && $pass !== '') {
        if (password_verify($pass, $user['Passwort'])) {
            $taken = $db->selectOne("SELECT UserID FROM users WHERE Email=? AND UserID!=?", [$email, $userId]);
            if ($taken) {
                $msg = "Diese E-Mail ist bereits vergeben.";
            } else {
                $db->execute("UPDATE users SET Email=? WHERE UserID=?", [$email, $userId]);
                $_SESSION['user_email'] = $email;
                $msg = "E-Mail wurde erfolgreich geändert.";
            }
        } else {
            $msg = "Passwort stimmt nicht.";
        }
    }
}

// ----- Passwort ändern -----
if (isset($_POST['update_pass'])) {
    $p1 = trim($_POST['pass1'] ?? '');
    $p2 = trim($_POST['pass2'] ?? '');

    if ($p1 !== '' && $p1 === $p2) {
        $hash = password_hash($p1, PASSWORD_BCRYPT);
        $db->execute("UPDATE users SET Passwort=? WHERE UserID=?", [$hash, $userId]);
        $msg = "Passwort wurde erfolgreich geändert.";
    } else {
        $msg = "Passwörter stimmen nicht überein.";
    }
}

// User nach Änderung neu laden
$user = $db->selectOne("SELECT * FROM users WHERE UserID = ?", [$userId]);
?>

<h1 class="mb-4">Mein Konto</h1>

<?php if ($msg): ?>
  <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- Persönliche Daten -->
<h4>Persönliche Daten</h4>
<form method="post" class="mb-4">
  <div class="row g-2">
    <div class="col-md-6">
      <label class="form-label">Vorname</label>
      <input class="form-control" name="vorname" value="<?= htmlspecialchars($user['Vorname']) ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Nachname</label>
      <input class="form-control" name="nachname" value="<?= htmlspecialchars($user['Nachname']) ?>" required>
    </div>
  </div>
  <button class="btn btn-primary mt-2" name="update_name">Speichern</button>
</form>

<!-- E-Mail ändern -->
<h4>E-Mail ändern</h4>
<form method="post" class="mb-4">
  <div class="mb-2">
    <label class="form-label">Neue E-Mail</label>
    <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>
  </div>
  <div class="mb-2">
    <label class="form-label">Passwort bestätigen</label>
    <input class="form-control" type="password" name="passwort_check" required>
  </div>
  <button class="btn btn-primary" name="update_email">E-Mail aktualisieren</button>
</form>

<!-- Passwort ändern -->
<h4>Passwort ändern</h4>
<form method="post" class="mb-4">
  <div class="mb-2">
    <label class="form-label">Neues Passwort</label>
    <input class="form-control" type="password" name="pass1" required>
  </div>
  <div class="mb-2">
    <label class="form-label">Neues Passwort wiederholen</label>
    <input class="form-control" type="password" name="pass2" required>
  </div>
  <button class="btn btn-primary" name="update_pass">Passwort aktualisieren</button>
</form>

<?php require_once __DIR__ . '/footer.php'; ?>
