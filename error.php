<?php
  http_response_code(404);
  $pageTitle = 'Page Not Found | MidWest Medical Hub';
  $headerType = 'inner';
  include __DIR__ . '/includes/header.php';
?>
    <!-- Start Page Heading -->
    <section class="cs_page_heading cs_bg_filed cs_center" data-src="assets/img/page_heading_bg.jpg">
      <div class="container">
        <h1 class="cs_page_title">Error Page</h1>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="./">Home</a></li>
          <li class="breadcrumb-item active">404</li>
        </ol>
      </div>
    </section>
    <!-- End Page Heading -->
    <section>
      <div class="cs_height_110 cs_height_lg_70"></div>
      <div class="container">
        <div class="cs_error_content">
          <div class="cs_error_thumbnail">
            <img src="assets/img/error_thumbnail.png" alt="Image">
          </div>
          <h2 class="cs_error_heading">Page can’t be found</h2>
          <p class="cs_error_subtitle">Sorry This Page Not found take a look at our most popular</p>
          <a href="./" class="cs_btn cs_style_1 cs_color_2">
            <span>GO TO HOME</span>
          </a>
        </div>
      </div>
      <div class="cs_height_120 cs_height_lg_80"></div>
    </section>
    
<?php include __DIR__ . '/includes/footer.php'; ?>
