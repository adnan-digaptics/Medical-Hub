<?php
if (!defined('MMH_ROOT')) {
  define('MMH_ROOT', dirname(__DIR__));
}

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
ini_set('expose_php', '0');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
header_remove('X-Powered-By');

function mmh_is_https() {
  return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443);
}

function mmh_web_base() {
  $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
  if (preg_match('#^(.*?)/admin/#', $script, $m)) {
    return $m[1];
  }
  $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
  return $dir === '/' ? '' : $dir;
}

function mmh_url($path = 'index') {
  $path = trim((string) $path);
  $frag = '';
  $query = '';
  if ($path === '') {
    $path = 'index';
  }
  if (strpos($path, '#') !== false) {
    list($path, $frag) = explode('#', $path, 2);
    $frag = '#' . $frag;
  }
  if (strpos($path, '?') !== false) {
    list($path, $query) = explode('?', $path, 2);
    $query = '?' . $query;
  }
  $path = preg_replace('/\.php$/i', '', ltrim($path, '/'));
  if ($path === '' || $path === 'index') {
    return './' . ($query !== '' ? $query : '') . $frag;
  }
  return $path . $query . $frag;
}

function mmh_send_security_headers() {
  static $sent = false;
  if ($sent || headers_sent()) {
    return;
  }
  $sent = true;
  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: SAMEORIGIN');
  header('Referrer-Policy: strict-origin-when-cross-origin');
  header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
  header('X-XSS-Protection: 0');
  header("Content-Security-Policy: default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' data: https://fonts.gstatic.com; script-src 'self' 'unsafe-inline'; frame-src 'self' https://www.google.com https://maps.google.com; connect-src 'self'");
  if (mmh_is_https()) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
  }
}

function mmh_boot_session() {
  if (session_status() === PHP_SESSION_ACTIVE) {
    return;
  }
  $path = mmh_web_base();
  if ($path === '') {
    $path = '/';
  }
  session_set_cookie_params(array(
    'lifetime' => 0,
    'path' => $path,
    'secure' => mmh_is_https(),
    'httponly' => true,
    'samesite' => 'Lax',
  ));
  session_name('MMHSESSID');
  session_start();
}

mmh_send_security_headers();
mmh_boot_session();

