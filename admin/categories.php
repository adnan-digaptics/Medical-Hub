<?php
require_once dirname(__DIR__) . '/includes/auth.php';
mmh_require_admin();
$pageTitle = 'Post categories';
$db = mmh_db();
$edit = null;
$editId = (int) ($_GET['id'] ?? 0);
if ($editId > 0) {
  $stmt = $db->prepare('SELECT * FROM post_categories WHERE id = :id LIMIT 1');
  $stmt->execute(array('id' => $editId));
  $edit = $stmt->fetch() ?: null;
  if (!$edit) {
    mmh_flash('error', 'Category not found.');
    mmh_redirect('categories.php');
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!mmh_csrf_check()) {
    mmh_flash('error', 'Please try again.');
    mmh_redirect('categories.php');
  }
  if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];
    $stmt = $db->prepare('SELECT * FROM post_categories WHERE id = :id LIMIT 1');
    $stmt->execute(array('id' => $id));
    $row = $stmt->fetch();
    if ($row) {
      $fallback = 'Resources';
      if (strcasecmp($row['name'], $fallback) === 0) {
        mmh_flash('error', 'The default Resources category cannot be deleted.');
        mmh_redirect('categories.php');
      }
      mmh_seed_category($db, $fallback);
      $upd = $db->prepare('UPDATE posts SET category = :fallback WHERE category = :old');
      $upd->execute(array('fallback' => $fallback, 'old' => $row['name']));
      $del = $db->prepare('DELETE FROM post_categories WHERE id = :id');
      $del->execute(array('id' => $id));
      mmh_flash('ok', 'Category deleted. Posts were moved to Resources.');
    }
    mmh_redirect('categories.php');
  }
  $name = trim((string) ($_POST['name'] ?? ''));
  $id = (int) ($_POST['id'] ?? 0);
  if ($name === '') {
    mmh_flash('error', 'Category name is required.');
    mmh_redirect('categories.php' . ($id ? '?id=' . $id : ''));
  }
  $dup = $db->prepare('SELECT id FROM post_categories WHERE name = :name AND id != :id LIMIT 1');
  $dup->execute(array('name' => $name, 'id' => $id));
  if ($dup->fetch()) {
    mmh_flash('error', 'That category already exists.');
    mmh_redirect('categories.php' . ($id ? '?id=' . $id : ''));
  }
  if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM post_categories WHERE id = :id LIMIT 1');
    $stmt->execute(array('id' => $id));
    $row = $stmt->fetch();
    if (!$row) {
      mmh_flash('error', 'Category not found.');
      mmh_redirect('categories.php');
    }
    $slug = mmh_slugify($name);
    $base = $slug;
    $i = 2;
    while (true) {
      $check = $db->prepare('SELECT id FROM post_categories WHERE slug = :slug AND id != :id LIMIT 1');
      $check->execute(array('slug' => $slug, 'id' => $id));
      if (!$check->fetch()) {
        break;
      }
      $slug = $base . '-' . $i;
      $i++;
    }
    $upd = $db->prepare('UPDATE post_categories SET name = :name, slug = :slug WHERE id = :id');
    $upd->execute(array('name' => $name, 'slug' => $slug, 'id' => $id));
    if ($row['name'] !== $name) {
      $move = $db->prepare('UPDATE posts SET category = :new WHERE category = :old');
      $move->execute(array('new' => $name, 'old' => $row['name']));
    }
    mmh_flash('ok', 'Category updated.');
    mmh_redirect('categories.php');
  }
  mmh_seed_category($db, $name);
  mmh_flash('ok', 'Category added.');
  mmh_redirect('categories.php');
}

$categories = $db->query("SELECT c.*, (SELECT COUNT(*) FROM posts p WHERE p.category = c.name) AS total FROM post_categories c ORDER BY c.name")->fetchAll();
include __DIR__ . '/_header.php';
?>
  <div class="mmh-split">
    <form class="mmh-form mmh-card" method="post">
      <?php mmh_csrf_field(); ?>
      <h2 class="mmh-box-title"><?php echo $edit ? 'Edit category' : 'Add category'; ?></h2>
      <?php if ($edit) : ?>
      <input type="hidden" name="id" value="<?php echo (int) $edit['id']; ?>">
      <?php endif; ?>
      <div class="row">
        <label>Name</label>
        <input type="text" name="name" required maxlength="80" value="<?php echo mmh_h($edit['name'] ?? ''); ?>" placeholder="e.g. Mental Health">
      </div>
      <button class="mmh-btn" type="submit"><?php echo $edit ? 'Save category' : 'Add category'; ?></button>
      <?php if ($edit) : ?>
      <a class="mmh-btn-ghost" href="categories.php">Cancel</a>
      <?php endif; ?>
    </form>
    <div class="mmh-card">
      <div class="mmh-toolbar">
        <p>Use these categories on blogs. They also appear on the Resources sidebar.</p>
      </div>
      <table>
        <tr>
          <th>Name</th>
          <th>Posts</th>
          <th></th>
        </tr>
        <?php if (!$categories) : ?>
        <tr><td colspan="3">No categories yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($categories as $cat) : ?>
        <tr>
          <td>
            <strong><?php echo mmh_h($cat['name']); ?></strong>
            <div class="mmh-muted"><?php echo mmh_h($cat['slug']); ?></div>
          </td>
          <td><?php echo (int) $cat['total']; ?></td>
          <td class="mmh-actions">
            <a class="mmh-btn-ghost" href="categories.php?id=<?php echo (int) $cat['id']; ?>">Edit</a>
            <?php if (strcasecmp($cat['name'], 'Resources') !== 0) : ?>
            <form method="post" onsubmit="return confirm('Delete this category? Posts will move to Resources.');">
              <?php mmh_csrf_field(); ?>
              <input type="hidden" name="delete_id" value="<?php echo (int) $cat['id']; ?>">
              <button class="mmh-btn-danger" type="submit">Delete</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
<?php include __DIR__ . '/_footer.php'; ?>
