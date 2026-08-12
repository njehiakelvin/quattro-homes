<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

$approvedReviews = [];
$avgRating = null;
$reviewCount = 0;
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT full_name, rating, comment, created_at FROM reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT 9");
    $approvedReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $agg = $pdo->query("SELECT AVG(rating) avg_r, COUNT(*) c FROM reviews WHERE status = 'approved'")->fetch(PDO::FETCH_ASSOC);
    if ($agg && $agg['c'] > 0) {
        $avgRating = round((float)$agg['avg_r'], 1);
        $reviewCount = (int)$agg['c'];
    }
} catch (Exception $e) {
    // fall back to empty state below if DB/tables aren't ready yet
}

$settings = getSettings();
$whatsapp_number = $settings['whatsapp_number'];
$whatsapp_display = '+' . substr($whatsapp_number, 0, 3) . ' ' . substr($whatsapp_number, 3, 3) . ' ' . substr($whatsapp_number, 6, 3) . ' ' . substr($whatsapp_number, 9);
$location = 'Bungoma, Kenya';
$currency = $settings['currency'];
$price_per_night = (float)$settings['price_per_night'];
$min_stay = (int)$settings['min_stay_nights'];
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quattro Homes | Exclusive Stays, Exceptional Comfort . Bungoma, Kenya</title>
<meta name="description" content="Quattro Homes offers fully-equipped, secure 2-bedroom apartments in Bungoma, Kenya. Book your stay for family time, business trips, staycations, medical or academic visits.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<!-- MailerLite Universal -->
<script>
    (function(w,d,e,u,f,l,n){w[f]=w[f]||function(){(w[f].q=w[f].q||[])
    .push(arguments);},l=d.createElement(e),l.async=1,l.src=u,
    n=d.getElementsByTagName(e)[0],n.parentNode.insertBefore(l,n);})
    (window,document,'script','https://assets.mailerlite.com/js/universal.js','ml');
    ml('account', '2565067');
</script>
<!-- End MailerLite Universal -->
</head>
<body>

<header class="site-header">
  <div class="nav-inner">
    <div class="brand">
      <div class="mark">Q</div>
      <div class="name">Quattro Homes<small>Exclusive Stays &middot; Exceptional Comfort</small></div>
    </div>
    <nav class="links">
      <a href="#about" data-en="About" data-sw="Kuhusu">About</a>
      <a href="#gallery" data-en="Gallery" data-sw="Picha">Gallery</a>
      <a href="#features" data-en="Features" data-sw="Huduma">Features</a>
      <a href="#book" data-en="Availability" data-sw="Nafasi">Availability</a>
      <a href="#faq" data-en="FAQ" data-sw="Maswali">FAQ</a>
      <a href="#contact" data-en="Contact" data-sw="Wasiliana">Contact</a>
      <a href="#book" class="nav-cta nav-cta-mobile" data-en="Book Now" data-sw="Weka Nafasi">Book Now</a>
    </nav>
    <div class="nav-right">
      <button id="lang-toggle" class="lang-btn" type="button">EN / SW</button>
      <a href="#book" class="nav-cta nav-cta-desktop" data-en="Book Now" data-sw="Weka Nafasi">Book Now</a>
      <button id="nav-toggle" class="nav-toggle-btn" type="button" aria-label="Toggle menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<section class="hero">
  <div class="hero-content">
    <p class="eyebrow" data-en="More Than Enough" data-sw="Zaidi Ya Vya Kutosha">More Than Enough</p>
    <h1 data-en-html="A place that feels<br><em>like home.</em>" data-sw-html="Mahali panapohisi<br><em>kama nyumbani.</em>">A place that feels<br><em>like home.</em></h1>
    <p class="lede" data-en="Rest, recharge and rejoice in a spacious, secure 2-bedroom apartment in Bungoma. every detail taken care of, every time." data-sw="Pumzika, jijaze nguvu na furahia nyumba ya vyumba viwili, salama na nafasi Bungoma . kila undani umeshughulikiwa, kila mara.">Rest, recharge and rejoice in a spacious, secure 2-bedroom apartment in Bungoma every detail taken care of, every time.</p>
    <div class="hero-actions">
      <a href="#book" class="btn btn-primary" data-en="Check Availability" data-sw="Angalia Nafasi">Check Availability</a>
      <a href="https://wa.me/<?php echo $whatsapp_number; ?>" target="_blank" class="btn btn-outline" data-en="Chat on WhatsApp" data-sw="Ongea WhatsApp">Chat on WhatsApp</a>
    </div>
  </div>

