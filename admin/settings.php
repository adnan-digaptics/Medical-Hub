<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
$pageTitle = 'Settings';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!mmh_csrf_check()) {
    mmh_flash('error', 'Please try again.');
  } else {
    $hours = trim((string) ($_POST['office_hours'] ?? ''));
    $notify = trim((string) ($_POST['notify_email'] ?? ''));
    $fromEmail = trim((string) ($_POST['from_email'] ?? ''));
    $fromName = trim((string) ($_POST['from_name'] ?? 'MidWest Medical Hub'));
    $smtpHost = trim((string) ($_POST['smtp_host'] ?? ''));
    $smtpPort = trim((string) ($_POST['smtp_port'] ?? '587'));
    $smtpUser = trim((string) ($_POST['smtp_user'] ?? ''));
    $smtpPass = (string) ($_POST['smtp_pass'] ?? '');
    $smtpSecure = strtolower(trim((string) ($_POST['smtp_secure'] ?? 'tls')));
    if (!in_array($smtpSecure, array('tls', 'ssl', 'none'), true)) {
      $smtpSecure = 'tls';
    }
    if ($notify !== '' && !filter_var($notify, FILTER_VALIDATE_EMAIL)) {
      mmh_flash('error', 'Notification email is not valid.');
      mmh_redirect('settings.php');
    }
    if ($fromEmail !== '' && !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
      mmh_flash('error', 'From email is not valid.');
      mmh_redirect('settings.php');
    }
    $stmt = mmh_db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $pairs = array(
      'office_hours' => $hours,
      'notify_email' => $notify,
      'from_email' => $fromEmail,
      'from_name' => $fromName !== '' ? $fromName : 'MidWest Medical Hub',
      'smtp_host' => $smtpHost,
      'smtp_port' => $smtpPort !== '' ? $smtpPort : '587',
      'smtp_user' => $smtpUser,
      'smtp_secure' => $smtpSecure,
    );
    if ($smtpPass !== '') {
      $pairs['smtp_pass'] = $smtpPass;
    }
    foreach ($pairs as $key => $value) {
      $stmt->execute(array('k' => $key, 'v' => $value));
    }
    $newPass = (string) ($_POST['new_password'] ?? '');
    if ($newPass !== '') {
      if (strlen($newPass) < 8) {
        mmh_flash('error', 'New password must be at least 8 characters.');
        mmh_redirect('settings.php');
      }
      $hash = password_hash($newPass, PASSWORD_DEFAULT);
      $user = mmh_admin_user();
      $upd = mmh_db()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
      $upd->execute(array('password_hash' => $hash, 'id' => $user['id']));
    }
    mmh_flash('ok', 'Settings saved.');
  }
  mmh_redirect('settings.php');
}
include __DIR__ . '/_header.php';
?>
  <form class="mmh-form mmh-card" method="post">
    <?php mmh_csrf_field(); ?>
    <div class="row">
      <label>Office hours</label>
      <input type="text" name="office_hours" value="<?php echo mmh_h(mmh_setting('office_hours', 'Mon - Fri: 8.00 am. - 6.00 pm.')); ?>">
    </div>
    <div class="row">
      <label>Notification email (receives appointments and contact messages)</label>
      <input type="email" name="notify_email" value="<?php echo mmh_h(mmh_setting('notify_email')); ?>" placeholder="care@example.com">
    </div>
    <div class="row">
      <label>From name</label>
      <input type="text" name="from_name" value="<?php echo mmh_h(mmh_setting('from_name', 'MidWest Medical Hub')); ?>">
    </div>
    <div class="row">
      <label>From email</label>
      <input type="email" name="from_email" value="<?php echo mmh_h(mmh_setting('from_email')); ?>" placeholder="noreply@example.com">
    </div>
    <div class="row">
      <label>SMTP host (leave blank to use PHP mail)</label>
      <input type="text" name="smtp_host" value="<?php echo mmh_h(mmh_setting('smtp_host')); ?>" placeholder="smtp.gmail.com">
    </div>
    <div class="row">
      <label>SMTP port</label>
      <input type="text" name="smtp_port" value="<?php echo mmh_h(mmh_setting('smtp_port', '587')); ?>">
    </div>
    <div class="row">
      <label>SMTP username</label>
      <input type="text" name="smtp_user" value="<?php echo mmh_h(mmh_setting('smtp_user')); ?>" autocomplete="off">
    </div>
    <div class="row">
      <label>SMTP password (leave blank to keep current)</label>
      <input type="password" name="smtp_pass" autocomplete="new-password">
    </div>
    <div class="row">
      <label>SMTP encryption</label>
      <select name="smtp_secure">
        <?php $secure = mmh_setting('smtp_secure', 'tls'); ?>
        <option value="tls"<?php echo $secure === 'tls' ? ' selected' : ''; ?>>TLS (port 587)</option>
        <option value="ssl"<?php echo $secure === 'ssl' ? ' selected' : ''; ?>>SSL (port 465)</option>
        <option value="none"<?php echo $secure === 'none' ? ' selected' : ''; ?>>None</option>
      </select>
    </div>
    <div class="row">
      <label>New admin password (leave blank to keep current)</label>
      <input type="password" name="new_password" autocomplete="new-password">
    </div>
    <button class="mmh-btn" type="submit">Save settings</button>
  </form>
<?php include __DIR__ . '/_footer.php'; ?>
