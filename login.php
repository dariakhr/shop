<?php
session_start();
require_once __DIR__ . '/db.php';

$db = new DB('localhost', 'testdb', 'root', 'root'); 
$error = "";

//zurück zum Checkout?
$redirectCheckout = isset($_GET['from']) && $_GET['from'] === 'checkout';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if ($email === '' || $pass === '') {
        $error = "Bitte E-Mail und Passwort eingeben.";
    } else {
        $user = $db->selectOne("SELECT * FROM users WHERE Email = ?", [$email]);

        if ($user && password_verify($pass, $user['Passwort'])) {
            // Login erfolgreich → Session setzen
            $_SESSION['user_role']  = $user['Rolle'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_id']    = $user['UserID'];
            $_SESSION['user_name']  = $user['Vorname'] . ' ' . $user['Nachname'];

            // ADMIN → niemals в checkout
            if ($user['Rolle'] === 'admin') {
                header('Location: admin/produkte_admin.php');
                exit;
            }

            // USER
            header('Location: ' . ($redirectCheckout ? 'checkout.php' : 'index2.php'));
            exit;

        } else {
            $error = "Login fehlgeschlagen — falsche E-Mail oder Passwort.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Anmelden</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
  <div class="card shadow p-4" style="max-width: 380px; width:100%;">
    <h3 class="text-center mb-3">Anmelden</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php?<?= $redirectCheckout ? 'from=checkout' : '' ?>">
      <div class="mb-3">
        <label class="form-label">E-Mail</label>
        <input type="email" name="email" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Passwort</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">Anmelden</button>
    </form>

    <div class="text-center mt-3 small">
      Noch kein Konto? <a href="registrierung.php">Registrieren</a>
    </div>
  </div>
</div>

</body>
</html>