</section>

<section id="about">
  <div class="container">
    <div class="about-grid">
      <div class="about-img">
        <img src="images/floor2/IMG_7846.jpg" alt="Quattro Homes apartment interior" loading="lazy">
      </div>
      <div class="about-copy">
        <p class="eyebrow" data-en="About Us" data-sw="Kuhusu Sisi">About Us</p>
        <h2 data-en="Quattro Homes" data-sw="Quattro Homes">Quattro Homes</h2>
        <p data-en="Quattro Homes offers exclusive, fully-furnished 2-bedroom apartments in Bungoma, designed for guests who want more than just a place to sleep. Every stay comes with fast WiFi, a fully equipped kitchen, secure parking and a private, peaceful environment . whether you're here for a weekend, a work trip, or a longer stay." data-sw="Quattro Homes hutoa nyumba za kipekee, zenye samani kamili za vyumba viwili Bungoma, zilizoundwa kwa wageni wanaotaka zaidi ya mahali pa kulala tu. Kila ukaaji unakuja na WiFi ya haraka, jiko lililokamilika, maegesho salama na mazingira ya faragha na amani . iwe uko hapa kwa wikendi, safari ya kazi, au ukaaji mrefu.">
          Quattro Homes offers exclusive, fully-furnished 2-bedroom apartments in Bungoma, designed for guests
          who want more than just a place to sleep. Every stay comes with fast WiFi, a fully equipped kitchen,
          secure parking and a private, peaceful environment  whether you're here for a weekend, a work trip,
          or a longer stay.
        </p>
        <div class="about-stats">
          <div><strong><?php echo $avgRating !== null ? $avgRating . '/5' : 'New'; ?></strong><span data-en="Guest rating" data-sw="Ukadiriaji">Guest rating</span></div>
          <div><strong>2</strong><span data-en="Bedrooms" data-sw="Vyumba vya Kulala">Bedrooms</span></div>
          <div><strong>24/7</strong><span data-en="Support" data-sw="Msaada">Support</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="gallery" style="background:var(--cream-dark);">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-en="Gallery" data-sw="Picha">Gallery</p>
      <h2 data-en="A look inside" data-sw="Angalia Ndani">A look inside</h2>
      <p data-en="Two fully-furnished floors. take a look at both before you book." data-sw="Ghorofa mbili zenye samani kamili . angalia zote mbili kabla ya kuweka nafasi.">Two fully-furnished floors . take a look at both before you book.</p>
    </div>

    <div class="gallery-tabs">
      <button type="button" class="gallery-tab active" data-floor="2">Floor 2</button>
      <button type="button" class="gallery-tab" data-floor="1">Floor 1</button>
    </div>

    <div class="gallery-grid" data-floor-panel="2">
      <img src="images/floor2/IMG_7843.jpg" alt="Quattro Homes Floor 2 dining area" loading="lazy">
      <img src="images/floor2/IMG_7846.jpg" alt="Quattro Homes Floor 2 living room" loading="lazy">
      <img src="images/floor2/IMG_7852.jpg" alt="Quattro Homes Floor 2 living room" loading="lazy">
      <img src="images/floor2/IMG_7858.jpg" alt="Quattro Homes Floor 2 living room with TV" loading="lazy">
      <img src="images/floor2/IMG_7833.jpg" alt="Quattro Homes Floor 2 hallway with laundry" loading="lazy">
      <img src="images/floor2/IMG_7869.jpg" alt="Quattro Homes Floor 2 laundry area" loading="lazy">
      <img src="images/floor2/IMG_7837.jpg" alt="Quattro Homes Floor 2 balcony plants" loading="lazy">
      <img src="images/floor2/IMG_7860.jpg" alt="Quattro Homes Floor 2 exercise corner" loading="lazy">
      <img src="images/floor2/IMG_7865.jpg" alt="Quattro Homes Floor 2 bedroom" loading="lazy">
      <img src="images/floor2/IMG_7850.jpg" alt="Quattro Homes Floor 2 bedroom" loading="lazy">
      <img src="images/floor2/IMG_7861.jpg" alt="Quattro Homes Floor 2 kitchen" loading="lazy">
      <img src="images/floor2/IMG_7839.jpg" alt="Quattro Homes Floor 2 living room" loading="lazy">
      <img src="images/floor2/IMG_7874.jpg" alt="Quattro Homes Floor 2 bedroom" loading="lazy">
    </div>

    <div class="gallery-grid" data-floor-panel="1" style="display:none;">
      <img src="images/floor1/IMG_7820.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7797.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7826.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7785.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7808.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7772.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7793.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7811.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7778.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7782.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7834.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
      <img src="images/floor1/IMG_7780.jpg" alt="Quattro Homes Floor 1 apartment" loading="lazy">
    </div>
  </div>
