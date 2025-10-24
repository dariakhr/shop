<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$db = new DB('localhost', 'testdb', 'root', 'root');

$cartCount = cart_count();

$current = basename($_SERVER['PHP_SELF']);
function active($page, $current) { return $page === $current ? 'active fw-semibold' : ''; }
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
  height: 200px;
  object-fit: cover;
  border-bottom: 1px solid #ddd;
}

  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-light border-bottom">
  <div class="container">

    <a class="navbar-brand" href="index2.php">Mein Shop</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">

        <li class="nav-item">
          <a class="nav-link <?= active('index2.php', $current) ?>" href="index2.php">Produkte</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= active('cart.php', $current) ?>" href="cart.php">
            Warenkorb<?= $cartCount ? " ({$cartCount})" : "" ?>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= active('kunden.php', $current) ?>" href="kunden.php">Kunden</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= active('produkte_admin.php', $current) ?>" href="produkte_admin.php">Produkte bearbeiten</a>
        </li>
        
        <li class="nav-item">
        <a class="nav-link <?= active('bestellungen.php', $current) ?>" href="bestellungen.php">Bestellungen</a>
        </li>


      </ul>

      <form class="d-flex" action="index2.php" method="get">
        <input class="form-control me-2" type="search" name="q" placeholder="Suche…" 
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button class="btn btn-outline-primary" type="submit">Suchen</button>
      </form>
    </div>
  </div>
</nav>

<div class="container py-4">