function mmh_h($value) {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function mmh_slugify($text) {
  $text = strtolower(trim($text));
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  $text = trim($text, '-');
  return $text !== '' ? $text : 'post';
}

function mmh_csrf_token() {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    mmh_boot_session();
  }
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function mmh_csrf_field() {
  echo '<input type="hidden" name="csrf_token" value="' . mmh_h(mmh_csrf_token()) . '">';
}

function mmh_csrf_check() {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    mmh_boot_session();
  }
  $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
  return $token !== '' && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function mmh_post_image_pool() {
  return array(
    'assets/img/post_1.jpeg',
    'assets/img/post_2.jpeg',
    'assets/img/post_3.jpeg',
    'assets/img/post_4.jpeg',
    'assets/img/post_5.jpeg',
    'assets/img/post_6.jpeg',
    'assets/img/unsplash_primary_care.jpg',
    'assets/img/unsplash_geriatric.jpg',
    'assets/img/unsplash_womens_health.jpg',
    'assets/img/unsplash_adult_day.jpg',
    'assets/img/unsplash_home_care.jpg',
    'assets/img/unsplash_home_health.jpg',
    'assets/img/unsplash_connected_care.jpg',
    'assets/img/latest_post_1.jpeg',
    'assets/img/latest_post_2.jpeg',
    'assets/img/latest_post_3.jpeg',
    'assets/img/offerings01.jpg',
    'assets/img/offerings02.jpg',
    'assets/img/offerings03.jpg',
    'assets/img/hero_slider_2.jpg',
    'assets/img/hero_slider_3.jpg',
    'assets/img/hero_slider_4.jpg',
  );
}

function mmh_post_image($post) {
  $image = is_array($post) ? ($post['image'] ?? '') : (string) $post;
  if ($image === '') {
    $pool = mmh_post_image_pool();
    $id = is_array($post) ? max(1, (int) ($post['id'] ?? 1)) : 1;
    return $pool[($id - 1) % count($pool)];
  }
  if (preg_match('#^(https?:)?//#', $image)) {
    return $image;
  }
  return $image;
}

function mmh_next_unused_post_image($ignoreId = 0) {
  $used = array();
  try {
    $stmt = mmh_db()->prepare('SELECT image FROM posts WHERE id != :id AND image <> ""');
    $stmt->execute(array('id' => (int) $ignoreId));
    foreach ($stmt->fetchAll() as $row) {
      $used[$row['image']] = true;
    }
  } catch (Throwable $e) {
    $used = array();
  }
  foreach (mmh_post_image_pool() as $candidate) {
    if (!isset($used[$candidate])) {
      return $candidate;
    }
  }
  $pool = mmh_post_image_pool();
  return $pool[(int) $ignoreId % count($pool)];
}

function mmh_post_url($post) {
  return mmh_url('blog-details?slug=' . rawurlencode($post['slug']));
}

function mmh_honeypot_field() {
  echo '<div class="mmh_hp" aria-hidden="true"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>';
}

function mmh_honeypot_filled() {
  return trim((string) ($_POST['website'] ?? '')) !== '';
}

function mmh_rate_limit($action, $max = 8, $window = 600) {
  $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
  $dir = MMH_ROOT . '/includes/.rate';
  if (!is_dir($dir)) {
    mkdir($dir, 0750, true);
  }
  $file = $dir . '/' . hash('sha256', $action . '|' . $ip) . '.json';
  $now = time();
  $hits = array();
  if (is_file($file)) {
    $raw = json_decode((string) file_get_contents($file), true);
    if (is_array($raw)) {
      $hits = $raw;
    }
  }
  $hits = array_values(array_filter($hits, function ($t) use ($now, $window) {
    return (int) $t > ($now - $window);
  }));
  if (count($hits) >= $max) {
    return false;
  }
  $hits[] = $now;
  file_put_contents($file, json_encode($hits), LOCK_EX);
  return true;
}

function mmh_guard_public_post($action, $redirect, $okMessage = '') {
  if (mmh_honeypot_filled()) {
    if ($okMessage !== '') {
      mmh_flash('ok', $okMessage);
    }
    mmh_redirect($redirect);
  }
  if (!mmh_rate_limit($action, 6, 600)) {
    mmh_flash('error', 'Please wait a few minutes before sending another request.');
    mmh_redirect($redirect);
  }
  if (!mmh_csrf_check()) {
    mmh_flash('error', 'Please try again.');
    mmh_redirect($redirect);
  }
}

function mmh_safe_attr_url($url) {
  $url = trim(html_entity_decode((string) $url, ENT_QUOTES, 'UTF-8'));
  if ($url === '' || preg_match('#^\s*javascript:#i', $url) || preg_match('#^\s*data:#i', $url)) {
    return '';
  }
  if (preg_match('#^(https?:|mailto:|tel:|#|/|\.\./|[a-z0-9_.-]+/)#i', $url)) {
    return $url;
  }
  return '';
}

function mmh_sanitize_post_html($html) {
  $html = (string) $html;
  $html = preg_replace('#<!--.*?-->#s', '', $html);
  $html = preg_replace('#<(script|iframe|object|embed|form|style|link|meta)[^>]*>.*?</\1>#is', '', $html);
  $html = preg_replace('#<(script|iframe|object|embed|form|style|link|meta)[^>]*/?>#is', '', $html);
  $allowed = '<p><br><br/><strong><b><em><i><u><s><ul><ol><li><h2><h3><h4><h5><h6><blockquote><a><img><hr><table><thead><tbody><tr><th><td><figure><figcaption><span><div><pre><code>';
  $html = strip_tags($html, $allowed);
  $html = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
  $html = preg_replace_callback('/\s(href|src)\s*=\s*(["\'])(.*?)\2/i', function ($m) {
    $safe = mmh_safe_attr_url($m[3]);
    return $safe === '' ? '' : ' ' . strtolower($m[1]) . '=' . $m[2] . htmlspecialchars($safe, ENT_QUOTES, 'UTF-8') . $m[2];
  }, $html);
  $html = preg_replace('/\s(style|srcset|srcdoc)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
  $html = preg_replace_callback('/\sclass\s*=\s*(["\'])(.*?)\2/i', function ($m) {
    $class = trim(preg_replace('/[^a-zA-Z0-9 _-]/', '', $m[2]));
    return $class === '' ? '' : ' class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"';
  }, $html);
  return $html;
}

function mmh_safe_html($html) {
  return mmh_sanitize_post_html($html);
}

function mmh_paragraphs($text) {
  $text = trim((string) $text);
  if ($text === '') {
    return '';
  }
  if (preg_match('/<\s*(p|h[1-6]|ul|ol|blockquote|img|div|figure|table)\b/i', $text)) {
    return mmh_sanitize_post_html($text);
  }
  $parts = preg_split("/\n\s*\n/", $text);
  $html = '';
  foreach ($parts as $part) {
    $html .= '<p>' . nl2br(mmh_h(trim($part))) . '</p>';
  }
  return $html;
}

function mmh_post_content($text) {
  return mmh_paragraphs($text);
}

function mmh_flash($key, $message = null) {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    mmh_boot_session();
  }
  if ($message === null) {
    if (empty($_SESSION['flash'][$key])) {
      return '';
    }
    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $value;
  }
  $_SESSION['flash'][$key] = $message;
}

function mmh_redirect($path) {
  $path = (string) $path;
  $isAdmin = strpos(str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '')), '/admin/') !== false;
  if (!$isAdmin && !preg_match('#^(https?:)?//|^/|\.\./#i', $path)) {
    $path = mmh_url($path);
  }
  header('Location: ' . $path);
  exit;
}

