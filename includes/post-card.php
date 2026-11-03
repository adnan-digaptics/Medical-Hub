<?php
$url = mmh_post_url($post);
$dateLabel = !empty($post['published_at']) ? date('M d', strtotime($post['published_at'])) : '';
?>
<article class="cs_post cs_style_1">
  <a href="<?php echo mmh_h($url); ?>" class="cs_post_thumbnail position-relative">
    <img src="<?php echo mmh_h(mmh_post_image($post)); ?>" alt="<?php echo mmh_h($post['title']); ?>">
    <div class="cs_post_category position-absolute"><?php echo mmh_h($post['category']); ?></div>
  </a>
  <div class="cs_post_content position-relative">
    <div class="cs_post_meta_wrapper">
      <div class="cs_posted_by cs_center position-absolute"><?php echo mmh_h($dateLabel); ?></div>
      <div class="cs_post_meta_item">
        <img src="assets/img/icons/post_user_icon.png" alt="Icon">
        <span>By: <?php echo mmh_h($post['author']); ?></span>
      </div>
      <div class="cs_post_meta_item">
        <img src="assets/img/icons/post_comment_icon.png" alt="Icon">
        <span><?php echo mmh_h($post['category']); ?></span>
      </div>
    </div>
    <h3 class="cs_post_title"><a href="<?php echo mmh_h($url); ?>"><?php echo mmh_h($post['title']); ?></a></h3>
    <p class="cs_post_subtitle"><?php echo mmh_h($post['excerpt']); ?></p>
    <a href="<?php echo mmh_h($url); ?>" class="cs_post_btn">
      <span>Read More</span>
      <span><i class="fa-solid fa-angle-right"></i></span>
    </a>
    <div class="cs_post_shape position-absolute"></div>
  </div>
</article>
