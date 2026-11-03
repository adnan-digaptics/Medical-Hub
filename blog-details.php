<?php
require_once __DIR__ . '/includes/db.php';
$slug = trim((string) ($_GET['slug'] ?? ''));
$post = $slug !== '' ? mmh_post_by_slug($slug) : false;
if (!$post) {
  $latest = mmh_published_posts(1);
  $post = $latest[0] ?? null;
}
$pageTitle = $post ? ($post['title'] . ' | MidWest Medical Hub') : 'Resources | MidWest Medical Hub';
$pageDescription = $post['excerpt'] ?? 'Health resources from MidWest Medical Hub.';
$headerType = 'inner';
include __DIR__ . '/includes/header.php';
if (!$post) {
  echo '<section class="container" style="padding:120px 0"><p>No blog posts yet.</p></section>';
  include __DIR__ . '/includes/footer.php';
  exit;
}
$related = array();
foreach (mmh_published_posts(6) as $item) {
  if ($item['id'] !== $post['id']) {
    $related[] = $item;
  }
}
$related = array_slice($related, 0, 3);
$categories = mmh_post_categories();
?>
  <section class="cs_page_heading cs_bg_filed cs_center" data-src="<?php echo mmh_h(mmh_post_image($post)); ?>">
    <div class="container">
      <h1 class="cs_page_title"><?php echo mmh_h($post['title']); ?></h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item"><a href="blog">Resources</a></li>
        <li class="breadcrumb-item active"><?php echo mmh_h($post['category']); ?></li>
      </ol>
    </div>
  </section>
  <section>
    <div class="cs_height_120 cs_height_lg_80"></div>
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="cs_post_details cs_style_1">
            <div class="cs_post_thumb_thumbnail">
              <img src="<?php echo mmh_h(mmh_post_image($post)); ?>" alt="<?php echo mmh_h($post['title']); ?>">
            </div>
            <ul class="cs_post_meta cs_mp0">
              <li><i class="fa-solid fa-user"></i><?php echo mmh_h($post['author']); ?></li>
              <li><i class="fa-regular fa-calendar-days"></i><?php echo mmh_h(date('F j, Y', strtotime($post['published_at'] ?: $post['created_at']))); ?></li>
              <li><?php echo mmh_h($post['category']); ?></li>
            </ul>
            <div class="mmh_post_body">
              <?php echo mmh_post_content($post['content']); ?>
            </div>
            <div class="cs_height_40 cs_height_lg_30"></div>
            <a href="appointments" class="cs_btn cs_style_1 cs_color_1">
              <span>Request an Appointment</span>
              <i class="fa-solid fa-angles-right"></i>
            </a>
          </div>
        </div>
        <aside class="col-lg-4">
          <div class="cs_height_0 cs_height_lg_50"></div>
          <div class="cs_sidebar cs_style_1">
            <div class="cs_sidebar_widget cs_radius_15 cs_search">
              <form action="blog" method="get">
                <input type="text" name="q" placeholder="Search...." value="<?php echo mmh_h($_GET['q'] ?? ''); ?>">
                <button type="submit" class="cs_search_icon cs_blue_bg cs_white_color" style="border:0">
                  <i class="fa-solid fa-magnifying-glass"></i>
                </button>
              </form>
            </div>
            <div class="cs_sidebar_widget cs_service cs_bg_filed" data-src="assets/img/hero_slider_7.jpg">
              <div class="cs_iconbox cs_style_11">
                <div class="cs_iconbox_icon cs_center">
                  <img src="assets/img/icons/service_icon_19.png" alt="Icon">
                </div>
                <h3 class="cs_iconbox_title cs_white_color">Need a Care Plan?</h3>
                <p class="cs_iconbox_subtitle cs_white_color">Primary care, geriatric care, and support at home through one hub.</p>
                <a href="service" class="cs_iconbox_btn cs_center"><i class="fa-solid fa-circle-chevron-right"></i></a>
              </div>
            </div>
            <div class="cs_sidebar_widget cs_radius_15">
              <h3 class="cs_sidebar_title">Recent Posts</h3>
              <?php foreach ($related as $item) : ?>
              <div class="cs_post cs_style_2">
                <a href="<?php echo mmh_h(mmh_post_url($item)); ?>" class="cs_post_thumb_thumbnail cs_type_2 cs_zoom">
                  <img src="<?php echo mmh_h(mmh_post_image($item)); ?>" alt="<?php echo mmh_h($item['title']); ?>" class="cs_zoom_in">
                </a>
                <div class="cs_post_info">
                  <div class="cs_post_meta"><i class="fa-regular fa-calendar-days"></i>
                    <?php echo mmh_h(date('M j, Y', strtotime($item['published_at'] ?: $item['created_at']))); ?></div>
                  <h3 class="cs_post_title mb-0"><a href="<?php echo mmh_h(mmh_post_url($item)); ?>"><?php echo mmh_h($item['title']); ?></a></h3>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="cs_sidebar_widget cs_radius_15">
              <h3 class="cs_sidebar_title">Categories</h3>
              <ul class="cs_categories cs_mp0">
                <?php foreach ($categories as $cat) : ?>
                <li><a href="blog?category=<?php echo rawurlencode($cat['category']); ?>"><i class="fa-solid fa-right-long"></i><?php echo mmh_h($cat['category']); ?><?php echo ((int) $cat['total'] > 0) ? ' (' . (int) $cat['total'] . ')' : ''; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </aside>
      </div>
    </div>
    <div class="cs_height_120 cs_height_lg_80"></div>
  </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
