<?php
if (!function_exists('euro')) {
  function euro($v) {
    if ($v === null || $v === '') return '—';
    return number_format((float)$v, 2, ',', '.') . ' €';
  }
}

if (!function_exists('cart_count')) {
  function cart_count() {
    $sum = 0;
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
      foreach ($_SESSION['cart'] as $qty) { $sum += (int)$qty; }
    }
    return $sum;
  }
}

if (!function_exists('cart_add')) {
  function cart_add($productId, $qty = 1) {
    $pid = (string)max(0, (int)$productId);
    $qty = max(1, (int)$qty);
    if (empty($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] = 0;
    $_SESSION['cart'][$pid] += $qty;
  }
}

if (!function_exists('cart_set')) {
  function cart_set($productId, $qty) {
    $pid = (string)max(0, (int)$productId);
    $qty = max(0, (int)$qty);
    if ($qty <= 0) unset($_SESSION['cart'][$pid]);
    else $_SESSION['cart'][$pid] = $qty;
  }
}

if (!function_exists('cart_clear')) {
  function cart_clear() { unset($_SESSION['cart']); }
}
