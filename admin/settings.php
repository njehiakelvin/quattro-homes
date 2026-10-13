<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/settings.php';
require_admin_login();

$pdo      = getDB();
$settings = getSettings();
$saved    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $updates = [
        'price_per_night'           => max(0, (float)($_POST['price_per_night'] ?? 0)),
        'min_stay_nights'           => max(1, (int)($_POST['min_stay_nights'] ?? 1)),
        'discount_percent'          => min(100, max(0, (float)($_POST['discount_percent'] ?? 0))),
        'discount_min_nights'       => max(1, (int)($_POST['discount_min_nights'] ?? 1)),
        'included_guests'           => max(1, (int)($_POST['included_guests'] ?? 2)),
        'extra_guest_fee'           => max(0, (float)($_POST['extra_guest_fee'] ?? 0)),
        'referral_discount_percent' => min(100, max(0, (float)($_POST['referral_discount_percent'] ?? 0))),
        'referral_reward_percent'   => min(100, max(0, (float)($_POST['referral_reward_percent'] ?? 0))),
        'whatsapp_number'           => trim($_POST['whatsapp_number'] ?? ''),
        'notify_email'              => trim($_POST['notify_email'] ?? ''),
        'contact_email'             => trim($_POST['contact_email'] ?? ''),
        'site_url'                  => rtrim(trim($_POST['site_url'] ?? ''), '/'),
        'wifi_ssid'                 => trim($_POST['wifi_ssid'] ?? ''),
        'wifi_password'             => trim($_POST['wifi_password'] ?? ''),
        'smtp_host'                 => trim($_POST['smtp_host'] ?? ''),
        'smtp_port'                 => trim($_POST['smtp_port'] ?? '587'),
        'smtp_username'             => trim($_POST['smtp_username'] ?? ''),
        'smtp_password'             => (trim($_POST['smtp_password'] ?? '') !== '')
                                         ? trim($_POST['smtp_password'])
                                         : ($settings['smtp_password'] ?? ''),
        'smtp_from_email'           => trim($_POST['smtp_from_email'] ?? ''),
        'smtp_from_name'            => trim($_POST['smtp_from_name'] ?? ''),
        'notifications_channel'     => in_array($_POST['notifications_channel']??'', ['whatsapp','email','both'],true)
                                         ? $_POST['notifications_channel'] : 'email',
        'owner_whatsapp_number'     => trim($_POST['owner_whatsapp_number'] ?? ''),
        'whatsapp_phone_number_id'  => trim($_POST['whatsapp_phone_number_id'] ?? ''),
        'whatsapp_access_token'     => trim($_POST['whatsapp_access_token'] ?? ''),
        'whatsapp_verify_token'     => trim($_POST['whatsapp_verify_token'] ?? ''),
        'auto_reply_bot_enabled'    => isset($_POST['auto_reply_bot_enabled']) ? '1' : '0',
        'at_username'               => trim($_POST['at_username'] ?? ''),
        'at_api_key'                => trim($_POST['at_api_key'] ?? ''),
        'at_sender_id'              => trim($_POST['at_sender_id'] ?? ''),
    ];
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (:k,:v) ON DUPLICATE KEY UPDATE setting_value=:v2");
    foreach ($updates as $key => $val) {
        $stmt->execute([':k'=>$key,':v'=>$val,':v2'=>$val]);
    }
    $saved    = true;
    $settings = array_merge($settings, $updates);
}

