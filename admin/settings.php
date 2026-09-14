<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/settings.php';
require_admin_login();

$pdo = getDB();
$settings = getSettings();

$settingsSaved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $updates = [
        'price_per_night' => max(0, (float)($_POST['price_per_night'] ?? 0)),
        'min_stay_nights' => max(1, (int)($_POST['min_stay_nights'] ?? 1)),
        'discount_percent' => min(100, max(0, (float)($_POST['discount_percent'] ?? 0))),
        'discount_min_nights' => max(1, (int)($_POST['discount_min_nights'] ?? 1)),
        'included_guests' => max(1, (int)($_POST['included_guests'] ?? 2)),
        'extra_guest_fee' => max(0, (float)($_POST['extra_guest_fee'] ?? 0)),
        'referral_discount_percent' => min(100, max(0, (float)($_POST['referral_discount_percent'] ?? 0))),
        'referral_reward_percent' => min(100, max(0, (float)($_POST['referral_reward_percent'] ?? 0))),
        'whatsapp_access_token' => trim($_POST['whatsapp_access_token'] ?? ''),
        'whatsapp_phone_number_id' => trim($_POST['whatsapp_phone_number_id'] ?? ''),
        'owner_whatsapp_number' => trim($_POST['owner_whatsapp_number'] ?? ''),
        'notifications_channel' => in_array($_POST['notifications_channel'] ?? '', ['whatsapp', 'email', 'both'], true) ? $_POST['notifications_channel'] : 'whatsapp',
        'smtp_host' => trim($_POST['smtp_host'] ?? ''),
        'smtp_port' => trim($_POST['smtp_port'] ?? '587'),
        'smtp_username' => trim($_POST['smtp_username'] ?? ''),
        // Keep existing password if field left blank (browser may not resend it)
        'smtp_password' => (trim($_POST['smtp_password'] ?? '') !== '')
            ? trim($_POST['smtp_password'])
            : ($settings['smtp_password'] ?? ''),
        'smtp_from_email' => trim($_POST['smtp_from_email'] ?? ''),
        'smtp_from_name' => trim($_POST['smtp_from_name'] ?? ''),
        'notify_email'  => trim($_POST['notify_email'] ?? ''),
        'notify_email'  => trim($_POST['notify_email'] ?? ''),
        'contact_email' => trim($_POST['contact_email'] ?? ''),
        'at_username' => trim($_POST['at_username'] ?? ''),
        'at_api_key' => trim($_POST['at_api_key'] ?? ''),
        'at_sender_id' => trim($_POST['at_sender_id'] ?? ''),
        'whatsapp_verify_token' => trim($_POST['whatsapp_verify_token'] ?? ''),
        'auto_reply_bot_enabled' => isset($_POST['auto_reply_bot_enabled']) ? '1' : '0',
        'site_url' => rtrim(trim($_POST['site_url'] ?? ''), '/'),
        'wifi_ssid' => trim($_POST['wifi_ssid'] ?? ''),
        'wifi_password' => trim($_POST['wifi_password'] ?? ''),
        'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
    ];
    $stmt = $pdo->prepare(
        "INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v)
         ON DUPLICATE KEY UPDATE setting_value = :v2"
    );
    foreach ($updates as $key => $value) {
        $stmt->execute([':k' => $key, ':v' => $value, ':v2' => $value]);
    }
    $settingsSaved = true;
    // getSettings() caches statically, so merge our fresh values in for this render
    $settings = array_merge($settings, $updates);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings | Quattro Homes Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">

<header class="admin-header">
  <div class="brand" style="color:var(--cream)">
    <div class="mark">Q</div>
    <div class="name">Quattro Homes<small>Admin Dashboard</small></div>
  </div>
  <nav class="admin-nav">
    <a href="dashboard.php">Bookings</a>
    <a href="reviews.php">Reviews</a>
    <a href="issues.php">Issues</a>
    <a href="referrals.php">Referrals</a>
    <a href="blog.php">Blog</a>
    <a href="settings.php" class="active">Settings</a>
    <a href="logout.php">Log out</a>
  </nav>
