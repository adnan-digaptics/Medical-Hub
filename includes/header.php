<?php
if (!isset($pageTitle)) {
  $pageTitle = 'MidWest Medical Hub | Primary Care, Senior Care & Home Health';
}
if (!isset($pageDescription)) {
  $pageDescription = 'MidWest Medical Hub provides primary care, geriatric care, women\'s health, adult day care, home care and home health services through a coordinated care model.';
}
if (!isset($headerType)) {
  $headerType = 'inner';
}
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
if (!isset($mmhServices)) {
  include __DIR__ . '/services-data.php';
}
if (!function_exists('mmh_db')) {
  require_once __DIR__ . '/db.php';
}
$servicePages = array_merge(array('service', 'service-details'), mmh_service_slugs());

function mmh_nav_active($pages) {
  global $currentPage;
  return in_array($currentPage, (array) $pages, true) ? ' class="current-menu-item"' : '';
}

$headerClass = 'cs_site_header cs_style_1 cs_primary_color cs_sticky_header';
if ($headerType === 'home-v3') {
  $headerClass .= ' cs_type_1';
} else {
  $headerClass .= ' cs_white_bg';
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="author" content="MidWest Medical Hub">
  <meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="icon" href="assets/img/favicon.png">
  <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/fontawesome.min.css">
  <link rel="stylesheet" href="assets/css/animate.css">
  <link rel="stylesheet" href="assets/css/odometer.css">
  <link rel="stylesheet" href="assets/css/slick.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/midwest-medical-hub.css?v=20260916s">
</head>
<body>
  <div class="cs_preloader">
    <div class="cs_preloader_in">
      <div class="cs_wave_first">
        <svg enable-background="new 0 0 300.08 300.08" viewBox="0 0 300.08 300.08" xmlns="http://www.w3.org/2000/svg"><path d="m293.26 184.14h-82.877l-12.692-76.138c-.546-3.287-3.396-5.701-6.718-5.701-.034 0-.061 0-.089 0-3.369.027-6.199 2.523-6.677 5.845l-12.507 87.602-14.874-148.69c-.355-3.43-3.205-6.056-6.643-6.138-.048 0-.096 0-.143 0-3.39 0-6.274 2.489-6.752 5.852l-19.621 137.368h-9.405l-12.221-42.782c-.866-3.028-3.812-5.149-6.8-4.944-3.13.109-5.777 2.332-6.431 5.395l-8.941 42.332h-73.049c-3.771 0-6.82 3.049-6.82 6.82 0 3.778 3.049 6.82 6.82 6.82h78.566c3.219 0 6.002-2.251 6.67-5.408l4.406-20.856 6.09 21.313c.839 2.939 3.526 4.951 6.568 4.951h20.46c3.396 0 6.274-2.489 6.752-5.845l12.508-87.596 14.874 148.683c.355 3.437 3.205 6.056 6.643 6.138h.143c3.39 0 6.274-2.489 6.752-5.845l14.227-99.599 6.397 38.362c.546 3.287 3.396 5.702 6.725 5.702h88.66c3.771 0 6.82-3.049 6.82-6.82-.001-3.772-3.05-6.821-6.821-6.821z"></path></svg>
      </div>
      <div class="cs_wave_second">
        <svg enable-background="new 0 0 300.08 300.08" viewBox="0 0 300.08 300.08" xmlns="http://www.w3.org/2000/svg"><path d="m293.26 184.14h-82.877l-12.692-76.138c-.546-3.287-3.396-5.701-6.718-5.701-.034 0-.061 0-.089 0-3.369.027-6.199 2.523-6.677 5.845l-12.507 87.602-14.874-148.69c-.355-3.43-3.205-6.056-6.643-6.138-.048 0-.096 0-.143 0-3.39 0-6.274 2.489-6.752 5.852l-19.621 137.368h-9.405l-12.221-42.782c-.866-3.028-3.812-5.149-6.8-4.944-3.13.109-5.777 2.332-6.431 5.395l-8.941 42.332h-73.049c-3.771 0-6.82 3.049-6.82 6.82 0 3.778 3.049 6.82 6.82 6.82h78.566c3.219 0 6.002-2.251 6.67-5.408l4.406-20.856 6.09 21.313c.839 2.939 3.526 4.951 6.568 4.951h20.46c3.396 0 6.274-2.489 6.752-5.845l12.508-87.596 14.874 148.683c.355 3.437 3.205 6.056 6.643 6.138h.143c3.39 0 6.274-2.489 6.752-5.845l14.227-99.599 6.397 38.362c.546 3.287 3.396 5.702 6.725 5.702h88.66c3.771 0 6.82-3.049 6.82-6.82-.001-3.772-3.05-6.821-6.821-6.821z"></path></svg>
      </div>
    </div>
  </div>
  <header class="<?php echo $headerClass; ?>">
    <?php if ($headerType === 'home') : ?>
    <div class="cs_top_header cs_blue_bg cs_white_color">
      <div class="container">
        <div class="cs_top_header_in">
          <div class="cs_top_header_left">
            <ul class="cs_header_contact_list cs_mp_0">
              <li>
                <i class="fa-solid fa-envelope"></i>
                <a href="contact">Contact our care team</a>
              </li>
              <li>
                <i class="fa-solid fa-location-dot"></i>
                Serving Midwest communities
              </li>
            </ul>
          </div>
          <div class="cs_top_header_right">
            <div class="cs_social_btns cs_style_1">
              <a href="#" class="cs_center"><i class="fa-brands fa-facebook-f"></i></a>
              <a href="#" class="cs_center"><i class="fa-brands fa-pinterest-p"></i></a>
              <a href="#" class="cs_center"><i class="fa-brands fa-twitter"></i></a>
              <a href="#" class="cs_center"><i class="fa-brands fa-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <div class="cs_main_header">
      <div class="container">
        <div class="cs_main_header_in">
          <div class="cs_main_header_left">
            <a class="cs_site_branding" href="./">
              <img src="assets/img/logo.svg" alt="MidWest Medical Hub">
            </a>
          </div>
          <div class="cs_main_header_right">
            <div class="cs_nav cs_primary_color">
              <ul class="cs_nav_list">
                <li<?php echo mmh_nav_active(array('index', 'home-v2', 'home-v3')); ?>><a href="./">Home</a></li>
                <li<?php echo mmh_nav_active('about'); ?>><a href="about">About</a></li>
                <li class="menu-item-has-children<?php echo in_array($currentPage, $servicePages, true) ? ' current-menu-item' : ''; ?>">
                  <a href="service">Services</a>
                  <ul>
                    <?php foreach ($mmhServices as $item) : ?>
                    <li><a href="<?php echo htmlspecialchars($item['file'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <?php endforeach; ?>
                  </ul>
                </li>
                <li<?php echo mmh_nav_active(array('doctors', 'doctor-details')); ?>><a href="doctors">Care Team</a></li>
                <li<?php echo mmh_nav_active(array('blog', 'blog-details')); ?>><a href="blog">Resources</a></li>
                <li<?php echo mmh_nav_active(array('contact', 'appointments')); ?>><a href="contact">Contact</a></li>
              </ul>
            </div>
            <div class="cs_search_wrap">
              <div class="cs_search_toggle cs_center">
                <i class="fa-solid fa-magnifying-glass"></i>
              </div>
              <form action="blog" method="get" class="cs_header_search_form">
                <div class="cs_header_search_form_in">
                  <input type="text" name="q" placeholder="Search" class="cs_header_search_field">
                  <button type="submit" class="cs_header_submit_btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                  </button>
                </div>
              </form>
            </div>
            <a href="appointments" class="cs_btn cs_style_1 cs_color_1">
              <span>Book an Appointment</span>
              <i class="fa-solid fa-angles-right"></i>
            </a>
            <a href="appointments" class="cs_header_mobile_cta cs_center" aria-label="Book an Appointment">
              <i class="fa-solid fa-calendar-check"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php if ($headerType === 'home-v3') : ?>
    <div class="cs_main_header_shape">
      <svg width="1679" height="112" viewBox="0 0 1679 112" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 0L1679 0.014C1679 0.014 1639 23.128 1639 48.261V111.014H40V47.351C40 22.567 0 0 0 0Z" fill="#2EA6F7"/>
        <path d="M10 0L1669 0.014C1669 0.014 1629 23.128 1629 48.261V111.014H50V47.351C50 22.567 10 0 10 0Z" fill="white"/>
      </svg>
    </div>
    <?php endif; ?>
  </header>
  <?php if ($headerType === 'home') : ?>
  <div class="cs_site_header_spacing_150"></div>
  <?php endif; ?>
