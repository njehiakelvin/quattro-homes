<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';

$settings = getSettings();
$token = csrf_token();
$currency = $settings['currency'];

$pageTitle = 'Book Your Stay | Quattro Homes Bungoma';
$pageDescription = 'Check live availability and book Floor 1, Floor 2, or the whole house at Quattro Homes in Bungoma, Kenya.';
$canonicalPath = 'book.php';
$activePage = 'book';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<section class="booking-section" style="padding-top:70px;">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-en="Book Your Stay Today" data-sw="Weka Nafasi Leo">Book Your Stay Today</p>
      <h2 data-en="Rest. Recharge. Rejoice." data-sw="Pumzika. Jijaze Nguvu. Furahia.">Rest. Recharge. Rejoice.</h2>
      <p data-en="Choose your unit, check the calendar below, then send your booking request. We'll confirm by phone or WhatsApp within minutes." data-sw="Chagua kitengo chako, angalia kalenda hapa chini, kisha tuma ombi lako la kuweka nafasi. Tutathibitisha kwa simu au WhatsApp ndani ya masaa machache.">Choose your unit, check the calendar below, then send your booking request. We'll confirm by phone or WhatsApp within minutes.</p>
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
            <span data-en="Referral code applied. Your discount will show in the total below." data-sw="Msimbo wa rufaa umetumika. Punguzo lako litaonekana kwenye jumla hapa chini.">Referral code applied. Your discount will show in the total below.</span>
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
        <div class="cal-grid-wrap">
          <div class="cal-grid" id="cal-grid"></div>
          <div class="cal-loading" id="cal-loading" style="display:none;"><span class="spinner dark"></span></div>
        </div>
        <div class="legend">
          <span><i class="i-avail"></i><span data-en="Available" data-sw="Ipo">Available</span></span>
          <span><i class="i-booked"></i><span data-en="Booked" data-sw="Imechukuliwa">Booked</span></span>
        </div>
        <p style="font-size:0.75rem;color:#999;margin:10px 0 0;" data-en="Showing availability for the unit selected above. &ldquo;Both&rdquo; shows dates where neither floor is free." data-sw="Inaonyesha nafasi za ghorofa uliyochagua hapo juu. &ldquo;Zote mbili&rdquo; inaonyesha tarehe ambazo hakuna ghorofa iliyo huru.">Showing availability for the unit selected above. "Both" shows dates where neither floor is free.</p>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/foot.php'; ?>
