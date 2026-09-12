<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

$settings     = getSettings();
$avgRating    = null;
$reviewCount  = 0;
try {
  $pdo = getDB();
  $agg = $pdo->query("SELECT AVG(rating) avg_r, COUNT(*) c FROM reviews WHERE approved=1")->fetch(PDO::FETCH_ASSOC);
  if ($agg && $agg['c'] > 0) { $avgRating = round((float)$agg['avg_r'],1); $reviewCount = (int)$agg['c']; }
} catch(Exception $e) {}

$pageTitle       = 'About Quattro Homes | Exclusive Stays in Bungoma, Kenya';
$pageDescription = 'Learn about Quattro Homes, two fully-furnished, secure 2-bedroom apartments in Bungoma, Kenya, designed for guests who want more than just a place to sleep.';
$canonicalPath   = 'about.php';
$activePage      = 'about';
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/includes/head.php'; ?></head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<!-- PAGE HEADER -->
<div class="page-header bg-forest">
  <div class="container">
    <p class="eyebrow" style="color:var(--gold-light);">About Us</p>
    <h1>More than a place to sleep.</h1>
    <p>Two fully-furnished apartments in Bungoma, built around your comfort.</p>
  </div>
</div>

<!-- STORY -->
<section>
  <div class="container">
    <div class="about-grid">
      <div class="about-img">
        <img src="images/floor2/IMG_5851.jpg" alt="Quattro Homes Floor 2 living room" loading="eager">
      </div>
      <div class="about-copy">
        <p class="eyebrow">Our Story</p>
        <h2>A home built for guests who expect more</h2>
        <p>Quattro Homes was created with one idea in mind: that short-stay guests in Bungoma deserve the same comfort and quality they'd find at home, or better. We designed both apartments from the ground up with real guests in mind, not just a checklist of amenities.</p>
        <p>Whether you're visiting for a medical appointment, a family occasion, a business trip, or just a well-deserved break, you'll find the space clean, quiet, private, and ready for you.</p>
        <div class="about-stats">
          <div>
            <strong><?php echo $avgRating !== null ? $avgRating : '5.0'; ?>/5</strong>
            <span><?php echo $reviewCount > 0 ? $reviewCount . ' reviews' : 'Guest rating'; ?></span>
          </div>
          <div><strong>2</strong><span>Bedrooms per floor</span></div>
          <div><strong>24/7</strong><span>WhatsApp support</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- VALUES -->
<section class="bg-subtle-section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">What We Stand For</p>
      <h2>The Quattro Homes standard</h2>
    </div>
    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon"><i class="fa-solid fa-star"></i></div>
        <h4>Quality without compromise</h4>
        <p>Every detail: linen, lighting, kitchen equipment, wifi speed, is chosen with a guest's real needs in mind. We don't cut corners.</p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fa-solid fa-lock"></i></div>
        <h4>Private &amp; secure</h4>
        <p>A gated compound, controlled access, and a quiet environment. You'll feel safe from the moment you arrive.</p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fa-brands fa-whatsapp"></i></div>
        <h4>Responsive hosts</h4>
        <p>We respond on WhatsApp fast. Questions before arrival, needs during your stay: we're a message away.</p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fa-solid fa-hand-holding-heart"></i></div>
        <h4>Genuine hospitality</h4>
        <p>We want you to leave better than you arrived. That means clean spaces, a smooth check-in, and a host who actually cares.</p>
      </div>
    </div>
  </div>
</section>

<!-- TWO FLOORS -->
<section>
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">The Property</p>
      <h2>Two independent floors, one address</h2>
      <p>Both apartments share the same compound and standard, but each has its own entrance, layout, and character. Book one or both.</p>
    </div>
    <div class="floors-grid">
      <div class="floor-card">
        <div class="floor-img">
          <img src="images/floor1/IMG_5926.jpg" alt="Floor 1 living room" loading="lazy">
          <span class="floor-badge">Floor 1</span>
        </div>
        <div class="floor-body">
          <h3>Floor 1</h3>
          <p>First floor apartment with its own private entrance. Spacious living area, 2 furnished bedrooms, full kitchen, and bathroom. Great for guests who prefer easy ground-level access.</p>
          <div class="floor-tags">
            <span>2 Bedrooms</span><span>Full Kitchen</span><span>Private Entrance</span><span>Ground Floor</span>
          </div>

        </div>
      </div>
      <div class="floor-card">
        <div class="floor-img">
          <img src="images/floor2/IMG_5883.jpg" alt="Floor 2 bedroom 2" loading="lazy">
          <span class="floor-badge">Floor 2</span>
        </div>
        <div class="floor-body">
          <h3>Floor 2</h3>
          <p>Upper-floor apartment with an open, airy feel. Two bedrooms, a bright living and dining area, full kitchen, and bathroom. The views and natural light make this floor a guest favourite.</p>
          <div class="floor-tags">
            <span>2 Bedrooms</span><span>Full Kitchen</span><span>Upper Floor</span><span>Guest Favourite</span>
          </div>

        </div>
      </div>
    </div>
    <div style="text-align:center;margin-top:28px;">
      <a href="book.php" class="btn btn-primary">Book Now</a>
    </div>
  </div>
</section>

<!-- LOCATION -->
<section class="bg-subtle-section">
  <div class="container">
    <div class="about-grid" style="gap:48px;">
      <div class="about-copy">
        <p class="eyebrow">Location</p>
        <h2>Centrally located in Bungoma</h2>
        <p>Quattro Homes is situated in a quiet, accessible part of Bungoma town, close enough to reach everything you need, and quiet enough to actually rest.</p>
        <ul class="location-list">
          <li><i class="fa-solid fa-circle-check"></i> Easy access to Bungoma town centre</li>
          <li><i class="fa-solid fa-circle-check"></i> Near major hospitals and medical facilities</li>
          <li><i class="fa-solid fa-circle-check"></i> Close to Bungoma County offices</li>
          <li><i class="fa-solid fa-circle-check"></i> Secure, residential neighbourhood</li>
        </ul>
        <a href="contact.php#map" class="btn btn-outline" style="margin-top:20px;">View on Map</a>
      </div>
      <div class="about-img">
        <iframe
          src="https://www.google.com/maps?q=HHM6+XQ9,+Bungoma&output=embed"
          width="100%" height="340" style="border:0;border-radius:var(--radius-lg);display:block;"
          allowfullscreen="" loading="lazy"
          referrerpolicy="no-referrer-when-downgrade" title="Quattro Homes map"></iframe>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="bg-forest-cta">
  <div class="container" style="text-align:center;">
    <h2 style="color:#f5ede0;font-size:clamp(1.8rem,4vw,2.6rem);margin-bottom:12px;">Ready to experience it yourself?</h2>
    <p style="color:rgba(245,237,224,0.65);margin-bottom:28px;">Check availability and book your stay in minutes.</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
      <a href="book.php" class="btn btn-gold">Book Now</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/foot.php'; ?>
