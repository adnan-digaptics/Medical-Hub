<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
$pageTitle = 'Dashboard';
$db = mmh_db();
$stats = array(
  'posts' => (int) $db->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
  'published' => (int) $db->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn(),
  'appointments' => (int) $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'new'")->fetchColumn(),
  'messages' => (int) $db->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'")->fetchColumn(),
);
$latestPosts = $db->query('SELECT id, title, status, published_at FROM posts ORDER BY id DESC LIMIT 5')->fetchAll();
$latestAppointments = $db->query('SELECT id, name, service, status, created_at FROM appointments ORDER BY id DESC LIMIT 5')->fetchAll();
include __DIR__ . '/_header.php';
?>
  <div class="mmh-grid">
    <div class="mmh-stat">
      <span class="mmh-stat__icon"><?php echo mmh_admin_icon('blogs'); ?></span>
      <span class="mmh-stat__label">All blogs</span>
      <strong><?php echo $stats['posts']; ?></strong>
      <em>In the resource library</em>
    </div>
    <div class="mmh-stat">
      <span class="mmh-stat__icon"><?php echo mmh_admin_icon('published'); ?></span>
      <span class="mmh-stat__label">Published</span>
      <strong><?php echo $stats['published']; ?></strong>
      <em>Live on the public site</em>
    </div>
    <div class="mmh-stat mmh-stat--appointments">
      <span class="mmh-stat__icon"><?php echo mmh_admin_icon('appointments'); ?></span>
      <span class="mmh-stat__label">New appointments</span>
      <strong><?php echo $stats['appointments']; ?></strong>
      <em>Waiting for follow-up</em>
    </div>
    <div class="mmh-stat mmh-stat--messages">
      <span class="mmh-stat__icon"><?php echo mmh_admin_icon('messages'); ?></span>
      <span class="mmh-stat__label">New messages</span>
      <strong><?php echo $stats['messages']; ?></strong>
      <em>From contact forms</em>
    </div>
  </div>
  <div class="mmh-dash-cols">
  <div class="mmh-card">
    <div class="mmh-toolbar">
      <h2>Latest blogs</h2>
      <a class="mmh-btn" href="post-edit.php">Add blog</a>
    </div>
    <table>
      <tr><th>Title</th><th>Status</th><th>Date</th></tr>
      <?php foreach ($latestPosts as $post) : ?>
      <tr>
        <td><a href="post-edit.php?id=<?php echo (int) $post['id']; ?>"><?php echo mmh_h($post['title']); ?></a></td>
        <td><span class="mmh-status mmh-status--<?php echo preg_replace('/[^a-z]/', '', strtolower($post['status'])); ?>"><?php echo mmh_h($post['status']); ?></span></td>
        <td><?php echo mmh_h($post['published_at'] ? date('M j, Y', strtotime($post['published_at'])) : ''); ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <div class="mmh-card">
    <div class="mmh-toolbar">
      <h2>Latest appointments</h2>
      <a class="mmh-btn-ghost" href="appointments.php">View all</a>
    </div>
    <table>
      <tr><th>Name</th><th>Service</th><th>Status</th></tr>
      <?php if (!$latestAppointments) : ?>
      <tr><td colspan="3">No appointment requests yet.</td></tr>
      <?php endif; ?>
      <?php foreach ($latestAppointments as $row) : ?>
      <tr>
        <td><a href="appointment-edit.php?id=<?php echo (int) $row['id']; ?>"><?php echo mmh_h($row['name']); ?></a></td>
        <td><?php echo mmh_h($row['service']); ?></td>
        <td><span class="mmh-status mmh-status--<?php echo preg_replace('/[^a-z]/', '', strtolower($row['status'])); ?>"><?php echo mmh_h($row['status']); ?></span></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
  </div>
<?php include __DIR__ . '/_footer.php'; ?>