</section>

<section class="features-band" id="features">
  <div class="container">
    <div class="section-head" style="max-width:100%">
      <p class="eyebrow" style="color:var(--gold-light)" data-en="Every Detail. Every Time." data-sw="Kila Undani. Kila Mara.">Every Detail. Every Time.</p>
      <h2 style="color:var(--cream)" data-en="What's included in your stay" data-sw="Kilichojumuishwa katika ukaaji wako">What's included in your stay</h2>
    </div>
    <div class="feature-grid">
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-wifi"></i></div><h4 data-en="Free WiFi" data-sw="WiFi Bure">Free WiFi</h4></div>
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-bed"></i></div><h4 data-en="Spacious 2-Bedroom Apartments" data-sw="Nyumba Nafasi za Vyumba 2">Spacious 2-Bedroom Apartments</h4></div>
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-shield-halved"></i></div><h4 data-en="Secure &amp; Private Environment" data-sw="Mazingira Salama na ya Faragha">Secure &amp; Private Environment</h4></div>
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-kitchen-set"></i></div><h4 data-en="Fully Equipped Kitchen" data-sw="Jiko Lililokamilika">Fully Equipped Kitchen</h4></div>
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-square-parking"></i></div><h4 data-en="Free Parking" data-sw="Maegesho Bure">Free Parking</h4></div>
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-location-dot"></i></div><h4 data-en="Convenient Location" data-sw="Mahali Panapofaa">Convenient Location</h4></div>
    </div>
  </div>
</section>

<section id="suited">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-en="Perfect For" data-sw="Inafaa Kwa">Perfect For</p>
      <h2 data-en="However you're staying, we've got you" data-sw="Vyovyote unavyokaa, tumekushughulikia">However you're staying, we've got you</h2>
      <p data-en="From a weekend of family time to a long academic visit, Quattro Homes adapts to you." data-sw="Kutoka wikendi ya familia hadi ziara ndefu ya kitaaluma, Quattro Homes inakubali mahitaji yako.">From a weekend of family time to a long academic visit, Quattro Homes adapts to you.</p>
    </div>
    <div class="suited-grid">
      <div class="suited-item"><span class="check">&#10003;</span><span data-en="Family time" data-sw="Muda wa Familia">Family time</span></div>
      <div class="suited-item"><span class="check">&#10003;</span><span data-en="Business trips" data-sw="Safari za Kibiashara">Business trips</span></div>
      <div class="suited-item"><span class="check">&#10003;</span><span data-en="Staycations" data-sw="Mapumziko ya Karibu">Staycations</span></div>
      <div class="suited-item"><span class="check">&#10003;</span><span data-en="Medical visits" data-sw="Ziara za Matibabu">Medical visits</span></div>
      <div class="suited-item"><span class="check">&#10003;</span><span data-en="Academic visits" data-sw="Ziara za Kitaaluma">Academic visits</span></div>
      <div class="suited-item"><span class="check">&#10003;</span><span data-en="Long stays" data-sw="Ukaaji Mrefu">Long stays</span></div>
    </div>
  </div>
