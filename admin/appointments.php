<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
$pageTitle = 'Appointments';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  if (!mmh_csrf_check()) {
    mmh_flash('error', 'Please try again.');
  } else {
    $stmt = mmh_db()->prepare('DELETE FROM appointments WHERE id = :id');
    $stmt->execute(array('id' => (int) $_POST['delete_id']));
    mmh_flash('ok', 'Appointment deleted.');
  }
  mmh_redirect('appointments.php');
}
$rows = mmh_db()->query('SELECT * FROM appointments ORDER BY id DESC')->fetchAll();
include __DIR__ . '/_header.php';
?>
  <div class="mmh-toolbar">
    <p>Open a request to edit every field and optionally send an email.</p>
  </div>
  <div class="mmh-card">
    <table>
      <tr>
        <th>Date</th>
        <th>Name</th>
        <th>Email</th>
        <th>Service</th>
        <th>Preferred</th>
        <th>Status</th>
        <th></th>
      </tr>
      <?php if (!$rows) : ?><tr><td colspan="7">No appointment requests yet.</td></tr><?php endif; ?>
      <?php foreach ($rows as $row) : ?>
      <tr>
        <td><?php echo mmh_h(date('M j, Y', strtotime($row['created_at']))); ?></td>
        <td>
          <strong><?php echo mmh_h($row['name']); ?></strong>
          <?php if ($row['address'] !== '') : ?><br><?php echo mmh_h($row['address']); ?><?php endif; ?>
        </td>
        <td><?php echo mmh_h($row['email']); ?></td>
        <td><?php echo mmh_h($row['service']); ?></td>
        <td><?php echo mmh_h(mmh_format_preferred($row['preferred_at'])); ?></td>
        <td><span class="mmh-status mmh-status--<?php echo preg_replace('/[^a-z]/', '', strtolower($row['status'])); ?>"><?php echo mmh_h($row['status']); ?></span></td>
        <td class="mmh-actions">
          <a class="mmh-btn-ghost" href="appointment-edit.php?id=<?php echo (int) $row['id']; ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Delete this appointment?');">
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
