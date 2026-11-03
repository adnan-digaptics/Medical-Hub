<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
$pageTitle = 'Messages';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  if (!mmh_csrf_check()) {
    mmh_flash('error', 'Please try again.');
  } else {
    $stmt = mmh_db()->prepare('DELETE FROM contacts WHERE id = :id');
    $stmt->execute(array('id' => (int) $_POST['delete_id']));
    mmh_flash('ok', 'Message deleted.');
  }
  mmh_redirect('contacts.php');
}
$rows = mmh_db()->query('SELECT * FROM contacts ORDER BY id DESC')->fetchAll();
include __DIR__ . '/_header.php';
?>
  <div class="mmh-toolbar">
    <p>Open a message to edit every field, add a team note, and email the visitor.</p>
  </div>
  <div class="mmh-card">
    <table>
      <tr>
        <th>Date</th>
        <th>From</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Status</th>
        <th></th>
      </tr>
      <?php if (!$rows) : ?><tr><td colspan="6">No messages yet.</td></tr><?php endif; ?>
      <?php foreach ($rows as $row) : ?>
      <tr>
        <td><?php echo mmh_h(date('M j, Y', strtotime($row['created_at']))); ?></td>
        <td>
          <strong><?php echo mmh_h($row['name']); ?></strong><br>
          <?php echo mmh_h($row['email']); ?>
          <?php if ($row['phone'] !== '') : ?><br><?php echo mmh_h($row['phone']); ?><?php endif; ?>
        </td>
        <td><?php echo mmh_h($row['subject']); ?></td>
        <td><?php
          $preview = trim((string) $row['message']);
          if (strlen($preview) > 140) {
            $preview = substr($preview, 0, 137) . '...';
          }
          echo nl2br(mmh_h($preview));
        ?></td>
        <td><span class="mmh-status mmh-status--<?php echo preg_replace('/[^a-z]/', '', strtolower($row['status'])); ?>"><?php echo mmh_h($row['status']); ?></span></td>
        <td class="mmh-actions">
          <a class="mmh-btn-ghost" href="contact-edit.php?id=<?php echo (int) $row['id']; ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Delete this message?');">
            <?php mmh_csrf_field(); ?>
            <input type="hidden" name="delete_id" value="<?php echo (int) $row['id']; ?>">
            <button class="mmh-btn-danger" type="submit">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
<?php include __DIR__ . '/_footer.php'; ?>
