<?php
require_once __DIR__ . '/helpers.php';

function mmh_config() {
  static $config = null;
  if ($config === null) {
    $config = include __DIR__ . '/config.php';
  }
  return $config;
}

function mmh_db() {
  static $pdo = null;
  if ($pdo instanceof PDO) {
    return $pdo;
  }
  $config = mmh_config();
  $dsnRoot = 'mysql:host=' . $config['db_host'] . ';charset=' . $config['db_charset'];
  $root = new PDO($dsnRoot, $config['db_user'], $config['db_pass'], array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ));
  $root->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $config['db_name']) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
  $dsn = 'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=' . $config['db_charset'];
  $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ));
  mmh_migrate($pdo);
  return $pdo;
}

function mmh_ensure_column(PDO $pdo, $table, $column, $definition) {
  $table = preg_replace('/[^a-z0-9_]/i', '', $table);
  $column = preg_replace('/[^a-z0-9_]/i', '', $column);
  $stmt = $pdo->query('SHOW COLUMNS FROM `' . $table . '` LIKE ' . $pdo->quote($column));
  if (!$stmt->fetch()) {
    $pdo->exec('ALTER TABLE `' . $table . '` ADD `' . $column . '` ' . $definition);
  }
}

function mmh_seed_setting(PDO $pdo, $key, $value) {
  $stmt = $pdo->prepare('INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (:k, :v)');
  $stmt->execute(array('k' => $key, 'v' => $value));
}

function mmh_seed_category(PDO $pdo, $name) {
  $name = trim((string) $name);
  if ($name === '') {
    return '';
  }
  $stmt = $pdo->prepare('SELECT name FROM post_categories WHERE name = :name LIMIT 1');
  $stmt->execute(array('name' => $name));
  $found = $stmt->fetchColumn();
  if ($found) {
    return $found;
  }
  $slug = mmh_slugify($name);
  $base = $slug;
  $i = 2;
  while (true) {
    $check = $pdo->prepare('SELECT id FROM post_categories WHERE slug = :slug LIMIT 1');
    $check->execute(array('slug' => $slug));
    if (!$check->fetch()) {
      break;
    }
    $slug = $base . '-' . $i;
    $i++;
  }
  $ins = $pdo->prepare('INSERT INTO post_categories (name, slug, created_at) VALUES (:name, :slug, NOW())');
  $ins->execute(array('name' => $name, 'slug' => $slug));
  return $name;
}

