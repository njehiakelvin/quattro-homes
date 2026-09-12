<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

$settings        = getSettings();
$whatsapp_number = $settings['whatsapp_number'];
$token           = csrf_token();
$currency        = $settings['currency'];
$price_per_night = (float)$settings['price_per_night'];
$min_stay        = (int)$settings['min_stay_nights'];

$pageTitle       = 'Quattro Homes | Exclusive 2-Bedroom Stays in Bungoma, Kenya';
$pageDescription = 'Fully-furnished, secure 2-bedroom apartments in Bungoma, Kenya.';
$canonicalPath   = 'index.php';
$activePage      = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/includes/head.php'; ?></head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-slides" id="hero-slides">
    <div class="hero-slide active" style="background-image:url('images/floor2/IMG_5843.jpg');"></div>
    <div class="hero-slide" style="background-image:url('images/floor2/IMG_5878.jpg');"></div>
    <div class="hero-slide" style="background-image:url('images/floor2/IMG_5893.jpg');"></div>
    <div class="hero-slide" style="background-image:url('images/floor2/IMG_5862.jpg');"></div>
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <p class="hero-eyebrow">Bungoma, Kenya</p>
    <h1>A place that feels<br><em>like home.</em></h1>
    <p class="lede">Spacious, secure 2-bedroom apartments. Two independent floors, one uncompromising standard.</p>
    <a href="#book-section" class="hero-scroll-cta">Check Availability ↓</a>
  </div>
  <div class="hero-dots" id="hero-dots"></div>
</section>

<!-- COMBINED: PERFECT FOR + LIVE CALENDAR -->
<section class="book-combined" id="book-section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Check &amp; Book</p>
      <h2>Is your date available?</h2>
    </div>
    <div class="combined-grid">

      <!-- Left: Perfect for cards filling calendar height -->
      <div class="combined-left">
        <h4 class="combined-sub">Perfect for</h4>
        <div class="suited-big-grid">
          <div class="suited-big-item"><i class="fa-solid fa-users"></i><span>Family time</span></div>
          <div class="suited-big-item"><i class="fa-solid fa-briefcase"></i><span>Business trips</span></div>
          <div class="suited-big-item"><i class="fa-solid fa-umbrella-beach"></i><span>Staycations</span></div>
          <div class="suited-big-item"><i class="fa-solid fa-stethoscope"></i><span>Medical visits</span></div>
          <div class="suited-big-item"><i class="fa-solid fa-graduation-cap"></i><span>Academic visits</span></div>
          <div class="suited-big-item"><i class="fa-solid fa-calendar-days"></i><span>Long stays</span></div>
        </div>
      </div>

      <!-- Right: Live calendar -->
      <div class="combined-right card calendar-card">
        <h3 style="margin-top:0;font-size:1.15rem;">Live Availability</h3>
        <div class="cal-floor-tabs" id="cal-floor-tabs">
          <button type="button" class="cal-floor-tab active" data-cal-floor="floor2">Floor 2</button>
          <button type="button" class="cal-floor-tab" data-cal-floor="floor1">Floor 1</button>
          <button type="button" class="cal-floor-tab" data-cal-floor="both">Both</button>
        </div>
        <div class="cal-nav">
          <button type="button" id="cal-prev">&#8249;</button>
          <span class="cal-month-label" id="cal-month-label"></span>
          <button type="button" id="cal-next">&#8250;</button>
        </div>
        <div class="cal-grid-wrap">
          <div class="cal-grid" id="cal-grid"></div>
          <div class="cal-loading" id="cal-loading" style="display:none;"><span class="spinner"></span></div>
        </div>
        <div class="legend">
          <span><i class="i-avail"></i> Available</span>
          <span><i class="i-booked"></i> Booked</span>
        </div>
        <a href="book.php" class="btn btn-gold" style="width:100%;text-align:center;margin-top:18px;">Book Now</a>
      </div>

    </div>
  </div>
</section>

