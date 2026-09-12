<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';

$settings = getSettings();
$token = csrf_token();
$currency = $settings['currency'];
$price_per_night = (float)$settings['price_per_night'];
$min_stay = (int)$settings['min_stay_nights'];

$pageTitle = 'Pricing | Quattro Homes Bungoma';
$pageDescription = "Simple, transparent pricing for Quattro Homes apartments in Bungoma, Kenya. From {$currency} " . number_format($price_per_night) . ' per night, with no hidden fees.';
$canonicalPath = 'pricing.php';
$activePage = 'pricing';
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
      <p class="price-note" data-en="Minimum stay: <?php echo $min_stay; ?> night(s). Booking both floors is priced as two units. Final total is calculated automatically on the booking page." data-sw="Ukaaji wa chini: usiku <?php echo $min_stay; ?>. Kubook ghorofa zote mbili kunahesabiwa kama vitengo viwili. Jumla ya mwisho inahesabiwa kiotomatiki kwenye ukurasa wa kuweka nafasi.">Minimum stay: <?php echo $min_stay; ?> night(s). Booking both floors is priced as two units. Final total is calculated automatically on the booking page.</p>
      <?php if ((float)$settings['discount_percent'] > 0): ?>
      <p class="price-discount-badge" data-en="Stay <?php echo (int)$settings['discount_min_nights']; ?>+ nights and get <?php echo (float)$settings['discount_percent']; ?>% off" data-sw="Kaa usiku <?php echo (int)$settings['discount_min_nights']; ?>+ na upate punguzo la <?php echo (float)$settings['discount_percent']; ?>%">Stay <?php echo (int)$settings['discount_min_nights']; ?>+ nights and get <?php echo (float)$settings['discount_percent']; ?>% off</p>
      <?php endif; ?>
      <div style="margin-top:26px;">
        <a href="book.php" class="btn btn-primary" style="width:100%;text-align:center;" data-en="Check Availability" data-sw="Angalia Nafasi">Check Availability</a>
      </div>
    </div>
  </div>
</section>

<section id="referral" class="bg-subtle-section">
  <div class="container">
    <div class="referral-card">
      <div class="referral-icon"><i class="fa-solid fa-gift"></i></div>
      <div>
        <h3 data-en="Refer a friend, you both save" data-sw="Alika rafiki, nyote mnaokoa">Refer a friend, you both save</h3>
        <p data-en="After your stay, we'll send you a personal referral link. Share it, when a friend books through it, <?php echo (float)$settings['referral_discount_percent']; ?>% off is applied automatically for them, and once their stay is complete, you'll get a <?php echo (float)$settings['referral_reward_percent']; ?>% discount code for your next one." data-sw="Baada ya ukaaji wako, tutakutumia kiungo chako binafsi cha rufaa. Kishiriki, rafiki yako anapobook kupitia hicho, punguzo la <?php echo (float)$settings['referral_discount_percent']; ?>% linatumika kiotomatiki, na baada ya ukaaji wao kukamilika, utapata punguzo la <?php echo (float)$settings['referral_reward_percent']; ?>% kwa ukaaji wako ujao.">After your stay, we'll send you a personal referral link. Share it, when a friend books through it, <?php echo (float)$settings['referral_discount_percent']; ?>% off is applied automatically for them, and once their stay is complete, you'll get a <?php echo (float)$settings['referral_reward_percent']; ?>% discount code for your next one.</p>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/foot.php'; ?>
