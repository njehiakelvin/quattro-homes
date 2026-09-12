<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/settings.php';
require_admin_login();

$pdo = getDB();
$settings = getSettings();

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

$formError = '';
$editingPost = null;

// Handle create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_post'])) {
    $id = (int)($_POST['post_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
    $slug = slugify($slug);
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $coverImage = trim($_POST['cover_image'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['draft', 'published'], true) ? $_POST['status'] : 'draft';

    if (!$title || !$content) {
        $formError = 'Title and content are required.';
    } elseif (!$slug) {
        $formError = 'Could not generate a valid URL slug from that title. Please set one manually.';
    } else {
        // Ensure slug uniqueness (excluding this post if editing)
        $dupCheck = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = :slug AND id != :id");
        $dupCheck->execute([':slug' => $slug, ':id' => $id]);
        if ($dupCheck->fetch()) {
            $formError = 'That URL slug is already used by another post. Please choose a different one.';
        } else {
            if ($id > 0) {
                $stmt = $pdo->prepare(
                    "UPDATE blog_posts SET title=:title, slug=:slug, excerpt=:excerpt, content=:content,
                     cover_image=:cover_image, meta_description=:meta_description, status=:status WHERE id=:id"
                );
                $stmt->execute([
                    ':title' => $title, ':slug' => $slug, ':excerpt' => $excerpt, ':content' => $content,
                    ':cover_image' => $coverImage, ':meta_description' => $metaDescription, ':status' => $status, ':id' => $id,
                ]);
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO blog_posts (title, slug, excerpt, content, cover_image, meta_description, status)
                     VALUES (:title, :slug, :excerpt, :content, :cover_image, :meta_description, :status)"
                );
                $stmt->execute([
                    ':title' => $title, ':slug' => $slug, ':excerpt' => $excerpt, ':content' => $content,
                    ':cover_image' => $coverImage, ':meta_description' => $metaDescription, ':status' => $status,
                ]);
            }
            header('Location: blog.php');
            exit;
        }
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = :id");
    $stmt->execute([':id' => (int)$_POST['delete_post_id']]);
    header('Location: blog.php');
    exit;
}

// Load post for editing
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['edit']]);
    $editingPost = $stmt->fetch(PDO::FETCH_ASSOC);
}

$posts = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog — Quattro Homes Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">

<header class="admin-header">
  <div class="brand" style="color:var(--cream)">
    <div class="mark">Q</div>
    <div class="name">Quattro Homes<small>Blog</small></div>
  </div>
  <nav class="admin-nav">
    <a href="dashboard.php">Bookings</a>
    <a href="reviews.php">Reviews</a>
    <a href="issues.php">Issues</a>
    <a href="referrals.php">Referrals</a>
    <a href="blog.php" class="active">Blog</a>
    <a href="settings.php">Settings</a>
    <a href="logout.php">Log out</a>
  </nav>
</header>

<div class="container" style="padding-top:36px;padding-bottom:60px;">

  <div class="card settings-card">
    <h3 style="margin-top:0;"><?php echo $editingPost ? 'Edit Post' : 'New Post'; ?></h3>
    <?php if ($formError): ?>
      <div id="form-feedback" class="error"><?php echo htmlspecialchars($formError); ?></div>
    <?php endif; ?>
    <form method="post" class="settings-form">
      <input type="hidden" name="save_post" value="1">
      <input type="hidden" name="post_id" value="<?php echo $editingPost ? (int)$editingPost['id'] : 0; ?>">
      <div class="field">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($editingPost['title'] ?? ''); ?>">
      </div>
      <div class="form-row">
        <div class="field">
          <label for="slug">URL slug (leave blank to auto-generate from title)</label>
          <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($editingPost['slug'] ?? ''); ?>" placeholder="e.g. guide-to-bungoma-kenya">
        </div>
        <div class="field">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="draft" <?php echo (($editingPost['status'] ?? 'draft') === 'draft') ? 'selected' : ''; ?>>Draft</option>
            <option value="published" <?php echo (($editingPost['status'] ?? '') === 'published') ? 'selected' : ''; ?>>Published</option>
          </select>
        </div>
      </div>
      <div class="field">
        <label for="excerpt">Excerpt (short summary shown on the blog list)</label>
        <input type="text" id="excerpt" name="excerpt" maxlength="300" value="<?php echo htmlspecialchars($editingPost['excerpt'] ?? ''); ?>">
      </div>
      <div class="field">
        <label for="meta_description">Meta description (for search engines, ~155 characters)</label>
        <input type="text" id="meta_description" name="meta_description" maxlength="300" value="<?php echo htmlspecialchars($editingPost['meta_description'] ?? ''); ?>">
      </div>
      <div class="field">
        <label for="cover_image">Cover image path (optional, e.g. images/floor2/IMG_7852.jpg)</label>
        <input type="text" id="cover_image" name="cover_image" value="<?php echo htmlspecialchars($editingPost['cover_image'] ?? ''); ?>">
      </div>
      <div class="field">
        <label for="content">Content (HTML allowed — use &lt;p&gt; and &lt;h2&gt; tags)</label>
        <textarea id="content" name="content" style="min-height:260px;font-family:monospace;font-size:0.85rem;" required><?php echo htmlspecialchars($editingPost['content'] ?? ''); ?></textarea>
      </div>
      <button type="submit" class="submit-btn" style="width:auto;padding:12px 28px;"><?php echo $editingPost ? 'Update Post' : 'Create Post'; ?></button>
      <?php if ($editingPost): ?>
        <a href="blog.php" class="btn btn-outline" style="color:var(--forest);border-color:var(--forest);margin-left:10px;">Cancel</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="card" style="padding:0;overflow-x:auto;">
    <table class="admin-table">
      <thead>
        <tr><th>Title</th><th>Slug</th><th>Status</th><th>Created</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php if (!$posts): ?>
          <tr><td colspan="5" style="text-align:center;padding:30px;color:#999;">No posts yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($posts as $p): ?>
          <tr>
            <td data-label="Title"><?php echo htmlspecialchars($p['title']); ?></td>
            <td data-label="Slug"><code>/<?php echo htmlspecialchars($p['slug']); ?></code></td>
            <td data-label="Status"><span class="badge badge-<?php echo $p['status'] === 'published' ? 'confirmed' : 'pending'; ?>"><?php echo ucfirst($p['status']); ?></span></td>
            <td data-label="Created"><?php echo htmlspecialchars(date('M j, Y', strtotime($p['created_at']))); ?></td>
            <td data-label="Action">
              <a href="blog.php?edit=<?php echo (int)$p['id']; ?>" class="mini-btn" style="display:inline-block;text-decoration:none;margin-bottom:6px;">Edit</a>
              <?php if ($p['status'] === 'published'): ?>
                <a href="../blog-post.php?slug=<?php echo urlencode($p['slug']); ?>" target="_blank" class="mini-btn" style="display:inline-block;text-decoration:none;margin-bottom:6px;">View</a>
              <?php endif; ?>
              <form method="post" style="display:inline;" onsubmit="return confirm('Delete this post permanently?');">
                <input type="hidden" name="delete_post_id" value="<?php echo (int)$p['id']; ?>">
                <button type="submit" class="mini-btn" style="background:#a3382a;">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
<script src="admin.js"></script>
</body>
</html>