function field($id, $label, $type='text', $value='', $extra='', $hint='') {
    $v = htmlspecialchars($value);
    $h = $hint ? "<small class='field-hint'>$hint</small>" : '';
    return "<div class='field'><label for='$id'>$label</label><input type='$type' id='$id' name='$id' value='$v' $extra>$h</div>";
}
function pw_field($id, $label, $saved=false, $placeholder='') {
    $ph = $placeholder ?: 'Leave blank to keep existing';
    $note = $saved
        ? "<small class='field-hint saved-pw'>Password saved. Leave blank to keep it.</small>"
        : "<small class='field-hint' style='color:#e07070;'>Not set yet.</small>";
    return "<div class='field'><label for='$id'>$label</label><input type='password' id='$id' name='$id' placeholder='".htmlspecialchars($ph)."' autocomplete='new-password'>$note</div>";
}
function row_open()  { return "<div class='form-row'>"; }
function row_close() { return "</div>"; }
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
  <a href="dashboard.php" class="admin-brand">
    <div class="mark">Q</div>
    <div class="name">Quattro Homes<small>Admin</small></div>
  </a>
  <nav class="admin-nav">
    <a href="dashboard.php">Bookings</a>
    <a href="reviews.php">Reviews</a>
    <a href="issues.php">Issues</a>
    <a href="referrals.php">Referrals</a>
    <a href="blog.php">Blog</a>
    <a href="settings.php" class="active">Settings</a>
    <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
  </nav>
</header>

