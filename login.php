<?php
require_once __DIR__ . '/db.php';
session_start();

$db = new DB('localhost', 'testdb', 'root', 'root');

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');
    if ($email !== '' && $pass !== '') {
        // Muss geändert werden zum password_hash()/password_verify()
        $user = $db->selectOne("SELECT * FROM users WHERE Email = ? AND Passwort = MD5(?)", [$email, $pass]);
        if ($user) {
            $_SESSION['user_id']  = $user['UserID'];
            $_SESSION['user_role']= $user['Rolle']; // 'admin' or 'user'
            header("Location: index2.php");
            exit;
        } else {
            $err = "Login fehlgeschlagen — falsche Daten.";
        }
    } else {
        $err = "Bitte füllen Sie alle Felder aus.";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
  <div class="card shadow p-4" style="max-width: 380px; width:100%;">
    <h3 class="text-center mb-3">Login</h3>

    <?php if ($err): ?>
      <div class="alert alert-danger"><?= $err ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">E-Mail</label>
        <input type="email" name="email" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Passwort</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">Einloggen</button>
    </form>

    <div class="text-center mt-3">
      <a href="index2.php" class="small">Zurück zum Shop</a>
    </div>
  </div>
</div>
</body>
</html>
