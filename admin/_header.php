<?php
require_once dirname(__DIR__) . '/includes/auth.php';
$admin = mmh_admin_user();
$current = basename($_SERVER['PHP_SELF']);
function mmh_admin_nav($file, $current) {
  return $current === $file ? ' class="active"' : '';
}
function mmh_admin_icon($name) {
  $icons = array(
    'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
    'blogs' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h5"/>',
    'categories' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><circle cx="7" cy="7" r="1.4" fill="currentColor" stroke="none"/>',
    'appointments' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>',
    'messages' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
    'settings' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v2.2M12 19.8V22M4.9 4.9l1.6 1.6M17.5 17.5l1.6 1.6M2 12h2.2M19.8 12H22M4.9 19.1l1.6-1.6M17.5 6.5l1.6-1.6"/>',
    'site' => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
    'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
    'published' => '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/>',
  );
  $path = $icons[$name] ?? $icons['dashboard'];
  return '<span class="mmh-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg></span>';
}
$adminName = (string) ($admin['name'] ?? 'Admin');
$parts = preg_split('/\s+/', trim($adminName));
$initials = strtoupper(substr($parts[0] ?? 'A', 0, 1) . substr($parts[1] ?? '', 0, 1));
if ($initials === '') {
  $initials = 'A';
}
$intros = array(
  'index.php' => 'A live view of content, appointment requests, and family messages.',
  'posts.php' => 'These stories appear on Resources, the home page, and the footer.',
  'post-edit.php' => 'Write and format the full post the same way visitors will read it.',
  'categories.php' => 'Add labels that organize Resources and the blog editor.',
  'appointments.php' => 'Review preferred dates, follow up, and keep each request moving.',
  'appointment-edit.php' => 'Update the booking details and send an email if needed.',
  'contacts.php' => 'Family and patient messages from the public contact forms.',
  'contact-edit.php' => 'Edit the inquiry and send a follow-up email.',
  'settings.php' => 'Office hours, notification inbox, and outgoing mail.',
);
$pageIntro = $pageIntro ?? ($intros[$current] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo mmh_h($pageTitle ?? 'Admin'); ?> | MidWest Medical Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/admin.css?v=20260916h">
</head>
<body>
  <div class="mmh-admin">
    <aside class="mmh-admin__nav">
      <a class="mmh-admin__brand" href="index.php">
        <span class="mmh-brand-mark" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        </span>
        <span>
          <strong>MidWest</strong>
          <em>Care console</em>
        </span>
      </a>
      <p class="mmh-nav-label">Workspace</p>
      <nav>
        <a href="index.php"<?php echo mmh_admin_nav('index.php', $current); ?>><?php echo mmh_admin_icon('dashboard'); ?>Dashboard</a>
        <a href="posts.php"<?php echo ($current === 'posts.php' || $current === 'post-edit.php') ? ' class="active"' : ''; ?>><?php echo mmh_admin_icon('blogs'); ?>Blogs</a>
        <a href="categories.php"<?php echo mmh_admin_nav('categories.php', $current); ?>><?php echo mmh_admin_icon('categories'); ?>Categories</a>
        <a href="appointments.php"<?php echo ($current === 'appointments.php' || $current === 'appointment-edit.php') ? ' class="active"' : ''; ?>><?php echo mmh_admin_icon('appointments'); ?>Appointments</a>
        <a href="contacts.php"<?php echo ($current === 'contacts.php' || $current === 'contact-edit.php') ? ' class="active"' : ''; ?>><?php echo mmh_admin_icon('messages'); ?>Messages</a>
        <a href="settings.php"<?php echo mmh_admin_nav('settings.php', $current); ?>><?php echo mmh_admin_icon('settings'); ?>Settings</a>
      </nav>
      <p class="mmh-nav-label">Account</p>
      <nav>
        <a href="../" target="_blank" rel="noopener"><?php echo mmh_admin_icon('site'); ?>View site</a>
        <a href="logout.php"><?php echo mmh_admin_icon('logout'); ?>Logout</a>
      </nav>
      <div class="mmh-admin__user">
        <span class="mmh-avatar"><?php echo mmh_h($initials); ?></span>
        <span class="mmh-admin__user-copy">
          <strong><?php echo mmh_h($adminName); ?></strong>
          <span>Administrator</span>
        </span>
      </div>
    </aside>
    <main class="mmh-admin__main">
      <div class="mmh-admin__top">
        <div>
          <p class="mmh-kicker">MidWest Medical Hub</p>
          <h1><?php echo mmh_h($pageTitle ?? 'Dashboard'); ?></h1>
          <?php if ($pageIntro !== '') : ?><p class="mmh-admin__intro"><?php echo mmh_h($pageIntro); ?></p><?php endif; ?>
        </div>
        <p class="mmh-admin__date"><?php echo mmh_h(date('l, F j')); ?></p>
      </div>
      <?php $ok = mmh_flash('ok'); $err = mmh_flash('error'); ?>
      <?php if ($ok) : ?><div class="mmh-alert mmh-alert--ok"><?php echo mmh_h($ok); ?></div><?php endif; ?>
      <?php if ($err) : ?><div class="mmh-alert mmh-alert--err"><?php echo mmh_h($err); ?></div><?php endif; ?>
