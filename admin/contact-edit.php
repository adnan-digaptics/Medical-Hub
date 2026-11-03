<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
$id = (int) ($_GET['id'] ?? 0);
if ($id < 1) {
  mmh_flash('error', 'Message not found.');
  mmh_redirect('contacts.php');
}
$stmt = mmh_db()->prepare('SELECT * FROM contacts WHERE id = :id LIMIT 1');
$stmt->execute(array('id' => $id));
$row = $stmt->fetch();
if (!$row) {
  mmh_flash('error', 'Message not found.');
  mmh_redirect('contacts.php');
}
$pageTitle = 'Edit message';
$statuses = array('new', 'reviewed', 'closed');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!mmh_csrf_check()) {
    mmh_flash('error', 'Please try again.');
    mmh_redirect('contact-edit.php?id=' . $id);
  }
  $name = trim((string) ($_POST['name'] ?? ''));
  $email = trim((string) ($_POST['email'] ?? ''));
  $phone = trim((string) ($_POST['phone'] ?? ''));
  $subject = trim((string) ($_POST['subject'] ?? ''));
  $message = trim((string) ($_POST['message'] ?? ''));
  $adminNotes = trim((string) ($_POST['admin_notes'] ?? ''));
  $status = (string) ($_POST['status'] ?? 'new');
  if (!in_array($status, $statuses, true)) {
    $status = 'new';
  }
  if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
    mmh_flash('error', 'Name, a valid email, and the message are required.');
    mmh_redirect('contact-edit.php?id=' . $id);
  }
  $upd = mmh_db()->prepare('UPDATE contacts SET name=:name, email=:email, phone=:phone, subject=:subject, message=:message, admin_notes=:admin_notes, status=:status, updated_at=NOW() WHERE id=:id');
  $upd->execute(array(
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'subject' => $subject,
    'message' => $message,
    'admin_notes' => $adminNotes,
    'status' => $status,
    'id' => $id,
  ));
  $row = array_merge($row, array(
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'subject' => $subject,
    'message' => $message,
    'admin_notes' => $adminNotes,
    'status' => $status,
  ));
  if (!empty($_POST['send_email'])) {
    $sent = mmh_email_contact($row, 'update');
    mmh_flash('ok', $sent ? 'Message saved and email sent.' : 'Message saved, but email could not be sent. Check Settings / mail log.');
  } else {
    mmh_flash('ok', 'Message saved.');
  }
  mmh_redirect('contact-edit.php?id=' . $id);
}
include __DIR__ . '/_header.php';
?>
  <form class="mmh-form mmh-card" method="post">
    <?php mmh_csrf_field(); ?>
    <div class="row">
      <label>Name</label>
      <input type="text" name="name" required value="<?php echo mmh_h($row['name']); ?>">
    </div>
    <div class="row">
      <label>Email</label>
      <input type="email" name="email" required value="<?php echo mmh_h($row['email']); ?>">
    </div>
    <div class="row">
      <label>Phone</label>
      <input type="text" name="phone" value="<?php echo mmh_h($row['phone']); ?>">
    </div>
    <div class="row">
      <label>Subject</label>
      <input type="text" name="subject" value="<?php echo mmh_h($row['subject']); ?>">
    </div>
    <div class="row">
      <label>Message</label>
      <textarea name="message" rows="6" required><?php echo mmh_h($row['message']); ?></textarea>
    </div>
    <div class="row">
      <label>Team note / reply</label>
      <textarea name="admin_notes" rows="4"><?php echo mmh_h($row['admin_notes'] ?? ''); ?></textarea>
    </div>
    <div class="row">
      <label>Status</label>
      <select name="status">
        <?php foreach ($statuses as $status) : ?>
        <option value="<?php echo $status; ?>"<?php echo $row['status'] === $status ? ' selected' : ''; ?>><?php echo $status; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="row">
      <label><input type="checkbox" name="send_email" value="1"> Email this update to the visitor (and notification inbox)</label>
    </div>
    <p class="mmh-muted">Received <?php echo mmh_h(date('M j, Y g:i a', strtotime($row['created_at']))); ?></p>
    <button class="mmh-btn" type="submit">Save message</button>
    <a class="mmh-btn-ghost" href="contacts.php">Back</a>
  </form>
<?php include __DIR__ . '/_footer.php'; ?>