function mmh_published_posts($limit = 12, $search = '', $category = '') {
  try {
    $sql = 'SELECT * FROM posts WHERE status = :status';
    $params = array('status' => 'published');
    if ($search !== '') {
      $sql .= ' AND (title LIKE :q OR excerpt LIKE :q2 OR content LIKE :q3)';
      $params['q'] = '%' . $search . '%';
      $params['q2'] = '%' . $search . '%';
      $params['q3'] = '%' . $search . '%';
    }
    if ($category !== '') {
      $sql .= ' AND category = :category';
      $params['category'] = $category;
    }
    $sql .= ' ORDER BY published_at DESC, id DESC';
    $stmt = mmh_db()->prepare($sql);
    $stmt->execute($params);
    $unique = array();
    $seenTitles = array();
    $usedImages = array();
    foreach ($stmt->fetchAll() as $row) {
      $titleKey = strtolower(preg_replace('/\s+/', ' ', trim($row['title'])));
      if (isset($seenTitles[$titleKey])) {
        continue;
      }
      $seenTitles[$titleKey] = true;
      $image = mmh_post_image($row);
      if (isset($usedImages[$image])) {
        foreach (mmh_post_image_pool() as $candidate) {
          if (!isset($usedImages[$candidate])) {
            $image = $candidate;
            break;
          }
        }
      }
      $usedImages[$image] = true;
      $row['image'] = $image;
      $unique[] = $row;
      if ($limit > 0 && count($unique) >= $limit) {
        break;
      }
    }
    return $unique;
  } catch (Throwable $e) {
    return array();
  }
}

function mmh_post_by_slug($slug) {
  try {
    $stmt = mmh_db()->prepare('SELECT * FROM posts WHERE slug = :slug AND status = :status LIMIT 1');
    $stmt->execute(array('slug' => $slug, 'status' => 'published'));
    return $stmt->fetch();
  } catch (Throwable $e) {
    return false;
  }
}

function mmh_all_categories() {
  try {
    return mmh_db()->query('SELECT * FROM post_categories ORDER BY name')->fetchAll();
  } catch (Throwable $e) {
    return array();
  }
}

function mmh_post_categories() {
  try {
    $rows = mmh_db()->query("SELECT c.name AS category, c.slug AS slug, COUNT(p.id) AS total FROM post_categories c LEFT JOIN posts p ON p.category = c.name AND p.status = 'published' GROUP BY c.id, c.name, c.slug ORDER BY c.name")->fetchAll();
    if ($rows) {
      return $rows;
    }
    return mmh_db()->query("SELECT category, COUNT(*) AS total FROM posts WHERE status = 'published' GROUP BY category ORDER BY category")->fetchAll();
  } catch (Throwable $e) {
    return array();
  }
}

