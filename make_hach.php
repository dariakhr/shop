<?php
// make_hash.php — временный инструмент для генерации bcrypt-хеша
// Открой в браузере: http://localhost:8888/php-mit-db/make_hash.php

if (php_sapi_name() === 'cli') {
    echo "Запусти этот файл через браузер.\n";
    exit;
}

$password = $_GET['p'] ?? ''; // передай пароль через ?p=
if ($password === '') {
    echo "<h3>Gib ?p=DEIN_PASSWORT in der URL an (GET).</h3>";
    echo "<p>Beispiel: <a href='?p=admin123'>?p=admin123</a></p>";
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
?>
<!doctype html>
<meta charset="utf-8">
<h2>Bcrypt-Hash Generator</h2>
<p>Passwort (unsicher im URL übergeben): <strong><?= htmlspecialchars($password) ?></strong></p>
<p>Generierter Hash (kopieren und in die DB einfügen):</p>
<pre style="background:#f6f8fa;padding:10px;border-radius:6px;"><?= htmlspecialchars($hash) ?></pre>

<p>SQL zum Ausführen in phpMyAdmin:</p>
<pre style="background:#f6f8fa;padding:10px;border-radius:6px;">
UPDATE users
SET Passwort = '<?= htmlspecialchars($hash) ?>'
WHERE Email = 'admin@shop.de';
</pre>

<p>Danach öffne <a href="verify_admin.php">verify_admin.php</a> (если он есть) или попробуй логин.</p>
