<?php
require_once __DIR__ . '/db.php';

function mmh_admin_user() {
  return $_SESSION['admin_user'] ?? null;
}

function mmh_require_admin() {
  if (!mmh_admin_user()) {
    mmh_redirect('login.php');
  }
}
