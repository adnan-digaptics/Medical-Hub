<?php
require_once dirname(__DIR__) . '/includes/auth.php';
if (mmh_admin_user()) {
  mmh_redirect('index.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!mmh_rate_limit('admin-login', 5, 900)) {
    $error = 'Too many sign-in attempts. Please wait and try again.';
  } elseif (!mmh_csrf_check()) {
    $error = 'Please try again.';
  } else {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $stmt = mmh_db()->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(array('username' => $username));
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
      session_regenerate_id(true);
      $_SESSION['admin_user'] = array(
        'id' => $user['id'],
        'username' => $user['username'],
        'name' => $user['name'],
      );
      mmh_redirect('index.php');
    }
    usleep(400000);
    $error = 'Invalid username or password.';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login | MidWest Medical Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/admin.css?v=20260916f">
</head>
<body class="mmh-login-body">
  <div class="mmh-login-shell">
    <section class="mmh-login-visual" style="background-image:url('../assets/img/unsplash_connected_care.jpg')">
      <strong>Care that stays connected.</strong>
      <p>Publish resources, review appointment requests, and keep family messages in one calm console.</p>
    </section>
    <div class="mmh-login">
      <p class="mmh-kicker">MidWest Medical Hub</p>
      <h1>Welcome back</h1>
      <p>Sign in to the care console.</p>
      <?php if ($error) : ?><div class="mmh-alert mmh-alert--err"><?php echo mmh_h($error); ?></div><?php endif; ?>
      <form method="post" class="mmh-form">
        <?php mmh_csrf_field(); ?>
        <div class="row">
          <label>Username</label>
          <input type="text" name="username" required autocomplete="username">
        </div>
        <div class="row">
          <label>Password</label>
          <input type="password" name="password" required autocomplete="current-password">
        </div>
        <button class="mmh-btn" type="submit">Sign in</button>
      </form>
      <p class="mmh-login-note">Use the admin username and password provided by the site owner.</p>
    </div>
  </div>
</body>
</html>
