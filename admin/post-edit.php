<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
if (!isset($mmhServices)) {
  include dirname(__DIR__) . '/includes/services-data.php';
}
$id = (int) ($_GET['id'] ?? 0);
$post = array(
  'id' => 0,
  'title' => '',
  'excerpt' => '',
  'content' => '',
  'image' => '',
  'category' => 'Resources',
  'author' => 'MidWest Medical Hub',
  'status' => 'published',
  'published_at' => date('Y-m-d\TH:i'),
);
if ($id) {
  $stmt = mmh_db()->prepare('SELECT * FROM posts WHERE id = :id LIMIT 1');
  $stmt->execute(array('id' => $id));
  $found = $stmt->fetch();
  if (!$found) {
    mmh_flash('error', 'Blog not found.');
    mmh_redirect('posts.php');
  }
  $post = $found;
  $post['published_at'] = $found['published_at'] ? date('Y-m-d\TH:i', strtotime($found['published_at'])) : date('Y-m-d\TH:i');
}
$pageTitle = $id ? 'Edit blog' : 'Add blog';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!mmh_csrf_check()) {
    mmh_flash('error', 'Please try again.');
    mmh_redirect('post-edit.php' . ($id ? '?id=' . $id : ''));
  }
  $title = trim((string) ($_POST['title'] ?? ''));
  $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
  $content = mmh_sanitize_post_html(trim((string) ($_POST['content'] ?? '')));
  $newCategory = trim((string) ($_POST['new_category'] ?? ''));
  $category = $newCategory !== '' ? $newCategory : trim((string) ($_POST['category'] ?? 'Resources'));
  if ($category === '') {
    $category = 'Resources';
  }
  mmh_seed_category(mmh_db(), $category);
  $author = trim((string) ($_POST['author'] ?? 'MidWest Medical Hub'));
  $status = ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
  $publishedAt = trim((string) ($_POST['published_at'] ?? ''));
  $publishedAt = $publishedAt !== '' ? date('Y-m-d H:i:s', strtotime($publishedAt)) : date('Y-m-d H:i:s');
  $image = $post['image'];
  $uploaded = mmh_save_upload('image');
  if ($uploaded) {
    $image = $uploaded;
  }
  if ($image === '') {
    $image = mmh_next_unused_post_image($id);
  }
  if ($title === '' || trim(strip_tags($content)) === '') {
    mmh_flash('error', 'Title and content are required.');
    mmh_redirect('post-edit.php' . ($id ? '?id=' . $id : ''));
  }
  $slug = mmh_unique_slug($title, $id);
  if ($id) {
    $stmt = mmh_db()->prepare('UPDATE posts SET title=:title, slug=:slug, excerpt=:excerpt, content=:content, image=:image, category=:category, author=:author, status=:status, published_at=:published_at, updated_at=NOW() WHERE id=:id');
    $stmt->execute(array(
      'title' => $title, 'slug' => $slug, 'excerpt' => $excerpt, 'content' => $content, 'image' => $image,
      'category' => $category, 'author' => $author, 'status' => $status, 'published_at' => $publishedAt, 'id' => $id,
    ));
    mmh_flash('ok', 'Blog updated.');
    mmh_redirect('post-edit.php?id=' . $id);
  }
  $stmt = mmh_db()->prepare('INSERT INTO posts (title, slug, excerpt, content, image, category, author, status, published_at, created_at, updated_at) VALUES (:title, :slug, :excerpt, :content, :image, :category, :author, :status, :published_at, NOW(), NOW())');
  $stmt->execute(array(
    'title' => $title, 'slug' => $slug, 'excerpt' => $excerpt, 'content' => $content, 'image' => $image,
    'category' => $category, 'author' => $author, 'status' => $status, 'published_at' => $publishedAt,
  ));
  mmh_flash('ok', 'Blog published.');
  mmh_redirect('posts.php');
}
$categories = mmh_all_categories();
$categoryNames = array();
foreach ($categories as $cat) {
  $categoryNames[] = $cat['name'];
}
if ($post['category'] !== '' && !in_array($post['category'], $categoryNames, true)) {
  $categories[] = array('name' => $post['category']);
}
include __DIR__ . '/_header.php';
?>
  <form class="mmh-form mmh-post-edit" method="post" enctype="multipart/form-data" id="mmh-post-form">
    <?php mmh_csrf_field(); ?>
    <div class="mmh-post-edit__main">
      <div class="mmh-card mmh-post-title-card">
        <label class="mmh-sr">Title</label>
        <input class="mmh-post-title" type="text" name="title" required placeholder="Add title" value="<?php echo mmh_h($post['title']); ?>">
      </div>
      <div class="mmh-card mmh-post-editor-card">
        <div class="mmh-editor-bar">
          <div>
            <strong>Content</strong>
            <span>Write and format the post visually, the same way it will appear on the site.</span>
          </div>
          <div class="mmh-editor-tabs" role="tablist">
            <button type="button" class="is-active" data-editor-tab="visual" role="tab" aria-selected="true">Visual</button>
            <button type="button" data-editor-tab="code" role="tab" aria-selected="false">Code</button>
          </div>
        </div>
        <textarea id="mmh-content" name="content" rows="18"><?php echo mmh_h($post['content']); ?></textarea>
      </div>
    </div>
    <aside class="mmh-post-edit__side">
      <div class="mmh-card">
        <h2 class="mmh-box-title">Publish</h2>
        <div class="row">
          <label>Status</label>
          <select name="status">
            <option value="published"<?php echo $post['status'] === 'published' ? ' selected' : ''; ?>>Published</option>
            <option value="draft"<?php echo $post['status'] === 'draft' ? ' selected' : ''; ?>>Draft</option>
          </select>
        </div>
        <div class="row">
          <label>Publish date</label>
          <input type="datetime-local" name="published_at" value="<?php echo mmh_h($post['published_at']); ?>">
        </div>
        <button class="mmh-btn" type="submit">Save blog</button>
        <a class="mmh-btn-ghost" href="posts.php">Back</a>
      </div>
      <div class="mmh-card">
        <h2 class="mmh-box-title">Category &amp; author</h2>
        <div class="row">
          <label>Category</label>
          <select name="category">
            <?php foreach ($categories as $cat) : ?>
            <option value="<?php echo mmh_h($cat['name']); ?>"<?php echo $post['category'] === $cat['name'] ? ' selected' : ''; ?>><?php echo mmh_h($cat['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="row">
          <label>Add new category</label>
          <input type="text" name="new_category" maxlength="80" placeholder="Type a name to create one">
          <p class="mmh-muted">Leave blank to use the selected category. <a href="categories.php">Manage categories</a></p>
        </div>
        <div class="row">
          <label>Author</label>
          <input type="text" name="author" value="<?php echo mmh_h($post['author']); ?>">
        </div>
      </div>
      <div class="mmh-card">
        <h2 class="mmh-box-title">Featured image</h2>
        <?php if (!empty($post['image'])) : ?>
        <p><img class="mmh-featured" src="../<?php echo mmh_h(mmh_post_image($post)); ?>" alt=""></p>
        <?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
      </div>
      <div class="mmh-card">
        <h2 class="mmh-box-title">Excerpt</h2>
        <p class="mmh-muted">Shown on resource cards, not in the full post body.</p>
        <textarea name="excerpt" rows="4"><?php echo mmh_h($post['excerpt']); ?></textarea>
      </div>
    </aside>
  </form>
<?php
$adminScripts = array(
  'assets/vendor/tinymce/tinymce.min.js',
  'assets/post-editor.js?v=20260916a',
);
include __DIR__ . '/_footer.php';
?>