</section>

<section id="pricing" style="background:var(--cream-dark);">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-en="Simple Pricing" data-sw="Bei Rahisi">Simple Pricing</p>
      <h2 data-en="One rate, everything included" data-sw="Bei Moja, Kila Kitu Kimejumuishwa">One rate, everything included</h2>
    </div>
    <div class="pricing-card">
      <div class="price-amount"><?php echo $currency; ?> <?php echo number_format($price_per_night); ?><span data-en="/ night" data-sw="/ usiku">/ night</span></div>
      <ul class="price-includes">
        <li data-en="Free WiFi &amp; parking" data-sw="WiFi na maegesho bure">Free WiFi &amp; parking</li>
        <li data-en="Fully equipped kitchen" data-sw="Jiko lililokamilika">Fully equipped kitchen</li>
        <li data-en="Secure, private compound" data-sw="Ua salama na la faragha">Secure, private compound</li>
        <li data-en="No hidden fees" data-sw="Hakuna ada zilizofichwa">No hidden fees</li>
      </ul>
      <p class="price-note" data-en="Minimum stay: <?php echo $min_stay; ?> night(s). Final total is calculated automatically in the booking form below." data-sw="Ukaaji wa chini: usiku <?php echo $min_stay; ?>. Jumla ya mwisho inahesabiwa kiotomatiki kwenye fomu ya kuweka nafasi hapa chini.">Minimum stay: <?php echo $min_stay; ?> night(s). Final total is calculated automatically in the booking form below.</p>
      <?php if ((float)$settings['discount_percent'] > 0): ?>
      <p class="price-discount-badge" data-en="Stay <?php echo (int)$settings['discount_min_nights']; ?>+ nights and get <?php echo (float)$settings['discount_percent']; ?>% off" data-sw="Kaa usiku <?php echo (int)$settings['discount_min_nights']; ?>+ na upate punguzo la <?php echo (float)$settings['discount_percent']; ?>%">Stay <?php echo (int)$settings['discount_min_nights']; ?>+ nights and get <?php echo (float)$settings['discount_percent']; ?>% off</p>
      <?php endif; ?>
    </div>
  </div>
</section>



<section id="testimonials">
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
          <span class="testimonial-name">. <?php echo htmlspecialchars($r['full_name']); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
      <p style="text-align:center;color:#888;" data-en="No reviews yet, be the first to share your stay!" data-sw="Hakuna maoni bado . kuwa wa kwanza kushiriki ukaaji wako!">No reviews yet . be the first to share your stay!</p>
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
            <textarea id="comment" name="comment" placeholder="Tell future guests about your stay..."></textarea>
          </div>
          <button type="submit" class="submit-btn" id="review-submit-btn" data-en="Submit Review" data-sw="Tuma Maoni">Submit Review</button>
          <div id="review-feedback"></div>
        </form>
      </details>
    </div>
  </div>
</section>

