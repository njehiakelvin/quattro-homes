<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

$settings        = getSettings();
$token           = csrf_token();
$whatsapp_number = $settings['whatsapp_number'];
$whatsapp_display = '+' . substr($whatsapp_number,0,3) . ' ' . substr($whatsapp_number,3,3) . ' ' . substr($whatsapp_number,6,3) . ' ' . substr($whatsapp_number,9);
$mapQuery        = 'HHM6+XQ9, Bungoma';

$pageTitle       = 'Contact & FAQ | Quattro Homes Bungoma';
$pageDescription = 'Get in touch with Quattro Homes in Bungoma, Kenya. WhatsApp, email, map location, and frequently asked questions all in one place.';
$canonicalPath   = 'contact.php';
$activePage      = 'contact';
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/includes/head.php'; ?></head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<!-- PAGE HEADER -->
<div class="page-header bg-forest">
  <div class="container">
    <p class="eyebrow" style="color:var(--gold-light);">Get in Touch</p>
    <h1>We're here for you.</h1>
    <p>Reach out any time. We respond fast on WhatsApp.</p>
  </div>
</div>

<!-- WHATSAPP PRIMARY CTA -->
<section class="wa-cta-section">
  <div class="container">
    <div class="wa-cta-card">
      <div class="wa-cta-icon"><i class="fa-brands fa-whatsapp"></i></div>
      <div class="wa-cta-body">
        <h3>WhatsApp is the fastest way to reach us</h3>
        <p>Questions about availability, directions, or a specific request. Just message us directly. We typically reply within minutes.</p>
        <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo urlencode('Hi Quattro Homes, I have a question about booking a stay.'); ?>"
           target="_blank" class="btn btn-wa">
          <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
        </a>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT CARDS -->
<section class="contact-cards-section bg-subtle-section">
  <div class="container">
    <div class="contact-info-grid">
      <div class="contact-info-card">
        <div class="cic-icon"><i class="fa-solid fa-phone"></i></div>
        <h4>Call Us</h4>
        <p><?php echo $whatsapp_display; ?></p>
        <a href="tel:+<?php echo $whatsapp_number; ?>" class="cic-link">Call now</a>
      </div>
      <div class="contact-info-card">
        <div class="cic-icon"><i class="fa-regular fa-envelope"></i></div>
        <h4>Email</h4>
        <p><?php echo htmlspecialchars($settings['contact_email'] ?? 'info@quattrohomes.co.ke'); ?></p>
        <a href="mailto:<?php echo htmlspecialchars($settings['contact_email'] ?? 'info@quattrohomes.co.ke'); ?>" class="cic-link">Send email</a>
      </div>
      <div class="contact-info-card">
        <div class="cic-icon"><i class="fa-solid fa-location-dot"></i></div>
        <h4>Location</h4>
        <p>Bungoma, Kenya</p>
        <a href="https://maps.google.com/?q=<?php echo urlencode($mapQuery); ?>" target="_blank" class="cic-link">Open in Maps</a>
      </div>
      <div class="contact-info-card">
        <div class="cic-icon"><i class="fa-regular fa-clock"></i></div>
        <h4>Response Hours</h4>
        <p>7 AM to 10 PM daily</p>
        <span class="cic-link" style="cursor:default;">Usually within minutes</span>
      </div>
    </div>
  </div>
</section>

<!-- MAP -->
<section style="padding:0;">
  <iframe
    src="https://www.google.com/maps?q=<?php echo urlencode($mapQuery); ?>&output=embed"
    width="100%" height="380" style="border:0;display:block;" allowfullscreen="" loading="lazy"
    referrerpolicy="no-referrer-when-downgrade" title="Quattro Homes location"></iframe>
</section>

<!-- FAQ -->
<section class="faq-section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">FAQ</p>
      <h2>Frequently asked questions</h2>
      <p>Everything you need to know before you book.</p>
    </div>

    <div class="faq-two-col">
      <div class="faq-list">
        <details class="faq-item">
          <summary>What's included in every stay?</summary>
          <p>Free WiFi, free secure parking, fully equipped kitchen, hot water, Smart TV, and 24/7 WhatsApp support. No hidden charges.</p>
        </details>
        <details class="faq-item">
          <summary>Can I book just one floor or the whole house?</summary>
          <p>Both options are available. Floor 1 and Floor 2 are fully independent apartments. Book one or take both for a larger group.</p>
        </details>
        <details class="faq-item">
          <summary>What are check-in and check-out times?</summary>
          <p>Check-in is from 2:00 PM. Check-out is by 10:00 AM. Early check-in or late check-out may be arranged on request, subject to availability.</p>
        </details>
        <details class="faq-item">
          <summary>What is the cancellation policy?</summary>
          <p>Free cancellation up to 48 hours before check-in. Cancellations within 48 hours may incur a one-night fee. No-shows are charged in full.</p>
        </details>
        <details class="faq-item">
          <summary>Is parking available?</summary>
          <p>Yes. Free, secure, on-site parking is included with every booking. No need to arrange anything separately.</p>
        </details>
      </div>
      <div class="faq-list">
        <details class="faq-item">
          <summary>Do you allow pets?</summary>
          <p>Pets are considered on a case-by-case basis. Please message us on WhatsApp before booking so we can confirm.</p>
        </details>
        <details class="faq-item">
          <summary>Are parties or events allowed?</summary>
          <p>No. Quiet hours are from 10 PM. Undisclosed gatherings may result in the stay being ended without a refund.</p>
        </details>
        <details class="faq-item">
          <summary>Do you offer discounts for long stays?</summary>
          <p>Yes. Stays of 7+ nights automatically get a discount. Check the pricing page for details. For 2 weeks or more, message us for a custom rate.</p>
        </details>
        <details class="faq-item">
          <summary>How do I confirm my booking?</summary>
          <p>Submit the booking form or message us on WhatsApp. We'll confirm availability and send payment instructions within minutes.</p>
        </details>
        <details class="faq-item">
          <summary>Is smoking allowed?</summary>
          <p>Smoking is not allowed indoors. A designated outdoor area is available.</p>
        </details>
      </div>
    </div>


  </div>
</section>

<?php include __DIR__ . '/includes/foot.php'; ?>