<!-- PHOTO MOSAIC -->
<section class="photos-section bg-subtle-section" id="gallery">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">The Space</p>
      <h2>See exactly what you're booking</h2>
    </div>
  </div>
  <div class="photos-mosaic">
    <a href="gallery.php" class="mosaic-item mosaic-large">
      <img src="images/floor2/IMG_5843.jpg" alt="Floor 2 living room" loading="eager">
      <span class="mosaic-label">Floor 2 · Living Room</span>
    </a>
    <a href="gallery.php" class="mosaic-item">
      <img src="images/floor2/IMG_5885.jpg" alt="Floor 2 bedroom" loading="eager">
      <span class="mosaic-label">Floor 2 · Bedroom 2</span>
    </a>
    <a href="gallery.php" class="mosaic-item">
      <img src="images/floor2/IMG_5893.jpg" alt="Floor 2 kitchen" loading="lazy">
      <span class="mosaic-label">Floor 2 · Kitchen</span>
    </a>
    <a href="gallery.php" class="mosaic-item">
      <img src="images/floor2/IMG_5878.jpg" alt="Floor 2 bedroom with baby cot" loading="lazy">
      <span class="mosaic-label">Floor 2 · Bedroom with Baby Cot</span>
    </a>
    <a href="gallery.php" class="mosaic-item">
      <img src="images/floor1/IMG_5923.jpg" alt="Floor 1 living room" loading="lazy">
      <span class="mosaic-label">Floor 1 · Living Room</span>
    </a>
  </div>
  <div class="container" style="margin-top:24px;text-align:center;">
    <a href="gallery.php" class="btn btn-outline">View All Photos</a>
  </div>
</section>

<!-- AMENITIES -->
<section class="amenities-section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Everything Included</p>
      <h2>No extras, no surprises</h2>
    </div>
    <div class="amenities-grid">
      <div class="amenity-card"><i class="fa-solid fa-wifi"></i><strong>Free WiFi</strong><span>High-speed throughout</span></div>
      <div class="amenity-card"><i class="fa-solid fa-bed"></i><strong>2 Bedrooms</strong><span>Fully furnished &amp; comfortable</span></div>
      <div class="amenity-card"><i class="fa-solid fa-kitchen-set"></i><strong>Full Kitchen</strong><span>Equipped with everything</span></div>
      <div class="amenity-card"><i class="fa-solid fa-square-parking"></i><strong>Free Parking</strong><span>Secure on-site parking</span></div>
      <div class="amenity-card"><i class="fa-solid fa-shield-halved"></i><strong>Secure Compound</strong><span>Gated, private &amp; safe</span></div>
      <div class="amenity-card"><i class="fa-solid fa-droplet"></i><strong>Hot Water</strong><span>24/7 availability</span></div>
      <div class="amenity-card"><i class="fa-solid fa-tv"></i><strong>Smart TV</strong><span>Streaming-ready</span></div>
      <div class="amenity-card"><i class="fa-solid fa-location-dot"></i><strong>Central Location</strong><span>Easy access to town</span></div>
    </div>
  </div>
</section>

<!-- TWO FLOORS -->
<section class="floors-section bg-subtle-section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Two Floors, One Standard</p>
      <h2>Book one floor or the whole house</h2>
      <p>Each floor is a fully self-contained 2-bedroom apartment. Book independently or take both for a larger group.</p>
    </div>
    <div class="floors-grid">
      <div class="floor-card">
        <div class="floor-img">
          <img src="images/floor1/IMG_5926.jpg" alt="Floor 1" loading="lazy">
          <span class="floor-badge">Floor 1</span>
        </div>
        <div class="floor-body">
          <h3>Floor 1</h3>
          <p>First floor apartment with private access, 2 bedrooms, living room, full kitchen and bathroom.</p>

        </div>
      </div>
      <div class="floor-card">
        <div class="floor-img">
          <img src="images/floor2/IMG_5843.jpg" alt="Floor 2" loading="lazy">
          <span class="floor-badge">Floor 2</span>
        </div>
        <div class="floor-body">
          <h3>Floor 2</h3>
          <p>Upper floor apartment with balcony views, 2 bedrooms, open-plan living and dining, full kitchen.</p>

        </div>
      </div>
    </div>
    <div style="text-align:center;margin-top:28px;">
      <a href="book.php" class="btn btn-primary">Book Now</a>
    </div>
  </div>
