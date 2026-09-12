<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';

$settings = getSettings();
$token = csrf_token();
$prefillBooking = isset($_GET['booking']) && ctype_digit($_GET['booking']) ? $_GET['booking'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Report an Issue | Quattro Homes</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
  <div class="nav-inner">
    <a href="index.php" class="brand" style="text-decoration:none;">
      <div class="mark">Q</div>
      <div class="name">Quattro Homes<small>Exclusive Stays &middot; Exceptional Comfort</small></div>
    </a>
  </div>
</header>

<section style="padding-top:60px;">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Currently Staying or Recently Stayed?</p>
      <h2>Report an issue</h2>
      <p>Booking problem, billing question, or something at the property that needs attention. Let us know and we'll follow up promptly.</p>
    </div>
    <form id="issue-form" class="card" style="max-width:640px;margin:0 auto;" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
      <div class="hp-field" aria-hidden="true">
        <label for="issue_website">Website</label>
        <input type="text" id="issue_website" name="website" tabindex="-1" autocomplete="off">
      </div>
      <div class="form-row">
        <div class="field">
          <label for="issue_name">Full Name *</label>
          <input type="text" id="issue_name" name="full_name" required>
        </div>
        <div class="field">
          <label for="issue_email">Email *</label>
          <input type="email" id="issue_email" name="email" required>
        </div>
      </div>
      <div class="form-row">
        <div class="field">
          <label for="issue_phone">Phone (optional)</label>
          <input type="tel" id="issue_phone" name="phone">
        </div>
        <div class="field">
          <label for="issue_booking_ref">Booking Reference *</label>
          <input type="text" id="issue_booking_ref" name="booking_ref" placeholder="From your confirmation" value="<?php echo htmlspecialchars($prefillBooking); ?>" required>
        </div>
      </div>
      <div class="field">
        <label for="issue_category">Category</label>
        <select id="issue_category" name="category">
          <option value="booking">Booking issue</option>
          <option value="billing">Billing / payment</option>
          <option value="property">Property / maintenance</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="field">
        <label for="issue_description">Describe the issue *</label>
        <textarea id="issue_description" name="description" required placeholder="Please share as much detail as you can..."></textarea>
      </div>
      <button type="submit" class="submit-btn" id="issue-submit-btn">Submit Report</button>
      <div id="issue-feedback"></div>
    </form>
    <p style="text-align:center;margin-top:20px;">
      <a href="https://wa.me/<?php echo htmlspecialchars($settings['whatsapp_number']); ?>" target="_blank" style="font-size:0.9rem;">
        <i class="fa-brands fa-whatsapp"></i> Prefer WhatsApp? Message us directly
      </a>
    </p>
  </div>
</section>

<footer style="margin-top:60px;">
  <div class="container footer-inner">
    <span>&copy; <?php echo date('Y'); ?> Quattro Homes</span>
  </div>
</footer>

<script>
document.getElementById('issue-form').addEventListener('submit', function (e) {
  e.preventDefault();
  const feedback = document.getElementById('issue-feedback');
  const btn = document.getElementById('issue-submit-btn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>Sending...';
  feedback.className = '';
  feedback.style.display = 'none';

  fetch('report_issue.php', { method: 'POST', body: new FormData(this) })
    .then(res => res.json())
    .then(data => {
      feedback.textContent = data.message;
      feedback.className = data.success ? 'success' : 'error';
      if (data.success) this.reset();
    })
    .catch(() => {
      feedback.textContent = 'Network error. Please try again or WhatsApp us directly.';
      feedback.className = 'error';
    })
    .finally(() => {
      btn.disabled = false;
      btn.textContent = 'Submit Report';
    });
});
</script>
</body>
</html>
