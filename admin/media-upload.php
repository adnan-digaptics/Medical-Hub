<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !mmh_csrf_check()) {
  http_response_code(403);
  echo json_encode(array('error' => 'Please try again.'));
  exit;
}
$path = mmh_save_upload('file');
if ($path === '') {
  http_response_code(400);
  echo json_encode(array('error' => 'Use a JPG, PNG, or WebP image.'));
  exit;
}
echo json_encode(array('location' => mmh_web_base() . '/' . ltrim($path, '/')));