<section class="booking-section" id="book">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-en="Book Your Stay Today" data-sw="Weka Nafasi Leo">Book Your Stay Today</p>
      <h2 data-en="Rest. Recharge. Rejoice." data-sw="Pumzika. Jijaze Nguvu. Furahia.">Rest. Recharge. Rejoice.</h2>
      <p data-en="Check the calendar below, then send your booking request. We'll confirm by phone or WhatsApp within a few hours." data-sw="Angalia kalenda hapa chini, kisha tuma ombi lako la kuweka nafasi. Tutathibitisha kwa simu au WhatsApp ndani ya masaa machache.">Check the calendar below, then send your booking request. We'll confirm by phone or WhatsApp within a few hours.</p>
    </div>

    <div class="booking-grid">
      <div class="card">
        <h3 style="margin-top:0;" data-en="Booking Request" data-sw="Ombi la Kuweka Nafasi">Booking Request</h3>
        <form id="booking-form" autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
          <!-- Honeypot field: hidden from real users via CSS, bots tend to fill it -->
          <div class="hp-field" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
          </div>

          <div class="field">
            <label for="floor" data-en="Which unit? *" data-sw="Ghorofa gani? *">Which unit? *</label>
            <select id="floor" name="floor" required>
              <option value="floor2">Floor 2</option>
              <option value="floor1">Floor 1</option>
              <option value="both">Both floors (whole house)</option>
            </select>
          </div>
          <div class="form-row">
            <div class="field">
              <label for="full_name" data-en="Full Name *" data-sw="Jina Kamili *">Full Name *</label>
              <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="field">
              <label for="phone" data-en="Phone Number *" data-sw="Nambari ya Simu *">Phone Number *</label>
              <input type="tel" id="phone" name="phone" placeholder="e.g. 07XX XXX XXX" required>
            </div>
          </div>
          <div class="field">
            <label for="email" data-en="Email Address *" data-sw="Barua Pepe *">Email Address *</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-row">
            <div class="field">
              <label for="checkin_date" data-en="Check-in *" data-sw="Kuingia *">Check-in *</label>
              <input type="date" id="checkin_date" name="checkin_date" required>
            </div>
            <div class="field">
              <label for="checkout_date" data-en="Check-out *" data-sw="Kutoka *">Check-out *</label>
              <input type="date" id="checkout_date" name="checkout_date" required>
            </div>
          </div>
          <div class="form-row">
            <div class="field">
              <label for="guests" data-en="Guests" data-sw="Wageni">Guests</label>
              <input type="number" id="guests" name="guests" min="1" value="2">
            </div>
            <div class="field">
              <label for="purpose" data-en="Purpose of Stay" data-sw="Kusudi la Ukaaji">Purpose of Stay</label>
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
            <label for="message" data-en="Message (optional)" data-sw="Ujumbe (hiari)">Message (optional)</label>
            <textarea id="message" name="message" placeholder="Anything else we should know?"></textarea>
          </div>
          <div class="field" id="referral-field-wrap">
            <label for="referral_code" data-en="Referral Code (optional)" data-sw="Nambari ya Rufaa (hiari)">Referral Code (optional)</label>
            <input type="text" id="referral_code" name="referral_code" placeholder="e.g. QH1023">
          </div>
          <div id="referral-applied-note" style="display:none;" class="referral-applied-note">
            <i class="fa-solid fa-circle-check"></i>
            <span data-en="Referral code applied . your discount will show in the total below." data-sw="Msimbo wa rufaa umetumika . punguzo lako litaonekana kwenye jumla hapa chini.">Referral code applied . your discount will show in the total below.</span>
          </div>

          <div id="price-summary" class="price-summary" style="display:none;">
            <span id="price-nights"></span>
            <strong id="price-total"></strong>
          </div>

          <button type="submit" class="submit-btn" id="submit-btn" data-en="Request to Book" data-sw="Omba Kuweka Nafasi">Request to Book</button>
          <div id="form-feedback"></div>
        </form>
      </div>

      <div class="card calendar-card">
        <h3 data-en="Availability" data-sw="Nafasi Zilizopo">Availability</h3>
        <div class="cal-floor-tabs" id="cal-floor-tabs">
          <button type="button" class="cal-floor-tab active" data-cal-floor="floor2">Floor 2</button>
          <button type="button" class="cal-floor-tab" data-cal-floor="floor1">Floor 1</button>
          <button type="button" class="cal-floor-tab" data-cal-floor="both">Both</button>
        </div>
        <div class="cal-nav">
          <button type="button" id="cal-prev" aria-label="Previous month">&#8249;</button>
          <span class="cal-month-label" id="cal-month-label"></span>
          <button type="button" id="cal-next" aria-label="Next month">&#8250;</button>
        </div>
        <div class="cal-grid" id="cal-grid"></div>
        <div class="legend">
          <span><i class="i-avail"></i><span data-en="Available" data-sw="Ipo">Available</span></span>
          <span><i class="i-booked"></i><span data-en="Booked" data-sw="Imechukuliwa">Booked</span></span>
        </div>
        <p style="font-size:0.75rem;color:#999;margin:10px 0 0;" data-en="Showing availability for the unit selected above. &ldquo;Both&rdquo; shows dates where neither floor is free." data-sw="Inaonyesha nafasi za ghorofa uliyochagua hapo juu. &ldquo;Zote mbili&rdquo; inaonyesha tarehe ambazo hakuna ghorofa iliyo huru.">Showing availability for the unit selected above. "Both" shows dates where neither floor is free.</p>
      </div>
    </div>
  </div>
