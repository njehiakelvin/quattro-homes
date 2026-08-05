<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/notify.php';
require_admin_login();

$pdo = getDB();
$settings = getSettings();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['new_status'])) {
    $allowed = ['pending', 'confirmed', 'cancelled'];
    if (in_array($_POST['new_status'], $allowed, true)) {
        $bookingId = (int)$_POST['booking_id'];
        $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $_POST['new_status'], ':id' => $bookingId]);

        if (in_array($_POST['new_status'], ['confirmed', 'cancelled'], true)) {
            $bStmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id");
            $bStmt->execute([':id' => $bookingId]);
            $bookingRow = $bStmt->fetch(PDO::FETCH_ASSOC);
            if ($bookingRow) {
                notify_booking_event($bookingRow, $_POST['new_status']);
            }
        }
    }
    header('Location: dashboard.php');
    exit;
}

// Handle marking a stay as checked out
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout_booking_id'])) {
    $checkoutId = (int)$_POST['checkout_booking_id'];
    $stmt = $pdo->prepare("UPDATE bookings SET checked_out_at = NOW() WHERE id = :id AND status = 'confirmed'");
    $stmt->execute([':id' => $checkoutId]);

    $bStmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id");
    $bStmt->execute([':id' => $checkoutId]);
    $bookingRow = $bStmt->fetch(PDO::FETCH_ASSOC);
    if ($bookingRow) {
        notify_booking_event($bookingRow, 'checked_out');
    }

    header('Location: dashboard.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
    exit;
}

// Handle settings update (price, discount, min stay, WhatsApp number)
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

