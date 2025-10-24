<?php
session_start();
require_once __DIR__ . '/db.php';

$db = new DB('localhost', 'testdb', 'root', 'root');
$error = "";

// Wenn Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vn = trim($_POST['vorname'] ?? '');
    $nn = trim($_POST['nachname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = trim($_POST['passwort'] ?? '');

    if ($vn === '' || $nn === '' || $email === '' || $pass === '') {
        $error = "Bitte alle Felder ausfüllen.";
    } else {
        // prüfen ob E-Mail bereits existiert
        $exists = $db->selectOne("SELECT UserID FROM users WHERE Email = ?", [$email]);
        if ($exists) {
            $error = "Diese E-Mail ist bereits registriert.";
        } else {
            // neues Passwort sicher speichern
            $hash = password_hash($pass, PASSWORD_BCRYPT);

            // neuen User speichern (Rolle = user)
            $db->execute(
                "INSERT INTO users (Vorname, Nachname, Email, Passwort, Rolle)
                 VALUES (?, ?, ?, ?, 'user')",
                [$vn, $nn, $email, $hash]
            );

            // automatisch einloggen
            $_SESSION['user_role'] = 'user';
            $_SESSION['user_email'] = $email;

            header('Location: index2.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrieren</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
  <div class="card shadow p-4" style="max-width: 420px; width:100%;">
    <h3 class="text-center mb-3">Registrieren</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
      <input class="form-control mb-2" name="vorname" placeholder="Vorname" required>
      <input class="form-control mb-2" name="nachname" placeholder="Nachname" required>
      <input class="form-control mb-2" type="email" name="email" placeholder="E-Mail" required>
      <input class="form-control mb-3" type="password" name="passwort" placeholder="Passwort" required>
      <button class="btn btn-success w-100">Jetzt registrieren</button>
    </form>

    <div class="text-center mt-3">
      Bereits ein Konto? <a href="login.php">Anmelden</a>
    </div>
  </div>
</div>

</body>
</html>