</section>

<section id="rules" style="background:var(--cream-dark);">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-en="Good to Know" data-sw="Vizuri Kujua">Good to Know</p>
      <h2 data-en="House rules" data-sw="Kanuni za Nyumba">House rules</h2>
    </div>
    <div class="rules-grid">
      <div class="rule-item"><strong data-en="Check-in" data-sw="Kuingia">Check-in</strong><span>2:00 PM . 8:00 PM</span></div>
      <div class="rule-item"><strong data-en="Check-out" data-sw="Kutoka">Check-out</strong><span>10:00 AM</span></div>
      <div class="rule-item"><strong data-en="No smoking indoors" data-sw="Hakuna kuvuta sigara ndani">No smoking indoors</strong><span data-en="Designated outdoor area available" data-sw="Eneo la nje limetengwa">Designated outdoor area available</span></div>
      <div class="rule-item"><strong data-en="No parties/events" data-sw="Hakuna sherehe">No parties/events</strong><span data-en="Quiet hours after 10 PM" data-sw="Masaa ya utulivu baada ya saa 4 usiku">Quiet hours after 10 PM</span></div>
      <div class="rule-item"><strong data-en="Pets" data-sw="Wanyama kipenzi">Pets</strong><span data-en="On request only, please ask first" data-sw="Kwa ombi tu, tafadhali uliza kwanza">On request only, please ask first</span></div>
      <div class="rule-item"><strong data-en="Cancellations" data-sw="Kufuta Nafasi">Cancellations</strong><span data-en="Free cancellation up to 48 hours before check-in" data-sw="Kufuta bure hadi masaa 48 kabla ya kuingia">Free cancellation up to 48 hours before check-in</span></div>
    </div>
  </div>
</section>

<section id="faq">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-en="Questions" data-sw="Maswali">Questions</p>
      <h2 data-en="Frequently asked questions" data-sw="Maswali Yanayoulizwa Mara kwa Mara">Frequently asked questions</h2>
    </div>
    <div class="faq-list">
      <details class="faq-item">
        <summary data-en="Is parking available on site?" data-sw="Je, kuna maegesho pale pale?">Is parking available on site?</summary>
        <p data-en="Yes.free, secure parking is included with every stay." data-sw="Ndiyo . maegesho salama na bure yamejumuishwa kwenye kila ukaaji.">Yes . free, secure parking is included with every stay.</p>
      </details>
      <details class="faq-item">
        <summary data-en="Can I bring extra guests beyond what I booked for?" data-sw="Naweza kuleta wageni wa ziada zaidi ya nilivyoweka?">Can I bring extra guests beyond what I booked for?</summary>
        <p data-en="Please let us know your total guest count when booking so we can confirm comfortably." data-sw="Tafadhali tujulishe idadi ya wageni wote wakati wa kuweka nafasi ili tuweze kuthibitisha vizuri.">Please let us know your total guest count when booking so we can confirm comfortably.</p>
      </details>
      <details class="faq-item">
        <summary data-en="What is the cancellation policy?" data-sw="Sera ya kufuta nafasi ni ipi?">What is the cancellation policy?</summary>
        <p data-en="Free cancellation up to 48 hours before check-in. After that, one night's fee may apply." data-sw="Kufuta bure hadi masaa 48 kabla ya kuingia. Baada ya hapo, ada ya usiku mmoja inaweza kutumika.">Free cancellation up to 48 hours before check-in. After that, one night's fee may apply.</p>
      </details>
      <details class="faq-item">
        <summary data-en="Do you offer long-stay discounts?" data-sw="Je, mnatoa punguzo la ukaaji mrefu?">Do you offer long-stay discounts?</summary>
        <p data-en="Yes, message us on WhatsApp for stays of 2 weeks or longer." data-sw="Ndiyo, tutumie ujumbe WhatsApp kwa ukaaji wa wiki 2 au zaidi.">Yes, message us on WhatsApp for stays of 2 weeks or longer.</p>
      </details>
    </div>
  </div>
