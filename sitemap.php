<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/db.php';

$settings = getSettings();
$siteUrl = rtrim($settings['site_url'] ?? 'https://quattrohomes.co.ke', '/');

header('Content-Type: application/xml; charset=utf-8');

$staticPages = [
    ['path' => 'index.php', 'freq' => 'weekly', 'priority' => '1.0'],
    ['path' => 'about.php', 'freq' => 'monthly', 'priority' => '0.8'],
    ['path' => 'gallery.php', 'freq' => 'monthly', 'priority' => '0.8'],
    ['path' => 'pricing.php', 'freq' => 'weekly', 'priority' => '0.9'],
    ['path' => 'book.php', 'freq' => 'daily', 'priority' => '1.0'],
    ['path' => 'testimonials.php', 'freq' => 'weekly', 'priority' => '0.7'],
    ['path' => 'blog.php', 'freq' => 'weekly', 'priority' => '0.7'],
    ['path' => 'faq.php', 'freq' => 'monthly', 'priority' => '0.6'],
    ['path' => 'contact.php', 'freq' => 'monthly', 'priority' => '0.6'],
];

$posts = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT slug, updated_at FROM blog_posts WHERE status = 'published'");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // if the DB isn't reachable, sitemap still outputs the static pages below
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $page): ?>
  <url>
    <loc><?php echo htmlspecialchars($siteUrl . '/' . $page['path']); ?></loc>
    <changefreq><?php echo $page['freq']; ?></changefreq>
    <priority><?php echo $page['priority']; ?></priority>
  </url>
<?php endforeach; ?>
<?php foreach ($posts as $post): ?>
  <url>
    <loc><?php echo htmlspecialchars($siteUrl . '/blog-post.php?slug=' . urlencode($post['slug'])); ?></loc>
    <lastmod><?php echo htmlspecialchars(date('Y-m-d', strtotime($post['updated_at']))); ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
<?php endforeach; ?>
</urlset>
