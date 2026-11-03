<?php
  require_once __DIR__ . '/includes/db.php';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mmh_guard_public_post('appointments', 'appointments', 'Your appointment request was sent. Our care team will follow up by email.');
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));
    $service = trim((string) ($_POST['service'] ?? ''));
    if (!isset($mmhServices)) {
      include __DIR__ . '/includes/services-data.php';
    }
    if (isset($mmhServices[$service])) {
      $service = $mmhServices[$service]['name'];
    }
    $preferred = mmh_combine_preferred($_POST['preferred_date'] ?? '', $_POST['preferred_time'] ?? '');
    $notes = trim((string) ($_POST['notes'] ?? ''));
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      mmh_flash('error', 'Please enter a valid name and email.');
    } elseif ($preferred === '') {
      mmh_flash('error', 'Please choose a preferred date and time from the calendar.');
    } else {
      $stmt = mmh_db()->prepare('INSERT INTO appointments (name, email, address, service, preferred_at, notes, created_at) VALUES (:name, :email, :address, :service, :preferred_at, :notes, NOW())');
      $stmt->execute(array(
        'name' => $name,
        'email' => $email,
        'address' => $address,
        'service' => $service,
        'preferred_at' => $preferred,
        'notes' => $notes,
      ));
      $row = array(
        'name' => $name,
        'email' => $email,
        'address' => $address,
        'service' => $service,
        'preferred_at' => $preferred,
        'notes' => $notes,
        'status' => 'new',
      );
      mmh_email_appointment($row, 'new');
      mmh_flash('ok', 'Your appointment request was sent. Our care team will follow up by email.');
      mmh_flash('booking', $preferred);
      mmh_redirect('appointments');
    }
  }
  $pageTitle = 'Appointments | MidWest Medical Hub';
  $headerType = 'inner';
  $pageScripts = array('assets/js/appointment-calendar.js?v=20260916c');
  include __DIR__ . '/includes/header.php';
  $ok = mmh_flash('ok');
  $err = mmh_flash('error');
  $booking = mmh_flash('booking');
?>
  <!-- Start Page Heading -->
  <section class="cs_page_heading cs_bg_filed cs_center" data-src="assets/img/process_bg_1.jpg">
    <div class="container">
      <h1 class="cs_page_title">Appointments</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item active">Appointments</li>
      </ol>
    </div>
  </section>
  <!-- End Page Heading -->
  <!-- Start Appointment Section -->
  <section class="cs_appointment">
    <div class="cs_height_110 cs_height_lg_70"></div>
    <div class="container">
      <div class="cs_appointment_form_wrapper">
        <div class="cs_section_heading cs_style_1 text-center">
          <p class="cs_section_subtitle cs_accent_color">
            <span class="cs_shape_left"></span>MAKE APPOINTMENTS<span class="cs_shape_right"></span>
          </p>
          <h2 class="cs_section_title">Request an Appointment</h2>
          <p class="mb-0">Choose the MidWest Medical Hub service that fits your needs. If you are unsure, pick Primary Care and we will help from there.</p>
        </div>
        <div class="cs_height_40 cs_height_lg_35"></div>
        <?php if ($ok) : ?>
        <div class="mmh_booking_confirm">
          <p><?php echo mmh_h($ok); ?></p>
          <?php if ($booking !== '') echo mmh_booking_when_html($booking); ?>
        </div>
        <?php endif; ?>
        <?php if ($err) : ?><p class="text-center mmh_booking_error"><?php echo mmh_h($err); ?></p><?php endif; ?>
        <form class="cs_appointment_form row cs_gap_y_30" method="post">
          <?php mmh_csrf_field(); ?>
          <?php mmh_honeypot_field(); ?>
          <div class="col-md-6">
            <input type="text" name="name" class="cs_form_field" placeholder="Name">
          </div>
          <div class="col-md-6">
            <input type="email" name="email" class="cs_form_field" placeholder="Email">
          </div>
          <div class="col-md-12">
            <input type="text" name="address" class="cs_form_field" placeholder="Address">
          </div>
          <div class="col-md-12">
            <select name="service" class="cs_form_field">
              <option value="">Choose a service</option>
              <?php foreach ($mmhServices as $slug => $item) : ?>
              <option value="<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-12">
            <label class="mmh_calendar_label">Preferred date & time</label>
            <div class="mmh_calendar" data-calendar>
              <div class="mmh_calendar_nav">
                <button type="button" class="mmh_calendar_nav_btn" data-cal-prev aria-label="Previous month">&lsaquo;</button>
                <strong data-cal-title>Calendar</strong>
                <button type="button" class="mmh_calendar_nav_btn" data-cal-next aria-label="Next month">&rsaquo;</button>
              </div>
              <div class="mmh_calendar_weekdays" aria-hidden="true">
                <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
              </div>
              <div class="mmh_calendar_days" data-cal-days></div>
              <p class="mmh_calendar_time_label">Preferred time</p>
              <div class="mmh_calendar_times" data-cal-times></div>
              <input type="hidden" name="preferred_date" value="">
              <input type="hidden" name="preferred_time" value="">
              <div class="mmh_calendar_preview" data-cal-preview></div>
            </div>
          </div>
          <div class="col-md-12">
            <textarea name="notes" rows="4" class="cs_form_field" placeholder="Anything we should know before the visit?"></textarea>
          </div>
          <div class="col-md-12">
            <button type="submit" class="cs_btn cs_style_1 cs_white_color">Request Appointment</button>
          </div>
        </form>
      </div>
    </div>
    <div class="cs_height_120 cs_height_lg_80"></div>
  </section>
  <!-- End Appointment Section -->
  
<?php include __DIR__ . '/includes/footer.php'; ?>
