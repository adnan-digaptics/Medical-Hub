<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
$pageTitle = 'Blogs';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  if (!mmh_csrf_check()) {
    mmh_flash('error', 'Please try again.');
  } else {
    $id = (int) $_POST['delete_id'];
    $stmt = mmh_db()->prepare('DELETE FROM posts WHERE id = :id');
    $stmt->execute(array('id' => $id));
    mmh_flash('ok', 'Blog deleted.');
  }
  mmh_redirect('posts.php');
}
$posts = mmh_db()->query('SELECT * FROM posts ORDER BY id DESC')->fetchAll();
include __DIR__ . '/_header.php';
?>
  <div class="mmh-toolbar">
    <p>These posts appear on the Resources page, home slider, and footer.</p>
    <div class="mmh-actions">
      <a class="mmh-btn-ghost" href="categories.php">Categories</a>
      <a class="mmh-btn" href="post-edit.php">Add blog</a>
    </div>
  </div>
  <div class="mmh-card">
    <table>
      <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Category</th>
        <th>Status</th>
        <th></th>
      </tr>
      <?php foreach ($posts as $post) : ?>
      <tr>
        <td><img class="mmh-thumb" src="../<?php echo mmh_h(mmh_post_image($post)); ?>" alt=""></td>
        <td>
          <strong><?php echo mmh_h($post['title']); ?></strong><br>
          <a href="../blog-details?slug=<?php echo rawurlencode($post['slug']); ?>" target="_blank" rel="noopener">View</a>
        </td>
        <td><?php echo mmh_h($post['category']); ?></td>
        <td><span class="mmh-status mmh-status--<?php echo preg_replace('/[^a-z]/', '', strtolower($post['status'])); ?>"><?php echo mmh_h($post['status']); ?></span></td>
        <td class="mmh-actions">
          <a class="mmh-btn-ghost" href="post-edit.php?id=<?php echo (int) $post['id']; ?>">Edit</a>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this blog?');">
            <?php mmh_csrf_field(); ?>
            <input type="hidden" name="delete_id" value="<?php echo (int) $post['id']; ?>">
            <button class="mmh-btn-danger" type="submit">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
<?php include __DIR__ . '/_footer.php'; ?>