</header>

<div class="container" style="padding-top:36px;padding-bottom:60px;">

  <div class="card settings-card">
    <h3 style="margin-top:0;">Pricing &amp; Settings</h3>
    <?php if ($settingsSaved): ?>
      <div id="form-feedback" class="success">Settings updated.</div>
    <?php endif; ?>
    <form method="post" class="settings-form">
      <input type="hidden" name="save_settings" value="1">
      <input type="hidden" name="csrf_token" id="csrf_token_field" value="<?php echo htmlspecialchars(csrf_token()); ?>">
      <div class="form-row">
        <div class="field">
          <label for="price_per_night">Price per night (<?php echo htmlspecialchars($settings['currency']); ?>)</label>
          <input type="number" min="0" step="1" id="price_per_night" name="price_per_night" value="<?php echo htmlspecialchars($settings['price_per_night']); ?>">
        </div>
        <div class="field">
          <label for="min_stay_nights">Minimum stay (nights)</label>
          <input type="number" min="1" step="1" id="min_stay_nights" name="min_stay_nights" value="<?php echo htmlspecialchars($settings['min_stay_nights']); ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="field">
          <label for="discount_percent">Long-stay discount (%)</label>
          <input type="number" min="0" max="100" step="1" id="discount_percent" name="discount_percent" value="<?php echo htmlspecialchars($settings['discount_percent']); ?>">
        </div>
        <div class="field">
          <label for="discount_min_nights">Discount applies from (nights)</label>
          <input type="number" min="1" step="1" id="discount_min_nights" name="discount_min_nights" value="<?php echo htmlspecialchars($settings['discount_min_nights']); ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="field">
          <label for="included_guests">Guests included in base price</label>
          <input type="number" min="1" step="1" id="included_guests" name="included_guests" value="<?php echo htmlspecialchars($settings['included_guests']); ?>">
        </div>
        <div class="field">
          <label for="extra_guest_fee">Extra guest fee / night (<?php echo htmlspecialchars($settings['currency']); ?>)</label>
          <input type="number" min="0" step="1" id="extra_guest_fee" name="extra_guest_fee" value="<?php echo htmlspecialchars($settings['extra_guest_fee']); ?>">
        </div>
      </div>
      <div class="field">
        <label for="whatsapp_number">WhatsApp number shown on site (international format, no +)</label>
        <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number']); ?>">
      </div>

      <hr style="border:none;border-top:1px solid var(--line);margin:22px 0 18px;">
      <p style="font-weight:600;color:var(--forest);font-size:0.85rem;margin:0 0 4px;">Email (SMTP)</p>
      <p style="font-size:0.78rem;color:#999;margin:0 0 14px;">
        Used for all guest and admin email notifications. Leave Host blank to fall back to the
        server's basic mail() function (less reliable for delivery). Ask your email vendor
        (e.g. Zoho, Google Workspace) for these SMTP details for info@quattrohomes.co.ke.
      </p>
      <div class="form-row">
        <div class="field">
          <label for="smtp_host">SMTP Host</label>
          <input type="text" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($settings['smtp_host']); ?>" placeholder="e.g. smtp.zoho.com">
        </div>
        <div class="field">
          <label for="smtp_port">SMTP Port</label>
          <input type="text" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($settings['smtp_port']); ?>" placeholder="587 (TLS) or 465 (SSL)">
        </div>
      </div>
      <div class="form-row">
        <div class="field">
          <label for="smtp_username">SMTP Username</label>
          <input type="text" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars($settings['smtp_username']); ?>" placeholder="usually the full email address">
        </div>
        <div class="field">
          <label for="smtp_password">SMTP Password</label>
          <input type="password" id="smtp_password" name="smtp_password"
            placeholder="Leave blank to keep existing password"
            autocomplete="new-password">
          <?php if (!empty($settings['smtp_password'])): ?>
            <small style="color:#888;">Password is saved. Leave blank to keep it unchanged.</small>
          <?php else: ?>
            <small style="color:#c0392b;">No password saved yet.</small>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-row">
        <div class="field">
          <label for="smtp_from_email">From email address</label>
          <input type="text" id="smtp_from_email" name="smtp_from_email" value="<?php echo htmlspecialchars($settings['smtp_from_email']); ?>">

          <label for="notify_email" style="margin-top:14px;display:block;">
            Admin notification email <small style="font-weight:400;color:#888;">(booking alerts sent here)</small>
          </label>
          <input type="email" id="notify_email" name="notify_email"
            value="<?php echo htmlspecialchars($settings['notify_email'] ?? ''); ?>"
            placeholder="e.g. info@quattrohomes.co.ke">
        </div>
        <div class="field">
          <label for="smtp_from_name">From name</label>
          <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?php echo htmlspecialchars($settings['smtp_from_name']); ?>">
        </div>
      </div>
      <div class="field">
        <label for="notify_email">Admin notification email <small style="color:#888;">(booking alerts sent here)</small></label>
        <input type="email" id="notify_email" name="notify_email"
               value="<?php echo htmlspecialchars($settings['notify_email'] ?? ''); ?>"
               placeholder="e.g. info@quattrohomes.co.ke" required>

        <label for="contact_email" style="margin-top:14px;display:block;">Public contact email <small style="color:#888;">(shown on site, not used for sending)</small></label>
        <input type="text" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
      </div>

      <hr style="border:none;border-top:1px solid var(--line);margin:22px 0 18px;">
      <p style="font-weight:600;color:var(--forest);font-size:0.85rem;margin:0 0 4px;">Guest Notifications</p>
      <p style="font-size:0.78rem;color:#999;margin:0 0 14px;">
        Sent automatically at each step: request received, confirmed, cancelled, and checked out.
      </p>
      <div class="field">
        <label for="notifications_channel">Preferred channel</label>
        <select id="notifications_channel" name="notifications_channel">
          <option value="whatsapp" <?php echo $settings['notifications_channel']==='whatsapp'?'selected':''; ?>>WhatsApp (email as backup)</option>
          <option value="both" <?php echo $settings['notifications_channel']==='both'?'selected':''; ?>>WhatsApp + Email (both always)</option>
          <option value="email" <?php echo $settings['notifications_channel']==='email'?'selected':''; ?>>Email only</option>
        </select>
      </div>
      <div class="form-row">
        <div class="field">
          <label for="whatsapp_phone_number_id">WhatsApp Cloud API: Phone Number ID</label>
          <input type="text" id="whatsapp_phone_number_id" name="whatsapp_phone_number_id" value="<?php echo htmlspecialchars($settings['whatsapp_phone_number_id']); ?>" placeholder="From Meta Business > WhatsApp > API Setup">
        </div>
        <div class="field">
          <label for="whatsapp_access_token">WhatsApp Cloud API: Access Token</label>
          <input type="password" id="whatsapp_access_token" name="whatsapp_access_token" value="<?php echo htmlspecialchars($settings['whatsapp_access_token']); ?>" placeholder="System user access token">
        </div>
      </div>
      <div class="field">
        <label for="owner_whatsapp_number">Your WhatsApp number for internal alerts</label>
        <input type="text" id="owner_whatsapp_number" name="owner_whatsapp_number" value="<?php echo htmlspecialchars($settings['owner_whatsapp_number']); ?>">
      </div>
      <p style="font-size:0.78rem;color:#999;margin:-6px 0 0;">
        Until the Phone Number ID and Access Token are filled in, WhatsApp sends are skipped automatically and email is used instead, so nothing breaks.
      </p>

      <hr style="border:none;border-top:1px solid var(--line);margin:22px 0 18px;">
      <p style="font-weight:600;color:var(--forest);font-size:0.85rem;margin:0 0 4px;">SMS &amp; Call Alerts (Africa's Talking)</p>
      <p style="font-size:0.78rem;color:#999;margin:0 0 14px;">
        Used as a backup if WhatsApp isn't configured/delivered, and for an urgent automated call on same-day cancellations.
      </p>
      <div class="form-row">
        <div class="field">
          <label for="at_username">Africa's Talking username</label>
          <input type="text" id="at_username" name="at_username" value="<?php echo htmlspecialchars($settings['at_username']); ?>" placeholder="e.g. sandbox, or your live username">
        </div>
        <div class="field">
          <label for="at_api_key">Africa's Talking API key</label>
          <input type="password" id="at_api_key" name="at_api_key" value="<?php echo htmlspecialchars($settings['at_api_key']); ?>">
        </div>
      </div>
      <div class="field">
        <label for="at_sender_id">Sender ID / Virtual number (required for voice calls)</label>
        <input type="text" id="at_sender_id" name="at_sender_id" value="<?php echo htmlspecialchars($settings['at_sender_id']); ?>">
      </div>

      <hr style="border:none;border-top:1px solid var(--line);margin:22px 0 18px;">
      <p style="font-weight:600;color:var(--forest);font-size:0.85rem;margin:0 0 4px;">WhatsApp Auto-Reply Bot</p>
      <div class="field">
        <label for="whatsapp_verify_token">Webhook Verify Token</label>
        <input type="text" id="whatsapp_verify_token" name="whatsapp_verify_token" value="<?php echo htmlspecialchars($settings['whatsapp_verify_token']); ?>" placeholder="Any string you choose, enter the same one in Meta's webhook setup">
      </div>
      <label style="display:flex;align-items:center;gap:8px;font-size:0.85rem;color:var(--forest);cursor:pointer;">
        <input type="checkbox" name="auto_reply_bot_enabled" value="1" <?php echo ($settings['auto_reply_bot_enabled'] ?? '1') === '1' ? 'checked' : ''; ?> style="width:auto;">
        Enable auto-reply bot (enquiries, booking, status checks via WhatsApp)
      </label>
      <p style="font-size:0.78rem;color:#999;margin:8px 0 0;">
        Webhook URL to set in Meta Business: <code>https://quattrohomes.co.ke/whatsapp_webhook.php</code>
      </p>

      <hr style="border:none;border-top:1px solid var(--line);margin:22px 0 18px;">
      <p style="font-weight:600;color:var(--forest);font-size:0.85rem;margin:0 0 4px;">Site &amp; Guest Info</p>
      <p style="font-size:0.78rem;color:#999;margin:0 0 14px;">
        The site URL is used to build referral links and the "report an issue" link sent in confirmation messages.
        WiFi details are included automatically in the confirmation message once a booking is confirmed.
      </p>
      <div class="field">
        <label for="site_url">Site URL (no trailing slash)</label>
        <input type="text" id="site_url" name="site_url" value="<?php echo htmlspecialchars($settings['site_url']); ?>" placeholder="https://quattrohomes.co.ke">
      </div>
      <div class="form-row">
        <div class="field">
          <label for="wifi_ssid">WiFi network name</label>
          <input type="text" id="wifi_ssid" name="wifi_ssid" value="<?php echo htmlspecialchars($settings['wifi_ssid']); ?>">
        </div>
        <div class="field">
          <label for="wifi_password">WiFi password</label>
          <input type="text" id="wifi_password" name="wifi_password" value="<?php echo htmlspecialchars($settings['wifi_password']); ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="field">
          <label for="referral_discount_percent">Referral: new guest discount (%)</label>
          <input type="number" min="0" max="100" step="1" id="referral_discount_percent" name="referral_discount_percent" value="<?php echo htmlspecialchars($settings['referral_discount_percent']); ?>">
        </div>
        <div class="field">
          <label for="referral_reward_percent">Referral: referrer reward (%)</label>
          <input type="number" min="0" max="100" step="1" id="referral_reward_percent" name="referral_reward_percent" value="<?php echo htmlspecialchars($settings['referral_reward_percent']); ?>">
        </div>
      </div>
      <button type="submit" class="submit-btn" style="width:auto;padding:12px 28px;">Save Settings</button>
    </form>
    <p style="font-size:0.8rem;color:#999;margin-top:10px;">
      Set discount to 0% to disable long-stay discounts. Changes apply immediately to the live booking form.
    </p>
  </div>

</div>
<script src="admin.js"></script>
</body>
</html>