function mmh_setting($key, $default = '') {
  static $cache = null;
  if ($cache === null) {
    $cache = array();
    try {
      foreach (mmh_db()->query('SELECT setting_key, setting_value FROM settings') as $row) {
        $cache[$row['setting_key']] = $row['setting_value'];
      }
    } catch (Throwable $e) {
      return $default;
    }
  }
  return array_key_exists($key, $cache) ? $cache[$key] : $default;
}

function mmh_unique_slug($title, $ignoreId = 0) {
  $base = mmh_slugify($title);
  $slug = $base;
  $i = 2;
  while (true) {
    $stmt = mmh_db()->prepare('SELECT id FROM posts WHERE slug = :slug AND id != :id LIMIT 1');
    $stmt->execute(array('slug' => $slug, 'id' => $ignoreId));
    if (!$stmt->fetch()) {
      return $slug;
    }
    $slug = $base . '-' . $i;
    $i++;
  }
}

function mmh_save_upload($field, $subdir = 'blog') {
  if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    return '';
  }
  $file = $_FILES[$field];
  if (($file['error'] ?? 1) !== UPLOAD_ERR_OK) {
    return '';
  }
  if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
    return '';
  }
  $allowed = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp');
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);
  if (!isset($allowed[$mime])) {
    return '';
  }
  $dir = MMH_ROOT . '/uploads/' . $subdir;
  if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
  }
  $name = date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
  $dest = $dir . '/' . $name;
  if (!move_uploaded_file($file['tmp_name'], $dest)) {
    return '';
  }
  return 'uploads/' . $subdir . '/' . $name;
}

function mmh_combine_preferred($date, $time) {
  $date = trim((string) $date);
  $time = trim((string) $time);
  if ($date === '') {
    return '';
  }
  if ($time === '') {
    $time = '09:00';
  }
  $ts = strtotime($date . ' ' . $time);
  return $ts ? date('Y-m-d H:i:s', $ts) : '';
}

function mmh_preferred_timestamp($value) {
  $value = trim((string) $value);
  if ($value === '') {
    return false;
  }
  $ts = strtotime($value);
  return $ts ?: false;
}

function mmh_format_preferred($value) {
  $ts = mmh_preferred_timestamp($value);
  if (!$ts) {
    return trim((string) $value);
  }
  return date('l, F j, Y \a\t g:i A', $ts);
}

function mmh_preferred_input_date($value) {
  $ts = mmh_preferred_timestamp($value);
  return $ts ? date('Y-m-d', $ts) : '';
}

function mmh_preferred_input_time($value) {
  $ts = mmh_preferred_timestamp($value);
  return $ts ? date('H:i', $ts) : '';
}

function mmh_booking_when_html($value) {
  $ts = mmh_preferred_timestamp($value);
  if (!$ts) {
    $raw = trim((string) $value);
    if ($raw === '') {
      return '';
    }
    return '<div class="mmh_booking_when"><div class="mmh_booking_when_text"><strong>' . mmh_h($raw) . '</strong></div></div>';
  }
  return '<div class="mmh_booking_when">'
    . '<div class="mmh_booking_when_cal">'
    . '<span class="mmh_booking_when_month">' . mmh_h(date('M', $ts)) . '</span>'
    . '<span class="mmh_booking_when_day">' . mmh_h(date('j', $ts)) . '</span>'
    . '<span class="mmh_booking_when_year">' . mmh_h(date('Y', $ts)) . '</span>'
    . '</div>'
    . '<div class="mmh_booking_when_text">'
    . '<strong>' . mmh_h(date('l', $ts)) . '</strong>'
    . '<span>' . mmh_h(date('F j, Y', $ts)) . '</span>'
    . '<em>' . mmh_h(date('g:i A', $ts)) . '</em>'
    . '</div>'
    . '</div>';
}

require_once __DIR__ . '/mail.php';