function mmh_migrate(PDO $pdo) {
  $pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content MEDIUMTEXT,
    image VARCHAR(255) DEFAULT '',
    category VARCHAR(80) DEFAULT 'Resources',
    author VARCHAR(120) DEFAULT 'MidWest Medical Hub',
    status VARCHAR(20) DEFAULT 'published',
    published_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    address VARCHAR(255) DEFAULT '',
    service VARCHAR(120) DEFAULT '',
    preferred_at VARCHAR(120) DEFAULT '',
    notes TEXT,
    status VARCHAR(20) DEFAULT 'new',
    created_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) DEFAULT '',
    subject VARCHAR(180) DEFAULT '',
    message TEXT,
    status VARCHAR(20) DEFAULT 'new',
    created_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(80) PRIMARY KEY,
    setting_value TEXT
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $pdo->exec("CREATE TABLE IF NOT EXISTS post_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    slug VARCHAR(80) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mmh_ensure_column($pdo, 'appointments', 'notes', 'TEXT');
  mmh_ensure_column($pdo, 'appointments', 'updated_at', 'DATETIME NULL');
  mmh_ensure_column($pdo, 'contacts', 'admin_notes', 'TEXT');
  mmh_ensure_column($pdo, 'contacts', 'updated_at', 'DATETIME NULL');

  $settingCount = (int) $pdo->query('SELECT COUNT(*) FROM settings')->fetchColumn();
  if ($settingCount === 0) {
    $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v)');
    $stmt->execute(array('k' => 'office_hours', 'v' => "Mon - Fri: 8.00 am. - 6.00 pm."));
    $stmt->execute(array('k' => 'site_name', 'v' => 'MidWest Medical Hub'));
  }
  mmh_seed_setting($pdo, 'notify_email', '');
  mmh_seed_setting($pdo, 'from_email', '');
  mmh_seed_setting($pdo, 'from_name', 'MidWest Medical Hub');
  mmh_seed_setting($pdo, 'smtp_host', '');
  mmh_seed_setting($pdo, 'smtp_port', '587');
  mmh_seed_setting($pdo, 'smtp_user', '');
  mmh_seed_setting($pdo, 'smtp_pass', '');
  mmh_seed_setting($pdo, 'smtp_secure', 'tls');

  $pdo->exec("DELETE FROM posts WHERE title = 'QA Dynamic Blog From Admin'");

  $emptyPosts = $pdo->query("SELECT id FROM posts WHERE image IS NULL OR image = '' ORDER BY id ASC")->fetchAll();
  if ($emptyPosts) {
    $imageUpdate = $pdo->prepare('UPDATE posts SET image = :image WHERE id = :id');
    foreach ($emptyPosts as $row) {
      $imageUpdate->execute(array(
        'image' => mmh_next_unused_post_image((int) $row['id']),
        'id' => $row['id'],
      ));
    }
  }

  $postCount = (int) $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
  if ($postCount === 0) {
    $posts = array(
      array(
        'title' => 'Preparing for a Primary Care Visit: Questions to Bring',
        'excerpt' => 'A short list of questions that helps your MidWest Medical Hub visit cover medications, symptoms, and the next step of care.',
        'image' => 'assets/img/post_1.jpeg',
        'category' => 'Primary Care',
        'content' => "A primary care visit works best when you arrive with a clear picture of what has changed. Write down new symptoms, the medications you take, and any questions about sleep, energy, or follow-up.\n\nBring a current medication list, including vitamins. If another clinic, hospital, or specialist was involved, note the dates. That helps the same hub keep your plan connected.\n\nAsk what belongs in clinic, what can wait, and whether adult day support, home care, or home health would make daily life safer. The goal is one coordinated next step, not a stack of disconnected referrals.",
      ),
      array(
        'title' => 'When Home Health Care May Be the Right Next Step',
        'excerpt' => 'Home health is skilled medical care at home. It is different from personal home care, and many families need both.',
        'image' => 'assets/img/post_2.jpeg',
        'category' => 'Home Health',
        'content' => "Home health care is used after a hospital stay, during recovery, or when nursing or therapy is safer in the home than in a clinic. It is skilled care, not the same as having a helper for bathing or meals.\n\nFamilies often mix the two. Home care covers daily living. Home health covers clinical tasks such as nursing, therapy, and education. MidWest Medical Hub coordinates both so the household is not managing two separate plans.\n\nIf travel is hard, medications are complicated, or a caregiver needs teaching, ask whether home health belongs in the next step of care.",
      ),
      array(
        'title' => 'Women’s Health Across the Adult Lifespan',
        'excerpt' => 'Women’s health at MidWest Medical Hub is whole-person primary care, including screening, chronic conditions, and healthy aging.',
        'image' => 'assets/img/post_3.jpeg',
        'category' => 'Women’s Health',
        'content' => "Women’s health is not a separate silo here. It is primary care with attention to screening, chronic disease, midlife changes, and the way needs shift over time.\n\nStay current on recommended screenings, keep a medication list, and tell your clinician about symptoms that are easy to dismiss. Continuity matters: the same team can connect later geriatric, adult day, or home support without starting over.\n\nIf you are helping a mother, sister, or spouse stay healthier as she ages, bring her questions to the visit. Family context is part of coordinated care.",
      ),
      array(
        'title' => 'Preventive Care for Healthy Aging',
        'excerpt' => 'Prevention for older adults includes medications, mobility, memory, and the support already available at home.',
        'image' => 'assets/img/post_4.jpeg',
        'category' => 'Geriatric Care',
        'content' => "Healthy aging is more than an annual checkup. Review medications, fall risk, memory changes, and what a normal day looks like at home.\n\nFamilies should mention missed meals, new confusion, or difficulty with stairs. Those details help geriatric care decide whether clinic follow-up, adult day care, or help at home is the safer path.\n\nPrevention also means keeping the same hub involved. When services are added, they should support one plan, not compete with it.",
      ),
      array(
        'title' => 'Understanding Home Health and Home Care',
        'excerpt' => 'Two different services, often used together: personal support at home and skilled clinical care at home.',
        'image' => 'assets/img/post_5.jpeg',
        'category' => 'Home Care',
        'content' => "Home care helps with bathing, meals, companionship, and a safer routine. Home health brings skilled nursing or therapy into the home.\n\nMany adults need a mix. After an illness, therapy may be short-term while personal support continues. MidWest Medical Hub keeps clinic, home care, and home health aligned so families are not repeating the story to every new person.\n\nIf you are not sure which one to start with, request a primary or geriatric visit. We will help you choose.",
      ),
      array(
        'title' => 'Healthy Aging: Practical Guidance for Families',
        'excerpt' => 'A practical guide for families supporting an older adult across clinic visits, day programs, and care at home.',
        'image' => 'assets/img/post_6.jpeg',
        'category' => 'Family Support',
        'content' => "Families often become the care coordinators overnight. Keep a shared list of medications, appointments, and what a good day looks like.\n\nAdult day care can give structure and respite. Home care can fill hours when nobody can be present. Geriatric and primary care visits should hear both the medical story and the home story.\n\nYou do not have to solve every setting at once. Start with the most urgent safety issue, then let MidWest Medical Hub help sequence the rest.",
      ),
    );
    $stmt = $pdo->prepare('INSERT INTO posts (title, slug, excerpt, content, image, category, author, status, published_at, created_at, updated_at) VALUES (:title, :slug, :excerpt, :content, :image, :category, :author, :status, :published_at, NOW(), NOW())');
    $day = 2;
    foreach ($posts as $post) {
      $stmt->execute(array(
        'title' => $post['title'],
        'slug' => mmh_slugify($post['title']),
        'excerpt' => $post['excerpt'],
        'content' => $post['content'],
        'image' => $post['image'],
        'category' => $post['category'],
        'author' => 'MidWest Medical Hub',
        'status' => 'published',
        'published_at' => date('Y-m-d H:i:s', strtotime('-' . $day . ' days')),
      ));
      $day += 5;
    }
  }

  $plainPosts = $pdo->query("SELECT id, content FROM posts WHERE content <> '' AND content NOT LIKE '%<%'")->fetchAll();
  if ($plainPosts) {
    $htmlUpdate = $pdo->prepare('UPDATE posts SET content = :content WHERE id = :id');
    foreach ($plainPosts as $row) {
      $htmlUpdate->execute(array(
        'content' => mmh_paragraphs($row['content']),
        'id' => $row['id'],
      ));
    }
  }

  foreach (array('Resources', 'Primary Care', 'Geriatric Care', 'Women’s Health', 'Adult Day Care', 'Home Care', 'Home Health', 'Family Support') as $catName) {
    mmh_seed_category($pdo, $catName);
  }
  $usedCats = $pdo->query("SELECT DISTINCT category FROM posts WHERE category IS NOT NULL AND category <> ''")->fetchAll();
  foreach ($usedCats as $row) {
    mmh_seed_category($pdo, $row['category']);
  }
}

try {
  mmh_db();
} catch (Throwable $e) {
  error_log('MMH DB: ' . $e->getMessage());
}