</section>

<section class="contact-section" id="contact">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" style="color:var(--gold-light)" data-en="Get in Touch" data-sw="Wasiliana Nasi">Get in Touch</p>
      <h2 style="color:var(--cream)" data-en="We're here to help." data-sw="Tuko hapa kukusaidia.">We're here to help.</h2>
      <p style="color:rgba(246,240,226,0.75)" data-en="Reach out any time . we'll take care of the rest." data-sw="Wasiliana wakati wowote . tutashughulikia mengine.">Reach out any time . we'll take care of the rest.</p>
    </div>
    <div class="contact-grid">
      <div class="contact-card">
        <div class="icon"><i class="fa-solid fa-phone"></i></div>
        <h4 data-en="Call or WhatsApp" data-sw="Piga Simu au WhatsApp">Call or WhatsApp</h4>
        <p><a class="whatsapp-link" href="https://wa.me/<?php echo $whatsapp_number; ?>" target="_blank"><?php echo $whatsapp_display; ?></a></p>
      </div>
      <div class="contact-card">
        <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
        <h4 data-en="Location" data-sw="Mahali">Location</h4>
        <p><?php echo $location; ?></p>
      </div>
      <div class="contact-card">
        <div class="icon"><i class="fa-solid fa-envelope"></i></div>
        <h4 data-en="Follow Us" data-sw="Tufuate">Follow Us</h4>
        <p><a href="#">Facebook</a> &middot; <a href="#">Instagram</a> &middot; <a href="https://wa.me/<?php echo $whatsapp_number; ?>" target="_blank">WhatsApp</a></p>
      </div>
    </div>
    <div class="map-embed">
      <iframe
        src="https://www.google.com/maps?q=Bungoma,Kenya&output=embed"
        width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade" title="Quattro Homes location map"></iframe>
    </div>
  </div>
</section>

<footer>
  <div class="container footer-inner">
    <span data-en="&copy; <?php echo date('Y'); ?> Quattro Homes.Feel at home. Stay in comfort. Return with a smile." data-sw="&copy; <?php echo date('Y'); ?> Quattro Homes &mdash; Jihisi nyumbani. Kaa vizuri. Rudi na tabasamu.">&copy; <?php echo date('Y'); ?> Quattro Homes.Feel at home. Stay in comfort. Return with a smile.</span>
    <span class="socials">
      <a href="#">Facebook</a>
      <a href="#">Instagram</a>
      <a href="https://wa.me/<?php echo $whatsapp_number; ?>" target="_blank">WhatsApp</a>
      
    </span>
  </div>
</footer>

<a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo urlencode('Hi Quattro Homes, I have a question about booking a stay.'); ?>"
   target="_blank" class="whatsapp-float" aria-label="Chat with us on WhatsApp">
  <i class="fa-brands fa-whatsapp"></i>
</a>

<script>
  window.QUATTRO_PRICE_PER_NIGHT = <?php echo json_encode($price_per_night); ?>;
  window.QUATTRO_CURRENCY = <?php echo json_encode($currency); ?>;
  window.QUATTRO_MIN_STAY = <?php echo json_encode($min_stay); ?>;
  window.QUATTRO_DISCOUNT_PERCENT = <?php echo json_encode((float)$settings['discount_percent']); ?>;
  window.QUATTRO_DISCOUNT_MIN_NIGHTS = <?php echo json_encode((int)$settings['discount_min_nights']); ?>;
  window.QUATTRO_INCLUDED_GUESTS = <?php echo json_encode((int)$settings['included_guests']); ?>;
  window.QUATTRO_EXTRA_GUEST_FEE = <?php echo json_encode((float)$settings['extra_guest_fee']); ?>;
</script>
<script src="js/script.js"></script>
</body>
</html>
