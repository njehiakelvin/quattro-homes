<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

$settings = getSettings();
$token = csrf_token();

$slug = trim($_GET['slug'] ?? '');
$post = null;
$relatedPosts = [];

try {
    $pdo = getDB();
    if ($slug !== '') {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = :slug AND status = 'published'");
        $stmt->execute([':slug' => $slug]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if ($post) {
        $relStmt = $pdo->prepare("SELECT title, slug FROM blog_posts WHERE status = 'published' AND id != :id ORDER BY created_at DESC LIMIT 3");
        $relStmt->execute([':id' => $post['id']]);
        $relatedPosts = $relStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $post = null;
}

if (!$post) {
    http_response_code(404);
}

$pageTitle = $post ? $post['title'] . ' | Quattro Homes Blog' : 'Post Not Found | Quattro Homes';
$pageDescription = $post ? ($post['meta_description'] ?: $post['excerpt'] ?: '') : 'This blog post could not be found.';
$canonicalPath = 'blog-post.php?slug=' . urlencode($slug);
$activePage = 'blog';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/includes/head.php'; ?>
<?php if ($post): ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": <?php echo json_encode($post['title']); ?>,
  "datePublished": <?php echo json_encode(date('c', strtotime($post['created_at']))); ?>,
  "dateModified": <?php echo json_encode(date('c', strtotime($post['updated_at']))); ?>,
  "publisher": { "@type": "Organization", "name": "Quattro Homes" }
}
</script>
<?php endif; ?>
</head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<section style="padding-top:70px;">
  <div class="container">
    <?php if (!$post): ?>
      <div class="section-head">
        <h2>Post not found</h2>
        <p>This article may have been moved or unpublished.</p>
        <div class="hero-actions">
          <a href="blog.php" class="btn btn-primary">Back to Blog</a>
        </div>
      </div>
    <?php else: ?>
      <article class="blog-article">
        <p class="eyebrow"><?php echo htmlspecialchars(date('F j, Y', strtotime($post['created_at']))); ?></p>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <?php if (!empty($post['cover_image'])): ?>
          <img class="blog-article-cover" src="<?php echo htmlspecialchars($post['cover_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
        <?php endif; ?>
        <div class="blog-article-body">
          <?php echo $post['content']; ?>
        </div>

        <div class="referral-card" style="margin-top:40px;">
          <div class="referral-icon"><i class="fa-solid fa-key"></i></div>
          <div>
            <h3>Planning a stay in Bungoma?</h3>
            <p>Quattro Homes offers fully-furnished 2-bedroom apartments with fast WiFi, secure parking, and a full kitchen. Book Floor 1, Floor 2, or the whole house.</p>
            <div class="hero-actions" style="justify-content:flex-start;margin-top:14px;">
              <a href="book.php" class="btn btn-primary">Check Availability</a>
            </div>
          </div>
        </div>
      </article>

      <?php if ($relatedPosts): ?>
      <div class="section-head" style="margin-top:60px;">
        <p class="eyebrow">More from the blog</p>
      </div>
      <div class="blog-grid">
        <?php foreach ($relatedPosts as $rp): ?>
          <a class="blog-card" href="blog-post.php?slug=<?php echo urlencode($rp['slug']); ?>">
            <img src="images/floor2/IMG_5856.jpg" alt="<?php echo htmlspecialchars($rp['title']); ?>" loading="lazy">
            <div class="blog-card-body">
              <h3><?php echo htmlspecialchars($rp['title']); ?></h3>
              <span class="blog-read-more">Read more <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/foot.php'; ?>