$filter = $_GET['status'] ?? 'all';
$sql = "SELECT * FROM bookings";
$params = [];
if (in_array($filter, ['pending', 'confirmed', 'cancelled'], true)) {
    $sql .= " WHERE status = :status";
    $params[':status'] = $filter;
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counts = $pdo->query("SELECT status, COUNT(*) c FROM bookings GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Quattro Homes Admin</title>
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
    <a href="dashboard.php" class="active">Bookings</a>
    <a href="reviews.php">Reviews</a>
    <a href="issues.php">Issues</a>
    <a href="referrals.php">Referrals</a>
    <a href="logout.php">Log out</a>
  </nav>
</header>

<div class="container" style="padding-top:36px;padding-bottom:60px;">

  <div class="admin-stats">
    <a href="?status=all" class="stat-card <?php echo $filter==='all'?'active':''; ?>">
      <span class="stat-num"><?php echo array_sum($counts); ?></span><span>Total</span>
    </a>
    <a href="?status=pending" class="stat-card <?php echo $filter==='pending'?'active':''; ?>">
      <span class="stat-num"><?php echo $counts['pending'] ?? 0; ?></span><span>Pending</span>
    </a>
    <a href="?status=confirmed" class="stat-card <?php echo $filter==='confirmed'?'active':''; ?>">
      <span class="stat-num"><?php echo $counts['confirmed'] ?? 0; ?></span><span>Confirmed</span>
    </a>
    <a href="?status=cancelled" class="stat-card <?php echo $filter==='cancelled'?'active':''; ?>">
      <span class="stat-num"><?php echo $counts['cancelled'] ?? 0; ?></span><span>Cancelled</span>
    </a>
  </div>

  <div class="card settings-card">
    <h3 style="margin-top:0;">Pricing &amp; Settings</h3>
    <?php if ($settingsSaved): ?>
      <div id="form-feedback" class="success">Settings updated.</div>
    <?php endif; ?>
    <form method="post" class="settings-form">
      <input type="hidden" name="save_settings" value="1">
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
          <label for="whatsapp_phone_number_id">WhatsApp Cloud API — Phone Number ID</label>
          <input type="text" id="whatsapp_phone_number_id" name="whatsapp_phone_number_id" value="<?php echo htmlspecialchars($settings['whatsapp_phone_number_id']); ?>" placeholder="From Meta Business > WhatsApp > API Setup">
        </div>
        <div class="field">
          <label for="whatsapp_access_token">WhatsApp Cloud API — Access Token</label>
          <input type="password" id="whatsapp_access_token" name="whatsapp_access_token" value="<?php echo htmlspecialchars($settings['whatsapp_access_token']); ?>" placeholder="System user access token">
        </div>
      </div>
      <div class="field">
        <label for="owner_whatsapp_number">Your WhatsApp number for internal alerts</label>
        <input type="text" id="owner_whatsapp_number" name="owner_whatsapp_number" value="<?php echo htmlspecialchars($settings['owner_whatsapp_number']); ?>">
      </div>
      <p style="font-size:0.78rem;color:#999;margin:-6px 0 0;">
        Until the Phone Number ID and Access Token are filled in, WhatsApp sends are skipped automatically and email is used instead — nothing breaks.
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
        <input type="text" id="whatsapp_verify_token" name="whatsapp_verify_token" value="<?php echo htmlspecialchars($settings['whatsapp_verify_token']); ?>" placeholder="Any string you choose — enter the same one in Meta's webhook setup">
      </div>
      <label style="display:flex;align-items:center;gap:8px;font-size:0.85rem;color:var(--forest);cursor:pointer;">
        <input type="checkbox" name="auto_reply_bot_enabled" value="1" <?php echo ($settings['auto_reply_bot_enabled'] ?? '1') === '1' ? 'checked' : ''; ?> style="width:auto;">
        Enable auto-reply bot (enquiries, booking, status checks via WhatsApp)
      </label>
      <p style="font-size:0.78rem;color:#999;margin:8px 0 0;">
        Webhook URL to set in Meta Business: <code>https://yourdomain.com/whatsapp_webhook.php</code>
      </p>

      <hr style="border:none;border-top:1px solid var(--line);margin:22px 0 18px;">
      <p style="font-weight:600;color:var(--forest);font-size:0.85rem;margin:0 0 4px;">Site &amp; Guest Info</p>
      <p style="font-size:0.78rem;color:#999;margin:0 0 14px;">
        The site URL is used to build referral links and the "report an issue" link sent in confirmation messages.
        WiFi details are included automatically in the confirmation message once a booking is confirmed.
      </p>
      <div class="field">
        <label for="site_url">Site URL (no trailing slash)</label>
        <input type="text" id="site_url" name="site_url" value="<?php echo htmlspecialchars($settings['site_url']); ?>" placeholder="https://yourdomain.com">
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

  <div class="card" style="padding:0;overflow-x:auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Guest</th><th>Contact</th><th>Floor</th><th>Dates</th><th>Nights</th><th>Guests</th><th>Purpose</th><th>Total</th><th>Status</th><th>Checkout</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$bookings): ?>
          <tr><td colspan="11" style="text-align:center;padding:30px;color:#999;">No bookings found.</td></tr>
        <?php endif; ?>
        <?php foreach ($bookings as $b): ?>
          <tr>
            <td data-label="Guest"><?php echo htmlspecialchars($b['full_name']); ?></td>
            <td data-label="Contact">
              <?php echo htmlspecialchars($b['phone']); ?><br>
              <span style="color:#999;font-size:0.8rem;"><?php echo htmlspecialchars($b['email']); ?></span>
            </td>
            <td data-label="Floor"><?php
              $bFloor = $b['floor'] ?? 'floor2';
              echo ['floor1' => 'Floor 1', 'floor2' => 'Floor 2', 'both' => 'Both'][$bFloor] ?? htmlspecialchars($bFloor);
            ?></td>
            <td data-label="Dates"><?php echo htmlspecialchars($b['checkin_date']); ?> &rarr; <?php echo htmlspecialchars($b['checkout_date']); ?></td>
            <td data-label="Nights"><?php echo (int)$b['nights']; ?></td>
            <td data-label="Guests"><?php echo (int)$b['guests']; ?></td>
            <td data-label="Purpose"><?php echo htmlspecialchars($b['purpose']); ?></td>
            <td data-label="Total"><?php echo $b['total_price'] !== null ? htmlspecialchars($settings['currency'] . ' ' . number_format($b['total_price'])) : '&mdash;'; ?></td>
            <td data-label="Status"><span class="badge badge-<?php echo $b['status']; ?>"><?php echo ucfirst($b['status']); ?></span></td>
            <td data-label="Checkout">
              <?php if (!empty($b['checked_out_at'])): ?>
                <span class="badge badge-confirmed"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars(date('M j', strtotime($b['checked_out_at']))); ?></span>
              <?php elseif ($b['status'] === 'confirmed'): ?>
                <form method="post">
                  <input type="hidden" name="checkout_booking_id" value="<?php echo (int)$b['id']; ?>">
                  <button type="submit" class="mini-btn" title="Mark this stay as checked out">Mark Checked Out</button>
                </form>
              <?php else: ?>
                <span style="color:#bbb;">&mdash;</span>
              <?php endif; ?>
            </td>
            <td data-label="Action">
              <form method="post" style="display:flex;gap:6px;">
                <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                <select name="new_status" class="mini-select">
                  <option value="pending" <?php echo $b['status']==='pending'?'selected':''; ?>>Pending</option>
                  <option value="confirmed" <?php echo $b['status']==='confirmed'?'selected':''; ?>>Confirmed</option>
                  <option value="cancelled" <?php echo $b['status']==='cancelled'?'selected':''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="mini-btn">Save</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
</body>
</html>