<?php
if (!isset($mmhServices)) {
  include __DIR__ . '/services-data.php';
}
if (!isset($serviceSlug) || !isset($mmhServices[$serviceSlug])) {
  $serviceSlug = 'primary-care';
}
$service = $mmhServices[$serviceSlug];
$pageTitle = $service['name'] . ' | MidWest Medical Hub';
$pageDescription = $service['summary'];
$headerType = 'inner';
include __DIR__ . '/header.php';
$h = function ($value) {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};
?>
  <section class="cs_page_heading cs_bg_filed cs_center" data-src="<?php echo $h($service['image']); ?>">
    <div class="container">
      <h1 class="cs_page_title"><?php echo $h($service['name']); ?></h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item"><a href="service">Services</a></li>
        <li class="breadcrumb-item active"><?php echo $h($service['name']); ?></li>
      </ol>
    </div>
  </section>
  <section class="mmh_service_page">
    <div class="cs_height_110 cs_height_lg_70"></div>
    <div class="container">
      <div class="row cs_gap_y_40 mmh_service_hero_row">
        <div class="col-xl-4 col-lg-5 order-2 order-lg-1">
          <div class="cs_solution_content_wrapper cs_gray_bg cs_type_1">
            <h3 class="cs_service_heading">All Services</h3>
            <ul class="cs_solution_links cs_style_2 cs_mp0">
              <?php foreach ($mmhServices as $slug => $item) : ?>
              <li<?php echo $slug === $serviceSlug ? ' class="active"' : ''; ?>>
                <a href="<?php echo $h($item['file']); ?>">
                  <span class="cs_tab_link_icon_left cs_center"><i class="fa-solid fa-check"></i></span>
                  <span><?php echo $h($item['name']); ?></span>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="mmh_service_side_note">
            <h3>Need help choosing?</h3>
            <p>Tell us what is going on at home or in clinic. We will help you start with the right MidWest Medical Hub service.</p>
            <a href="appointments" class="cs_btn cs_style_1 cs_color_1">
              <span>Request an Appointment</span>
              <i class="fa-solid fa-angles-right"></i>
            </a>
          </div>
        </div>
        <div class="col-xl-8 col-lg-7 order-1 order-lg-2">
          <div class="cs_service_details_thumbnail mmh_service_hero_image">
            <img src="<?php echo $h($service['image']); ?>" alt="<?php echo $h($service['name']); ?>">
          </div>
        </div>
      </div>
      <div class="cs_height_35 cs_height_lg_30"></div>
      <div class="cs_service_details">
        <p class="mmh_service_kicker">MidWest Medical Hub</p>
        <h2 class="cs_service_heading"><?php echo $h($service['name']); ?></h2>
        <p class="cs_service_subtitle"><?php echo $h($service['intro']); ?></p>
        <p class="cs_service_subtitle"><?php echo $h($service['body']); ?></p>
        <div class="row cs_gap_y_30 mmh_service_split">
          <div class="col-md-6">
            <h3 class="mmh_service_subhead">Who it is for</h3>
            <p><?php echo $h($service['audience']); ?></p>
          </div>
          <div class="col-md-6">
            <h3 class="mmh_service_subhead">How we help</h3>
            <p><?php echo $h($service['approach']); ?></p>
          </div>
        </div>
        <div class="cs_height_35 cs_height_lg_30"></div>
        <div class="row cs_gap_y_30">
          <div class="col-lg-6">
            <div class="cs_service_details_thumbnail">
              <img src="<?php echo $h($service['detailImage']); ?>" alt="<?php echo $h($service['name']); ?>">
            </div>
          </div>
          <div class="col-lg-6">
            <h3 class="mmh_service_subhead">What this service includes</h3>
            <ul class="cs_list cs_style_2 cs_mp_0">
              <?php foreach ($service['points'] as $point) : ?>
              <li><i class="fa-solid fa-circle-check"></i><?php echo $h($point); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="cs_height_45 cs_height_lg_30"></div>
        <h3 class="mmh_service_subhead">What to expect</h3>
        <div class="row cs_gap_y_30">
          <?php foreach ($service['steps'] as $index => $step) : ?>
          <div class="col-md-4">
            <div class="mmh_service_step">
              <span><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
              <h4><?php echo $h($step['title']); ?></h4>
              <p><?php echo $h($step['text']); ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="cs_height_45 cs_height_lg_30"></div>
        <h3 class="mmh_service_subhead">Common questions</h3>
        <div class="mmh_service_faq">
          <?php foreach ($service['faqs'] as $faq) : ?>
          <details>
            <summary><?php echo $h($faq['q']); ?></summary>
            <p><?php echo $h($faq['a']); ?></p>
          </details>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="cs_height_50 cs_height_lg_40"></div>
      <h3 class="mmh_service_subhead">Related MidWest Medical Hub services</h3>
      <div class="row cs_row_gap_30 cs_gap_y_30">
        <?php foreach ($mmhServices as $slug => $item) : if ($slug === $serviceSlug) continue; ?>
        <div class="col-md-6 col-xl-4">
          <a class="mmh_related_service" href="<?php echo $h($item['file']); ?>">
            <strong><?php echo $h($item['name']); ?></strong>
            <span><?php echo $h($item['summary']); ?></span>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="cs_height_80 cs_height_lg_50"></div>
    <section class="cs_connected_care cs_bg_filed" data-src="assets/img/unsplash_connected_care.jpg">
      <div class="container">
        <div class="cs_height_80 cs_height_lg_60"></div>
        <p class="cs_section_subtitle">CONNECTED CARE</p>
        <h2 class="cs_section_title">One hub for clinic, day programs, and care at home.</h2>
        <p class="cs_cta_subtitle">If you are not sure which service to start with, request a visit. MidWest Medical Hub will help you choose the next right step.</p>
        <a href="appointments" class="cs_btn cs_style_1 cs_color_3">
          <span>Book an Appointment</span>
          <i class="fa-solid fa-angles-right"></i>
        </a>
        <div class="cs_height_80 cs_height_lg_60"></div>
      </div>
    </section>
  </section>
<?php include __DIR__ . '/footer.php'; ?>
