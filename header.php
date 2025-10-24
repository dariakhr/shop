<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$db = new DB('localhost', 'testdb', 'root', 'root'); // Port 8889 
$BASE = '/php-mit-db';

// aktuelle Datei
$current = basename($_SERVER['PHP_SELF']);

// kleine Helper-Funktion
function active($page, $current) { return $page === $current ? 'active fw-semibold' : ''; }

// Warenkorb-Zähler (nur für User/Gast)
$cartCount = cart_count();
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Mein Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .navbar-brand { font-weight: 700; }
    .active { color: #0d6efd !important; }

    .product-card img {
      width: 100%;
      height: 240px;
      object-fit: cover;
      display: block;
      background: #eee;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-light border-bottom">
  <div class="container">

    <!-- LOGO mit Rolle-Logik -->
    <?php if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
      <a class="navbar-brand" href="<?= $BASE ?>/admin/produkte_admin.php">Mein Shop</a>
    <?php else: ?>
      <a class="navbar-brand" href="<?= $BASE ?>/index2.php">Mein Shop</a>
    <?php endif; ?>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="nav" class="collapse navbar-collapse">

      <ul class="navbar-nav me-auto">

        <?php
        // GAST
        if (empty($_SESSION['user_role'])): ?>

          <li class="nav-item">
            <a class="nav-link <?= active('index2.php', $current) ?>" href="<?= $BASE ?>/index2.php">Produkte</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= active('cart.php', $current) ?>" href="<?= $BASE ?>/cart.php">
              Warenkorb<?= $cartCount ? " ({$cartCount})" : "" ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= active('login.php', $current) ?>" href="<?= $BASE ?>/login.php">Anmelden</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= active('registrierung.php', $current) ?>" href="<?= $BASE ?>/register.php">Registrierung</a>
          </li>

        <?php
        // USER
        elseif ($_SESSION['user_role'] === 'user'): ?>

          <li class="nav-item">
            <a class="nav-link <?= active('index2.php', $current) ?>" href="<?= $BASE ?>/index2.php">Produkte</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= active('cart.php', $current) ?>" href="<?= $BASE ?>/cart.php">
              Warenkorb<?= $cartCount ? " ({$cartCount})" : "" ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= active('mein_konto.php', $current) ?>" href="<?= $BASE ?>/mein_konto.php">Mein Konto</a>
          </li>

        <?php
        // ADMIN
        elseif ($_SESSION['user_role'] === 'admin'): ?>

          <li class="nav-item">
            <a class="nav-link <?= active('index2.php', $current) ?>" href="<?= $BASE ?>/index2.php">Produkte (Shop)</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= active('produkte_admin.php', $current) ?>" href="<?= $BASE ?>/admin/produkte_admin.php">Produkte bearbeiten</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= active('kunden.php', $current) ?>" href="<?= $BASE ?>/admin/kunden.php">Kunden</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= active('bestellungen.php', $current) ?>" href="<?= $BASE ?>/admin/bestellungen.php">Bestellungen</a>
          </li>

        <?php endif; ?>
      </ul>

      <!-- Rechts: Logout -->
      <ul class="navbar-nav ms-auto">
        <?php if (!empty($_SESSION['user_role'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/logout.php">Logout</a></li>
        <?php endif; ?>
      </ul>

    </div>
  </div>
</nav>

<div class="container py-4">