<div class="admin-container">
  <h2 class="admin-page-title">Settings</h2>

  <?php if ($saved): ?>
    <div class="admin-alert success">Settings saved.</div>
  <?php endif; ?>

  <form method="post" class="settings-form">
    <input type="hidden" name="save_settings" value="1">
    <input type="hidden" name="csrf_token" id="csrf_token_field" value="<?php echo htmlspecialchars(csrf_token()); ?>">

    <!-- ① HOME SETTINGS — open by default -->
    <details class="settings-accordion" open>
      <summary><i class="fa-solid fa-house"></i> Home Settings</summary>
      <div class="accordion-body">
        <?php echo row_open(); ?>
        <?php echo field('price_per_night','Price per night ('.$settings['currency'].')','number',$settings['price_per_night'],'min="0" step="1"'); ?>
        <?php echo field('min_stay_nights','Minimum stay (nights)','number',$settings['min_stay_nights'],'min="1" step="1"'); ?>
        <?php echo row_close(); ?>
        <?php echo row_open(); ?>
        <?php echo field('discount_percent','Long-stay discount (%)','number',$settings['discount_percent'],'min="0" max="100" step="1"'); ?>
        <?php echo field('discount_min_nights','Discount from (nights)','number',$settings['discount_min_nights'],'min="1" step="1"'); ?>
        <?php echo row_close(); ?>
        <?php echo row_open(); ?>
        <?php echo field('included_guests','Guests in base price','number',$settings['included_guests'],'min="1" step="1"'); ?>
        <?php echo field('extra_guest_fee','Extra guest fee / night','number',$settings['extra_guest_fee'],'min="0" step="1"'); ?>
        <?php echo row_close(); ?>
        <?php echo row_open(); ?>
        <?php echo field('wifi_ssid','WiFi Name (SSID)',  'text', $settings['wifi_ssid']); ?>
        <?php echo field('wifi_password','WiFi Password','text', $settings['wifi_password']); ?>
        <?php echo row_close(); ?>
        <?php echo field('whatsapp_number','WhatsApp number on site (no +)','text',$settings['whatsapp_number'],'','e.g. 254733545858'); ?>
        <?php echo field('site_url','Site URL','url',$settings['site_url'],'','e.g. https://quattrohomes.co.ke'); ?>
      </div>
    </details>

    <!-- ② EMAIL -->
    <details class="settings-accordion">
      <summary><i class="fa-regular fa-envelope"></i> Email (SMTP)</summary>
      <div class="accordion-body">
        <?php echo row_open(); ?>
        <?php echo field('smtp_host','SMTP Host','text',$settings['smtp_host'],'','e.g. smtp-relay.brevo.com'); ?>
        <?php echo field('smtp_port','SMTP Port','text',$settings['smtp_port'],'','587 (TLS) or 465 (SSL)'); ?>
        <?php echo row_close(); ?>
        <?php echo row_open(); ?>
        <?php echo field('smtp_username','SMTP Username / Login','text',$settings['smtp_username']); ?>
        <?php echo pw_field('smtp_password','SMTP Password',!empty($settings['smtp_password'])); ?>
        <?php echo row_close(); ?>
        <?php echo row_open(); ?>
        <?php echo field('smtp_from_email','From email address','email',$settings['smtp_from_email']); ?>
        <?php echo field('smtp_from_name','From name','text',$settings['smtp_from_name']); ?>
        <?php echo row_close(); ?>
        <?php echo row_open(); ?>
        <?php echo field('notify_email','Admin notification email','email',$settings['notify_email']??'','','Booking alerts are sent here'); ?>
        <?php echo field('contact_email','Public contact email','email',$settings['contact_email']??'','','Shown on site, not used for sending'); ?>
        <?php echo row_close(); ?>
      </div>
    </details>

    <!-- ③ NOTIFICATIONS -->
    <details class="settings-accordion">
      <summary><i class="fa-solid fa-bell"></i> Guest Notifications</summary>
      <div class="accordion-body">
        <div class="field">
          <label for="notifications_channel">Preferred channel</label>
          <select id="notifications_channel" name="notifications_channel">
            <option value="email"     <?php echo $settings['notifications_channel']==='email'?'selected':''; ?>>Email only</option>
            <option value="whatsapp" <?php echo $settings['notifications_channel']==='whatsapp'?'selected':''; ?>>WhatsApp (email as backup)</option>
            <option value="both"     <?php echo $settings['notifications_channel']==='both'?'selected':''; ?>>WhatsApp + Email (both)</option>
          </select>
        </div>
        <?php echo row_open(); ?>
        <?php echo field('whatsapp_phone_number_id','WhatsApp Cloud API: Phone Number ID','text',$settings['whatsapp_phone_number_id'],'','From Meta Business > WhatsApp > API Setup'); ?>
        <?php echo pw_field('whatsapp_access_token','WhatsApp Cloud API: Access Token',!empty($settings['whatsapp_access_token'])); ?>
        <?php echo row_close(); ?>
        <?php echo field('owner_whatsapp_number','Your WhatsApp number (internal alerts)','text',$settings['owner_whatsapp_number']); ?>
      </div>
    </details>

    <!-- ④ REFERRALS -->
    <details class="settings-accordion">
      <summary><i class="fa-solid fa-gift"></i> Referrals</summary>
      <div class="accordion-body">
        <?php echo row_open(); ?>
        <?php echo field('referral_discount_percent','Discount for referred guest (%)','number',$settings['referral_discount_percent'],'min="0" max="100" step="1"'); ?>
        <?php echo field('referral_reward_percent','Reward for referrer (%)','number',$settings['referral_reward_percent'],'min="0" max="100" step="1"'); ?>
        <?php echo row_close(); ?>
      </div>
    </details>

    <!-- ⑤ SMS / AFRICA'S TALKING -->
    <details class="settings-accordion">
      <summary><i class="fa-solid fa-mobile-screen"></i> SMS & Call Alerts</summary>
      <div class="accordion-body">
        <?php echo row_open(); ?>
        <?php echo field('at_username',"Africa's Talking username",'text',$settings['at_username'],'','e.g. sandbox or your live username'); ?>
        <?php echo pw_field('at_api_key',"Africa's Talking API key",!empty($settings['at_api_key'])); ?>
        <?php echo row_close(); ?>
        <?php echo field('at_sender_id','Sender ID / Virtual number','text',$settings['at_sender_id'],'','Required for voice calls'); ?>
      </div>
    </details>

    <!-- ⑥ WHATSAPP BOT -->
    <details class="settings-accordion">
      <summary><i class="fa-brands fa-whatsapp"></i> WhatsApp Auto-Reply Bot</summary>
      <div class="accordion-body">
        <?php echo field('whatsapp_verify_token','Webhook Verify Token','text',$settings['whatsapp_verify_token'],'','Any string — enter the same value in Meta webhook setup'); ?>
        <div class="field">
          <label>
            <input type="checkbox" name="auto_reply_bot_enabled" value="1" <?php echo !empty($settings['auto_reply_bot_enabled']) && $settings['auto_reply_bot_enabled']==='1'?'checked':''; ?>>
            Enable auto-reply bot
          </label>
        </div>
      </div>
    </details>

    <div class="settings-save-bar">
      <button type="submit" class="admin-btn admin-btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> Save Settings
      </button>
    </div>

  </form>
</div>
</body>
</html>
