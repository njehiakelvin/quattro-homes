<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';

$settings = getSettings();
$token = csrf_token();

$pageTitle = 'House Rules & FAQ | Quattro Homes Bungoma';
$pageDescription = 'Check-in and check-out times, house rules, and frequently asked questions for staying at Quattro Homes in Bungoma, Kenya.';
$canonicalPath = 'faq.php';
$activePage = 'faq';
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
      <p class="eyebrow" data-en="Good to Know" data-sw="Vizuri Kujua">Good to Know</p>
      <h2 data-en="House rules" data-sw="Kanuni za Nyumba">House rules</h2>
    </div>
    <div class="rules-grid">
      <div class="rule-item"><strong data-en="Check-in" data-sw="Kuingia">Check-in</strong><span>2:00 PM &ndash; 8:00 PM</span></div>
      <div class="rule-item"><strong data-en="Check-out" data-sw="Kutoka">Check-out</strong><span>10:00 AM</span></div>
      <div class="rule-item"><strong data-en="No smoking indoors" data-sw="Hakuna kuvuta sigara ndani">No smoking indoors</strong><span data-en="Designated outdoor area available" data-sw="Eneo la nje limetengwa">Designated outdoor area available</span></div>
      <div class="rule-item"><strong data-en="No parties or events" data-sw="Hakuna sherehe">No parties or events</strong><span data-en="Quiet hours after 10 PM" data-sw="Masaa ya utulivu baada ya saa 4 usiku">Quiet hours after 10 PM</span></div>
      <div class="rule-item"><strong data-en="Pets" data-sw="Wanyama kipenzi">Pets</strong><span data-en="On request only, please ask first" data-sw="Kwa ombi tu, tafadhali uliza kwanza">On request only, please ask first</span></div>
      <div class="rule-item"><strong data-en="Cancellations" data-sw="Kufuta Nafasi">Cancellations</strong><span data-en="Free cancellation up to 48 hours before check-in" data-sw="Kufuta bure hadi masaa 48 kabla ya kuingia">Free cancellation up to 48 hours before check-in</span></div>
    </div>
  </div>
</section>

<section class="bg-subtle-section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-en="Questions" data-sw="Maswali">Questions</p>
      <h2 data-en="Frequently asked questions" data-sw="Maswali Yanayoulizwa Mara kwa Mara">Frequently asked questions</h2>
    </div>
    <div class="faq-list">
      <details class="faq-item">
        <summary data-en="Is parking available on site?" data-sw="Je, kuna maegesho pale pale?">Is parking available on site?</summary>
        <p data-en="Yes. Free, secure parking is included with every stay." data-sw="Ndiyo. Maegesho salama na bure yamejumuishwa kwenye kila ukaaji.">Yes. Free, secure parking is included with every stay.</p>
      </details>
      <details class="faq-item">
        <summary data-en="Can I book just one floor, or the whole house?" data-sw="Naweza kubook ghorofa moja tu, au nyumba nzima?">Can I book just one floor, or the whole house?</summary>
        <p data-en="Both. Floor 1 and Floor 2 are independent apartments, so you can book either one, or both together if your group needs the extra space." data-sw="Zote mbili. Ghorofa ya 1 na ya 2 ni nyumba huru, kwa hivyo unaweza kubook moja, au zote mbili pamoja ikiwa kikundi chako kinahitaji nafasi zaidi.">Both. Floor 1 and Floor 2 are independent apartments, so you can book either one, or both together if your group needs the extra space.</p>
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
    <div class="hero-actions" style="margin-top:40px;">
      <a href="book.php" class="btn btn-primary" data-en="Check Availability" data-sw="Angalia Nafasi">Check Availability</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/foot.php'; ?>
