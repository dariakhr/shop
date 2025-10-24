<?php
require __DIR__ . '/db.php';
try { $db = new DB('localhost','testdb','root','root'); } catch (Throwable $e) { echo "DB error: ".$e->getMessage(); exit; }

$u = $db->selectOne("SELECT * FROM users WHERE Email='admin@shop.de'");
if (!$u) { echo "No admin row found."; exit; }

echo "Hash (start): " . substr($u['Passwort'],0,4) . "<br>";
echo "Hash length: " . strlen($u['Passwort']) . "<br>";
echo "password_verify(admin123): " . (password_verify('admin123', $u['Passwort']) ? 'OK' : 'FAIL');
