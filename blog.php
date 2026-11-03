<?php
  $search = trim((string) ($_GET['q'] ?? ''));
  $category = trim((string) ($_GET['category'] ?? ''));
  $pageTitle = 'Resources | MidWest Medical Hub';
  $headerType = 'inner';
  include __DIR__ . '/includes/header.php';
  $posts = mmh_published_posts(24, $search, $category);
  $allCats = mmh_post_categories();
?>
  <section class="cs_page_heading cs_bg_filed cs_center" data-src="assets/img/overlay_bg_1.jpeg">
    <div class="container">
      <h1 class="cs_page_title">Health Resources</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item active">Health Resources</li>
      </ol>
    </div>
  </section>
  <section>
    <div class="cs_height_110 cs_height_lg_70"></div>
    <div class="container">
      <div class="cs_section_heading cs_style_1 text-center">
        <p class="cs_section_subtitle cs_accent_color">
          <span class="cs_shape_left"></span>OUR LARGEST BLOG<span class="cs_shape_right"></span>
        </p>
        <h2 class="cs_section_title">Health Guidance for Patients & Families</h2>
        <?php if ($search !== '' || $category !== '') : ?>
        <p>Showing results<?php echo $category !== '' ? ' in ' . mmh_h($category) : ''; ?><?php echo $search !== '' ? ' for “' . mmh_h($search) . '”' : ''; ?>.</p>
        <?php endif; ?>
      </div>
      <?php if ($allCats) : ?>
      <div class="mmh_cat_pills">
        <a href="blog" class="<?php echo $category === '' ? 'is-active' : ''; ?>">All</a>
        <?php foreach ($allCats as $cat) : ?>
        <a href="blog?category=<?php echo rawurlencode($cat['category']); ?>" class="<?php echo $category === $cat['category'] ? 'is-active' : ''; ?>"><?php echo mmh_h($cat['category']); ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="cs_height_50 cs_height_lg_50"></div>
      <?php if (!$posts) : ?>
      <p class="text-center">No resources found. New articles appear here after they are published in the admin panel.</p>
      <?php else : ?>
      <div class="cs_posts_grid cs_style_1">
        <?php foreach ($posts as $post) { include __DIR__ . '/includes/post-card.php'; } ?>
      </div>
      <?php endif; ?>
    </div>
    <div class="cs_height_120 cs_height_lg_80"></div>
  </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
