<?php
  $pageTitle = 'Services | MidWest Medical Hub';
  $pageDescription = 'Explore MidWest Medical Hub services: primary care, geriatric care, women’s health, adult day care, home care, and home health.';
  $headerType = 'inner';
  include __DIR__ . '/includes/header.php';
  $h = function ($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  };
?>
  <section class="cs_page_heading cs_bg_filed cs_center" data-src="assets/img/medical_solution_bg_1.jpg">
    <div class="container">
      <h1 class="cs_page_title">Our Services</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item active">Services</li>
      </ol>
    </div>
  </section>
  <section>
    <div class="cs_height_110 cs_height_lg_70"></div>
    <div class="container">
      <div class="cs_section_heading cs_style_1 cs_type_1">
        <div class="cs_section_heading_left">
          <p class="cs_section_subtitle cs_accent_color">
            <span class="cs_shape_left"></span>
            OUR SERVICES
          </p>
          <h2 class="cs_section_title">Comprehensive Care, Connected Around You</h2>
        </div>
        <div class="cs_section_heading_right">MidWest Medical Hub brings primary care, geriatric care, women’s health, adult day care, home care, and home health into one coordinated model for adults, older patients, and families.
        </div>
      </div>
      <div class="cs_height_50 cs_height_lg_50"></div>
      <div class="row cs_row_gap_30 cs_gap_y_30">
        <?php $index = 1; foreach ($mmhServices as $item) : ?>
        <div class="col-xl-4 col-lg-4 col-sm-6">
          <div class="cs_iconbox cs_style_2 cs_gray_bg cs_radius_15">
            <div class="cs_iconbox_overlay cs_bg_filed" data-src="assets/img/service_bg.jpg"></div>
            <div class="cs_iconbox_shape"></div>
            <div class="cs_iconbox_header d-flex align-items-center justify-content-between">
              <div class="cs_iconbox_icon cs_center">
                <img src="<?php echo $h($item['icon']); ?>" alt="<?php echo $h($item['name']); ?>">
              </div>
              <h3 class="iconbox_index"><?php echo str_pad((string) $index, 2, '0', STR_PAD_LEFT); ?></h3>
            </div>
            <h3 class="cs_iconbox_title"><a href="<?php echo $h($item['file']); ?>"><?php echo $h($item['name']); ?></a></h3>
            <p class="cs_iconbox_subtitle m-0"><?php echo $h($item['summary']); ?></p>
          </div>
        </div>
        <?php $index++; endforeach; ?>
      </div>
      <div class="cs_service_footer">
        <div class="cs_service_footer_icon">
          <img src="assets/img/icons/service_footer_icon_1.png" alt="Icon">
        </div>
        <div class="cs_service_footer_text cs_medium">
          Not sure where to start? Request a visit and we will help you choose the right service.<br>
          <a href="appointments">Book an Appointment<span> <i class="fa-solid fa-angles-right"></i></span></a>
        </div>
      </div>
    </div>
    <div class="cs_height_120 cs_height_lg_80"></div>
  </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
