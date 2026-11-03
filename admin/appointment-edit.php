<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
if (!isset($mmhServices)) {
  include dirname(__DIR__) . '/includes/services-data.php';
}
$id = (int) ($_GET['id'] ?? 0);
if ($id < 1) {
  mmh_flash('error', 'Appointment not found.');
  mmh_redirect('appointments.php');
}
$stmt = mmh_db()->prepare('SELECT * FROM appointments WHERE id = :id LIMIT 1');
$stmt->execute(array('id' => $id));
$row = $stmt->fetch();
if (!$row) {
  mmh_flash('error', 'Appointment not found.');
  mmh_redirect('appointments.php');
}
$pageTitle = 'Edit appointment';
$statuses = array('new', 'reviewed', 'scheduled', 'closed');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!mmh_csrf_check()) {
    mmh_flash('error', 'Please try again.');
    mmh_redirect('appointment-edit.php?id=' . $id);
  }
  $name = trim((string) ($_POST['name'] ?? ''));
  $email = trim((string) ($_POST['email'] ?? ''));
  $address = trim((string) ($_POST['address'] ?? ''));
  $service = trim((string) ($_POST['service'] ?? ''));
  $preferred = mmh_combine_preferred($_POST['preferred_date'] ?? '', $_POST['preferred_time'] ?? '');
  if ($preferred === '') {
    $preferred = trim((string) ($_POST['preferred_at'] ?? ''));
  }
  $notes = trim((string) ($_POST['notes'] ?? ''));
  $status = (string) ($_POST['status'] ?? 'new');
  if (!in_array($status, $statuses, true)) {
    $status = 'new';
  }
  if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    mmh_flash('error', 'Name and a valid email are required.');
    mmh_redirect('appointment-edit.php?id=' . $id);
  }
  $upd = mmh_db()->prepare('UPDATE appointments SET name=:name, email=:email, address=:address, service=:service, preferred_at=:preferred_at, notes=:notes, status=:status, updated_at=NOW() WHERE id=:id');
  $upd->execute(array(
    'name' => $name,
    'email' => $email,
    'address' => $address,
    'service' => $service,
    'preferred_at' => $preferred,
    'notes' => $notes,
    'status' => $status,
    'id' => $id,
  ));
  $row = array_merge($row, array(
    'name' => $name,
    'email' => $email,
    'address' => $address,
    'service' => $service,
    'preferred_at' => $preferred,
    'notes' => $notes,
    'status' => $status,
  ));
  if (!empty($_POST['send_email'])) {
    $sent = mmh_email_appointment($row, 'update');
    mmh_flash('ok', $sent ? 'Appointment saved and email sent.' : 'Appointment saved, but email could not be sent. Check Settings / mail log.');
  } else {
    mmh_flash('ok', 'Appointment saved.');
  }
  mmh_redirect('appointment-edit.php?id=' . $id);
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
      <label>Address</label>
      <input type="text" name="address" value="<?php echo mmh_h($row['address']); ?>">
    </div>
    <div class="row">
      <label>Service</label>
      <input type="text" name="service" list="mmh-services" value="<?php echo mmh_h($row['service']); ?>">
      <datalist id="mmh-services">
        <?php foreach ($mmhServices as $item) : ?>
        <option value="<?php echo mmh_h($item['name']); ?>"></option>
        <?php endforeach; ?>
      </datalist>
    </div>
    <div class="row">
      <label>Preferred date & time</label>
      <?php echo mmh_booking_when_html($row['preferred_at']); ?>
      <div class="mmh-datetime">
        <input type="date" name="preferred_date" value="<?php echo mmh_h(mmh_preferred_input_date($row['preferred_at'])); ?>">
        <input type="time" name="preferred_time" step="1800" min="08:00" max="18:00" value="<?php echo mmh_h(mmh_preferred_input_time($row['preferred_at'])); ?>">
      </div>
    </div>
    <div class="row">
      <label>Notes</label>
      <textarea name="notes" rows="5"><?php echo mmh_h($row['notes']); ?></textarea>
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
    <button class="mmh-btn" type="submit">Save appointment</button>
    <a class="mmh-btn-ghost" href="appointments.php">Back</a>
  </form>
<?php include __DIR__ . '/_footer.php'; ?>
