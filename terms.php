<?php
require_once __DIR__ . '/includes/settings.php';
$settings    = getSettings();
$pageTitle   = 'Terms & Conditions | Quattro Homes';
$pageDescription = 'Terms and conditions, privacy policy and cookie policy for Quattro Homes Bungoma.';
$canonicalPath = 'terms.php';
$activePage  = 'terms';
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/includes/head.php'; ?></head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>
<section style="padding:80px 0 100px;">
  <div class="container" style="max-width:780px;">
    <p class="eyebrow">Legal</p>
    <h1 style="font-size:2.4rem;margin-bottom:32px;">Terms &amp; Conditions</h1>
    <div class="prose">

      <h2>1. Booking &amp; Reservation</h2>
      <p>All bookings are subject to availability. A booking is confirmed only after written confirmation (via WhatsApp or email) from Quattro Homes. We reserve the right to decline any booking request.</p>

      <h2>2. Payment</h2>
      <p>Payment details will be provided upon booking confirmation. Full or partial payment may be required to secure your reservation as communicated at the time of confirmation.</p>

      <h2>3. Cancellations &amp; Refunds</h2>
      <p>Cancellations made more than 48 hours before check-in will receive a full refund. Cancellations within 48 hours of check-in are non-refundable. No-shows will be charged the full booking amount.</p>

      <h2>4. Check-in &amp; Check-out</h2>
      <p>Standard check-in is from 2:00 PM and check-out is by 11:00 AM. Early check-in or late check-out may be available on request and subject to availability.</p>

      <h2>5. House Rules</h2>
      <p>Guests are expected to treat the property with respect. Loud noise, parties, or gatherings not disclosed at the time of booking are prohibited. Any damage to the property will be charged to the guest.</p>

      <h2>6. Liability</h2>
      <p>Quattro Homes is not liable for loss or damage to guests' personal property. Guests stay at their own risk. We make every effort to ensure the property is safe and well-maintained.</p>

      <h2 id="privacy">7. Privacy Policy</h2>
      <p>We collect personal information (name, email, phone) solely for the purpose of processing your booking and communicating with you. We do not sell or share your information with third parties. Your data is stored securely and retained only as long as necessary.</p>

      <h2 id="cookies">8. Cookie Policy</h2>
      <p>Our website uses essential cookies to ensure the site functions correctly (e.g. form security tokens). We do not use advertising or tracking cookies. By using our website you consent to our use of essential cookies. You may disable cookies in your browser settings, though this may affect certain site functionality.</p>

      <h2>9. Changes to These Terms</h2>
      <p>We reserve the right to update these terms at any time. Continued use of the website or services constitutes acceptance of the updated terms.</p>

      <h2>10. Contact</h2>
      <p>For questions about these terms, please <a href="contact.php" class="terms-link">contact us</a> or reach out on WhatsApp.</p>

      <p style="margin-top:32px;font-size:0.82rem;color:#999;">Last updated: <?php echo date('F Y'); ?></p>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/foot.php'; ?>