</section>

<!-- PRICING -->
<section class="pricing-section" id="pricing">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Simple Pricing</p>
      <h2>One rate, everything included</h2>
    </div>
    <div class="pricing-card">
      <div class="price-amount"><?php echo $currency; ?> <?php echo number_format($price_per_night); ?><span> / night</span></div>
      <ul class="price-includes">
        <li>Free WiFi &amp; parking</li>
        <li>Fully equipped kitchen</li>
        <li>Secure, private compound</li>
        <li>No hidden fees</li>
      </ul>
      <p class="price-note">Minimum stay: <?php echo $min_stay; ?> night(s). Both floors priced as two units.</p>
      <?php if ((float)$settings['discount_percent'] > 0): ?>
      <p class="price-discount-badge">Stay <?php echo (int)$settings['discount_min_nights']; ?>+ nights &rarr; <?php echo (float)$settings['discount_percent']; ?>% off</p>
      <?php endif; ?>
      <div style="margin-top:26px;text-align:center;">
        <a href="book.php" class="btn btn-primary">Book Now</a>
      </div>
    </div>
  </div>
</section>

<!-- REVIEWS -->
<section class="reviews-section bg-subtle-section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Guest Reviews</p>
      <h2>What our guests say</h2>
    </div>
    <?php
      $reviews = [];
      try {
        $stmt = getDB()->query("SELECT reviewer_name, rating, review_text, created_at FROM reviews WHERE approved=1 ORDER BY created_at DESC LIMIT 6");
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch(Exception $e) {}
    ?>
    <?php if ($reviews): ?>
    <div class="testimonial-grid">
      <?php foreach($reviews as $r): ?>
      <div class="testimonial-card">
        <div class="stars"><?php echo str_repeat('<i class="fa-solid fa-star"></i>', (int)$r['rating']); ?><?php echo str_repeat('<i class="fa-regular fa-star"></i>', 5-(int)$r['rating']); ?></div>
        <p>"<?php echo htmlspecialchars($r['review_text']); ?>"</p>
        <div class="testimonial-name"><?php echo htmlspecialchars($r['reviewer_name']); ?> &nbsp;·&nbsp; <?php echo date('M Y', strtotime($r['created_at'])); ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="testimonial-grid">
      <div class="testimonial-card"><div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div><p>"Absolutely loved the space, clean, well-furnished, and the WiFi was excellent. Perfect for our family visit."</p><div class="testimonial-name">Sarah M. &nbsp;·&nbsp; 3 nights</div></div>
      <div class="testimonial-card"><div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div><p>"Great value, quiet compound, and the kitchen had everything we needed. Would book again without hesitation."</p><div class="testimonial-name">James K. &nbsp;·&nbsp; Floor 2</div></div>
      <div class="testimonial-card"><div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div><p>"We booked the whole house for a family reunion. Plenty of space, great communication from the host."</p><div class="testimonial-name">Grace O. &nbsp;·&nbsp; Both floors</div></div>
    </div>
    <?php endif; ?>
    <div style="text-align:center;margin-top:36px;">
      <a href="testimonials.php" class="btn btn-outline">Read All Reviews</a>
    </div>
  </div>
</section>

<!-- BOOKING FORM -->
<section class="booking-section" id="booking-home">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Book Your Stay</p>
      <h2>Ready to reserve? Let's do it.</h2>
      <p>Fill in your details and we'll confirm by WhatsApp or phone within minutes.</p>
    </div>
    <div class="booking-grid">
      <div class="card">
        <form id="booking-form" autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
          <div class="hp-field" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
          </div>
          <div class="field">
            <label for="floor">Which unit? *</label>
            <select id="floor" name="floor" required>
              <option value="floor2">Floor 2</option>
              <option value="floor1">Floor 1</option>
              <option value="both">Both floors (whole house)</option>
            </select>
          </div>
          <div class="form-row">
            <div class="field">
              <label for="full_name">Full Name *</label>
              <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="field">
              <label for="phone">Phone Number *</label>
              <input type="tel" id="phone" name="phone" placeholder="e.g. 07XX XXX XXX" required>
            </div>
          </div>
          <div class="field">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-row">
            <div class="field">
              <label for="checkin_date">Check-in *</label>
              <input type="date" id="checkin_date" name="checkin_date" required>
            </div>
            <div class="field">
              <label for="checkout_date">Check-out *</label>
              <input type="date" id="checkout_date" name="checkout_date" required>
            </div>
          </div>
          <div class="form-row">
            <div class="field">
              <label for="guests">Guests</label>
              <input type="number" id="guests" name="guests" min="1" value="2">
            </div>
            <div class="field">
              <label for="purpose">Purpose of Stay</label>
              <select id="purpose" name="purpose">
                <option value="Family time">Family time</option>
                <option value="Business trip">Business trip</option>
                <option value="Staycation">Staycation</option>
                <option value="Medical visit">Medical visit</option>
                <option value="Academic visit">Academic visit</option>
                <option value="Long stay">Long stay</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label for="message">Message (optional)</label>
            <textarea id="message" name="message" placeholder="Anything else we should know?"></textarea>
          </div>
          <div class="field">
            <label for="referral_code">Referral Code (optional)</label>
            <input type="text" id="referral_code" name="referral_code" placeholder="e.g. QH1023">
          </div>
          <div id="referral-applied-note" style="display:none;" class="referral-applied-note">
            <i class="fa-solid fa-circle-check"></i> Referral code applied.
          </div>
          <div id="price-summary" class="price-summary" style="display:none;">
            <span id="price-nights"></span>
            <strong id="price-total"></strong>
          </div>
          <!-- Terms & Conditions -->
          <div class="field terms-field">
            <label class="checkbox-label">
              <input type="checkbox" id="terms_agree" name="terms_agree" required>
              <span>I agree to the <a href="terms.php" target="_blank" class="terms-link">Terms &amp; Conditions</a> and <a href="terms.php#privacy" target="_blank" class="terms-link">Privacy Policy</a> *</span>
            </label>
          </div>
          <button type="submit" class="submit-btn" id="submit-btn">Request to Book</button>
          <div id="form-feedback"></div>
        </form>
      </div>

      <!-- Calendar beside form -->
      <div class="card calendar-card" id="home-calendar">
        <h3 style="margin-top:0;font-size:1.15rem;">Check Availability</h3>
        <div class="cal-floor-tabs" id="cal-floor-tabs-home">
          <button type="button" class="cal-floor-tab active" data-cal-floor="floor2">Floor 2</button>
          <button type="button" class="cal-floor-tab" data-cal-floor="floor1">Floor 1</button>
          <button type="button" class="cal-floor-tab" data-cal-floor="both">Both</button>
        </div>
        <div class="cal-nav">
          <button type="button" id="cal-prev-home">&#8249;</button>
          <span class="cal-month-label" id="cal-month-label-home"></span>
          <button type="button" id="cal-next-home">&#8250;</button>
        </div>
        <div class="cal-grid-wrap">
          <div class="cal-grid" id="cal-grid-home"></div>
          <div class="cal-loading" id="cal-loading-home" style="display:none;"><span class="spinner"></span></div>
        </div>
        <div class="legend">
          <span><i class="i-avail"></i> Available</span>
          <span><i class="i-booked"></i> Booked</span>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/foot.php'; ?>
