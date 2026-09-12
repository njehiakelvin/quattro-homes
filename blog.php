<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

$settings = getSettings();
$token = csrf_token();

$posts = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT title, slug, excerpt, cover_image, created_at FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // fall back to empty state below
}

$pageTitle = 'Blog | Quattro Homes Bungoma';
$pageDescription = 'Travel tips, local guides, and stay advice from Quattro Homes in Bungoma, Kenya.';
$canonicalPath = 'blog.php';
$activePage = 'blog';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<section style="padding-top:70px;">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-en="From the Blog" data-sw="Kutoka kwa Blogu">From the Blog</p>
      <h2 data-en="Bungoma travel guides and stay tips" data-sw="Miongozo ya safari na vidokezo vya ukaaji Bungoma">Bungoma travel guides and stay tips</h2>
    </div>

    <?php if ($posts): ?>
      <div class="blog-grid">
        <?php foreach ($posts as $p): ?>
          <a class="blog-card" href="blog-post.php?slug=<?php echo urlencode($p['slug']); ?>">
            <?php if (!empty($p['cover_image'])): ?>
              <img src="<?php echo htmlspecialchars($p['cover_image']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy">
            <?php else: ?>
              <img src="images/floor2/IMG_5856.jpg" alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy">
            <?php endif; ?>
            <div class="blog-card-body">
              <span class="blog-date"><?php echo htmlspecialchars(date('M j, Y', strtotime($p['created_at']))); ?></span>
              <h3><?php echo htmlspecialchars($p['title']); ?></h3>
              <?php if (!empty($p['excerpt'])): ?>
                <p><?php echo htmlspecialchars($p['excerpt']); ?></p>
              <?php endif; ?>
              <span class="blog-read-more">Read more <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p style="text-align:center;color:#888;">No posts published yet. Check back soon.</p>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/foot.php'; ?>
