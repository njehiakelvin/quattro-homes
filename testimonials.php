<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

$settings = getSettings();
$token = csrf_token();

$approvedReviews = [];
$avgRating = null;
$reviewCount = 0;
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT full_name, rating, comment, created_at FROM reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT 20");
    $approvedReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $agg = $pdo->query("SELECT AVG(rating) avg_r, COUNT(*) c FROM reviews WHERE status = 'approved'")->fetch(PDO::FETCH_ASSOC);
    if ($agg && $agg['c'] > 0) {
        $avgRating = round((float)$agg['avg_r'], 1);
        $reviewCount = (int)$agg['c'];
    }
} catch (Exception $e) {
    // fall back to empty state below
}

$pageTitle = 'Guest Reviews | Quattro Homes Bungoma';
$pageDescription = 'Read what guests say about staying at Quattro Homes in Bungoma, Kenya, and leave your own review after checkout.';
$canonicalPath = 'testimonials.php';
$activePage = 'testimonials';
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
      <p class="eyebrow" data-en="Guest Stories" data-sw="Hadithi za Wageni">Guest Stories</p>
      <h2 data-en="What guests say" data-sw="Wageni Wanasema Nini">What guests say</h2>
      <?php if ($avgRating !== null): ?>
        <p><strong><?php echo $avgRating; ?>/5</strong> &middot; <?php echo $reviewCount; ?> review<?php echo $reviewCount === 1 ? '' : 's'; ?></p>
      <?php endif; ?>
    </div>

    <?php if ($approvedReviews): ?>
    <div class="testimonial-grid">
      <?php foreach ($approvedReviews as $r): ?>
        <div class="testimonial-card">
          <div class="stars"><?php echo str_repeat('<i class="fa-solid fa-star"></i>', (int)$r['rating']) . str_repeat('<i class="fa-regular fa-star"></i>', 5 - (int)$r['rating']); ?></div>
          <?php if ($r['comment']): ?>
            <p>&ldquo;<?php echo htmlspecialchars($r['comment']); ?>&rdquo;</p>
          <?php endif; ?>
          <span class="testimonial-name"><?php echo htmlspecialchars($r['full_name']); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
      <p style="text-align:center;color:#888;" data-en="No reviews yet. Be the first to share your stay." data-sw="Hakuna maoni bado. Kuwa wa kwanza kushiriki ukaaji wako.">No reviews yet. Be the first to share your stay.</p>
    <?php endif; ?>

    <div class="review-form-wrap">
      <details class="review-toggle">
        <summary data-en="Stayed with us? Leave a review" data-sw="Umeishi nasi? Acha maoni">Stayed with us? Leave a review</summary>
        <form id="review-form" class="card" style="margin-top:18px;" autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
          <div class="hp-field" aria-hidden="true">
            <label for="review_website">Website</label>
            <input type="text" id="review_website" name="website" tabindex="-1" autocomplete="off">
          </div>
          <div class="form-row">
            <div class="field">
              <label for="booking_ref" data-en="Booking Reference # *" data-sw="Nambari ya Nafasi *">Booking Reference # *</label>
              <input type="text" id="booking_ref" name="booking_ref" placeholder="From your confirmation" required>
            </div>
            <div class="field">
              <label for="review_email" data-en="Email used to book *" data-sw="Barua pepe uliyotumia *">Email used to book *</label>
              <input type="email" id="review_email" name="email" required>
            </div>
          </div>
          <div class="field">
            <label data-en="Your rating *" data-sw="Ukadiriaji wako *">Your rating *</label>
            <div class="star-rating" id="star-rating">
              <button type="button" data-value="1" aria-label="1 star"><i class="fa-solid fa-star"></i></button>
              <button type="button" data-value="2" aria-label="2 stars"><i class="fa-solid fa-star"></i></button>
              <button type="button" data-value="3" aria-label="3 stars"><i class="fa-solid fa-star"></i></button>
              <button type="button" data-value="4" aria-label="4 stars"><i class="fa-solid fa-star"></i></button>
              <button type="button" data-value="5" aria-label="5 stars"><i class="fa-solid fa-star"></i></button>
            </div>
            <input type="hidden" id="rating" name="rating" value="0">
          </div>
          <div class="field">
            <label for="comment" data-en="Your review (optional)" data-sw="Maoni yako (hiari)">Your review (optional)</label>
            <textarea id="comment" name="comment" placeholder="Tell future guests about your stay"></textarea>
          </div>
          <button type="submit" class="submit-btn" id="review-submit-btn" data-en="Submit Review" data-sw="Tuma Maoni">Submit Review</button>
          <div id="review-feedback"></div>
        </form>
      </details>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/foot.php'; ?>
