<?php
if (!isset($footerClass)) {
  $footerClass = '';
}
$footerExtra = trim('cs_footer cs_blue_bg cs_bg_filed cs_white_color ' . $footerClass);
?>
  <footer class="<?php echo htmlspecialchars($footerExtra, ENT_QUOTES, 'UTF-8'); ?>" data-src="assets/img/footer_bg.jpg">
    <div class="container">
      <div class="cs_footer_row">
        <div class="cs_footer_col">
          <div class="cs_footer_highlight_col cs_accent_bg<?php echo strpos($footerClass, 'mt-0') !== false ? ' mt-0' : ''; ?>">
            <div class="cs_footer_logo">
              <img src="assets/img/footer_logo.svg" alt="MidWest Medical Hub">
            </div>
            <ul class="cs_footer_contact cs_mp_0">
              <li>
                <i class="fa-regular fa-clock"></i>
                Office Hours: <br>
                <?php echo nl2br(mmh_h(mmh_setting('office_hours', 'Mon - Fri: 8.00 am. - 6.00 pm.'))); ?>
              </li>
            </ul>
            <div class="cs_social_btns cs_style_1">
              <a href="#" class="cs_center"><i class="fa-brands fa-facebook-f"></i></a>
              <a href="#" class="cs_center"><i class="fa-brands fa-pinterest-p"></i></a>
              <a href="#" class="cs_center"><i class="fa-brands fa-twitter"></i></a>
              <a href="#" class="cs_center"><i class="fa-brands fa-instagram"></i></a>
            </div>
          </div>
        </div>
        <div class="cs_footer_col">
          <div class="cs_footer_widget">
            <h2 class="cs_footer_widget_title">Services</h2>
            <ul class="cs_footer_widget_nav_list cs_mp_0">
              <li><a href="primary-care">Primary Care</a></li>
              <li><a href="geriatric-care">Geriatric Care</a></li>
              <li><a href="womens-health">Women’s Health</a></li>
              <li><a href="adult-day-care">Adult Day Care</a></li>
              <li><a href="home-care">Home Care</a></li>
              <li><a href="home-health">Home Health Care</a></li>
            </ul>
          </div>
        </div>
        <div class="cs_footer_col">
          <div class="cs_footer_widget">
            <h2 class="cs_footer_widget_title">Quick Links</h2>
            <ul class="cs_footer_widget_nav_list cs_mp_0">
              <li><a href="about">About Us</a></li>
              <li><a href="doctors">Care Team</a></li>
              <li><a href="appointments">Appointments</a></li>
              <li><a href="blog">Resources</a></li>
              <li><a href="contact">Contact</a></li>
            </ul>
          </div>
        </div>
        <div class="cs_footer_col">
          <div class="cs_footer_widget">
            <h2 class="cs_footer_widget_title">Recent Posts</h2>
            <ul class="cs_recent_post_list cs_mp_0">
              <?php foreach (mmh_published_posts(2) as $recentPost) : ?>
              <li>
                <div class="cs_recent_post">
                  <a href="<?php echo mmh_h(mmh_post_url($recentPost)); ?>" class="cs_recent_post_thumb">
                    <img src="<?php echo mmh_h(mmh_post_image($recentPost)); ?>" alt="<?php echo mmh_h($recentPost['title']); ?>">
                  </a>
                  <div class="cs_recent_post_right">
                    <p><?php echo mmh_h(date('d M Y', strtotime($recentPost['published_at'] ?: $recentPost['created_at']))); ?></p>
                    <h3 class="cs_recent_post_title"><a href="<?php echo mmh_h(mmh_post_url($recentPost)); ?>"><?php echo mmh_h($recentPost['title']); ?></a></h3>
                  </div>
                </div>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="cs_footer_bottom cs_primary_bg">
      <div class="container">
        <div class="cs_footer_bottom_in">
          <p class="cs_footer_copyright mb-0">Copyright © 2026 MidWest Medical Hub. All Rights Reserved.</p>
          <ul class="cs_footer_menu cs_mp_0">
            <li><a href="about">About Us</a></li>
            <li><a href="blog">News</a></li>
            <li><a href="service">Service</a></li>
            <li><a href="contact">Contact</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
  <span class="cs_scrollup">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0 10L1.7625 11.7625L8.75 4.7875V20H11.25V4.7875L18.225 11.775L20 10L10 0L0 10Z" fill="currentColor" />
    </svg>
  </span>
  <script src="assets/js/jquery-3.6.0.min.js"></script>
  <script src="assets/js/wow.min.js"></script>
  <script src="assets/js/jquery.slick.min.js"></script>
  <script src="assets/js/odometer.js"></script>
  <script src="assets/js/main.js"></script>
  <?php if (!empty($pageScripts) && is_array($pageScripts)) : foreach ($pageScripts as $scriptSrc) : ?>
  <script src="<?php echo mmh_h($scriptSrc); ?>"></script>
  <?php endforeach; endif; ?>
</body>
</html>
