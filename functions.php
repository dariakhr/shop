<?php
// €-Format
function euro($v) {
  if ($v === null || $v === '') return '—';
  return number_format((float)$v, 2, ',', '.') . ' €';
}

// Anzahl Artikel im Warenkorb (Session)
function cart_count() {
  $sum = 0;
  if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) { $sum += (int)$qty; }
  }
  return $sum;
}

// Artikel zum Warenkorb hinzufügen
function cart_add($productId, $qty = 1) {
  $pid = (string)max(0, (int)$productId);
  $qty = max(1, (int)$qty);
  if (empty($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] = 0;
  $_SESSION['cart'][$pid] += $qty;
}

// Menge setzen/entfernen
function cart_set($productId, $qty) {
  $pid = (string)max(0, (int)$productId);
  $qty = (int)$qty;
  if ($qty <= 0) unset($_SESSION['cart'][$pid]);
  else $_SESSION['cart'][$pid] = $qty;
}

// Warenkorb leeren
function cart_clear() { unset($_SESSION['cart']); }

?>
