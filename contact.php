<?php
  require_once __DIR__ . '/includes/db.php';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mmh_guard_public_post('contact', 'contact', 'Your message was sent. Thank you for contacting MidWest Medical Hub.');
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $message !== '') {
      $stmt = mmh_db()->prepare('INSERT INTO contacts (name, email, phone, subject, message, created_at) VALUES (:name, :email, :phone, :subject, :message, NOW())');
      $stmt->execute(array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
      ));
      mmh_email_contact(array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'status' => 'new',
        'admin_notes' => '',
      ), 'new');
      mmh_flash('ok', 'Your message was sent. Thank you for contacting MidWest Medical Hub.');
      mmh_redirect('contact');
    }
    mmh_flash('error', 'Please enter your name, a valid email, and a message.');
  }
  $pageTitle = 'Contact | MidWest Medical Hub';
  $headerType = 'inner';
  $footerClass = 'mt-0';
  include __DIR__ . '/includes/header.php';
  $ok = mmh_flash('ok');
  $err = mmh_flash('error');
?>
  <!-- Start Page Heading -->
  <section class="cs_page_heading cs_bg_filed cs_center" data-src="assets/img/schedule_bg_1.jpg">
    <div class="container">
      <h1 class="cs_page_title">Contact Us</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item active">Contact Us</li>
      </ol>
    </div>
  </section>
  <!-- End Page Heading -->
  <!-- Start Contact Section -->
  <section class="cs_contact">
    <div class="cs_height_110 cs_height_lg_70"></div>
    <div class="container">
      <div class="row cs_gap_y_30">
        <div class="col-lg-6">
          <div class="cs_contact_thumbnail cs_pr_40">
            <div class="cs_teeth_shape">
              <img src="assets/img/icons/hero_shape_3.png" alt="Image" class="cs_spinner_img">
            </div>
            <div class="cs_contact_img">
              <img src="assets/img/contact_2.png" alt="Image">
            </div>
            <div class="cs_contact_bg_shape">
              <div class="cs_white_bg_shape"></div>
              <div class="cs_iconbox cs_style_4">
                <div class="cs_iconbox_icon cs_center"><img src="assets/img/icons/call_icon_1.png" alt="Icon"></div>
                <div class="cs_iconbox_right">
                  <h3 class="cs_iconbox_title">Need Help Scheduling?</h3>
                  <p class="cs_iconbox_subtitle mb-0">Appointments available by request</p>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="col-lg-6">
          <div class="cs_section_heading cs_style_1">
            <p class="cs_section_subtitle cs_accent_color">
              <span class="cs_shape_left"></span>CONTACT US
            </p>
            <h2 class="cs_section_title">Contact MidWest Medical Hub</h2>
          </div>
          <div class="cs_height_25 cs_height_lg_25"></div>
          <form class="cs_contact_form row cs_gap_y_30" method="post">
            <?php mmh_csrf_field(); ?>
            <?php mmh_honeypot_field(); ?>
            <?php if ($ok) : ?><div class="col-lg-12"><p style="color:#146c43"><?php echo mmh_h($ok); ?></p></div><?php endif; ?>
            <?php if ($err) : ?><div class="col-lg-12"><p style="color:#9b1c1c"><?php echo mmh_h($err); ?></p></div><?php endif; ?>
            <div class="col-md-6">
              <input type="text" name="name" class="cs_form_field" placeholder="Your name">
            </div>
            <div class="col-md-6">
              <input type="email" name="email" class="cs_form_field" placeholder="Your email">
            </div>
            <div class="col-md-6">
              <input type="text" name="subject" class="cs_form_field" placeholder="What can we help with?">
            </div>
            <div class="col-md-6">
              <input type="text" name="phone" class="cs_form_field" placeholder="Your phone">
            </div>
            <div class="col-lg-12">
              <textarea rows="5" name="message" class="cs_form_field" placeholder="Tell us briefly about the care or service you are looking for"></textarea>
            </div>
            <div class="col-lg-12">
              <button type="submit" class="cs_btn cs_style_1 cs_color_1">Send Request</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="cs_height_120 cs_height_lg_80"></div>
  </section>
  <!-- End Contact Section -->
  <!-- Start Location Map -->
  <div class="cs_location_map">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d190255.3385487463!2d-87.7319638!3d41.8339037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x880e2c3cd0f4cbed%3A0xafe0a6ad09c0c000!2sChicago%2C%20IL!5e0!3m2!1sen!2sus!4v1726000000000!5m2!1sen!2sus"></iframe>
  </div>
  <!-- End Location Map -->
  
<?php include __DIR__ . '/includes/footer.php'; ?>
