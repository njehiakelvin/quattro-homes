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

// Handle recording an external booking (Airbnb, Booking.com, walk-in, etc.) as occupied
$blockError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['block_dates'])) {
    $blockFloor = in_array($_POST['block_floor'] ?? '', ['floor1', 'floor2', 'both'], true) ? $_POST['block_floor'] : 'floor2';
    $blockCheckin = trim($_POST['block_checkin'] ?? '');
    $blockCheckout = trim($_POST['block_checkout'] ?? '');
    $blockSource = trim($_POST['block_source'] ?? '') ?: 'Other';
    $blockGuestName = trim($_POST['block_guest_name'] ?? '');
    $blockLabel = $blockGuestName !== '' ? "{$blockGuestName} ({$blockSource})" : "Booked via {$blockSource}";

    $ci = DateTime::createFromFormat('Y-m-d', $blockCheckin);
    $co = DateTime::createFromFormat('Y-m-d', $blockCheckout);

    if (!$ci || !$co || $co <= $ci) {
        $blockError = 'Please provide a valid check-in and check-out date, with check-out after check-in.';
    } else {
        $floorCondition = $blockFloor === 'both'
            ? "floor IN ('floor1', 'floor2', 'both')"
            : "floor IN (:floor, 'both')";
        $checkSql = "SELECT COUNT(*) FROM bookings WHERE status != 'cancelled' AND checkin_date < :checkout AND checkout_date > :checkin AND {$floorCondition}";
        $checkStmt = $pdo->prepare($checkSql);
        $checkParams = [':checkout' => $blockCheckout, ':checkin' => $blockCheckin];
        if ($blockFloor !== 'both') $checkParams[':floor'] = $blockFloor;
        $checkStmt->execute($checkParams);

        if ($checkStmt->fetchColumn() > 0) {
            $blockError = 'Those dates overlap an existing booking or block for this unit.';
        } else {
            $nights = (int)$ci->diff($co)->days;
            $insert = $pdo->prepare(
                "INSERT INTO bookings (full_name, email, phone, checkin_date, checkout_date, floor, nights, total_price, guests, purpose, message, status, is_blocked)
                 VALUES (:name, '', '', :checkin, :checkout, :floor, :nights, NULL, 0, :source, :label, 'confirmed', 1)"
            );
            $insert->execute([
                ':name' => $blockLabel,
                ':checkin' => $blockCheckin,
                ':checkout' => $blockCheckout,
                ':floor' => $blockFloor,
                ':nights' => $nights,
                ':source' => $blockSource,
                ':label' => $blockLabel,
            ]);
            header('Location: dashboard.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
            exit;
        }
    }
}

// Handle deleting a booking entirely (permanent — used for blocks or mistaken entries)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
    $deleteId = (int)$_POST['delete_booking_id'];
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
    $stmt->execute([':id' => $deleteId]);
    header('Location: dashboard.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
    exit;
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
<title>Dashboard | Quattro Homes Admin</title>
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
    <a href="blog.php">Blog</a>
    <a href="settings.php">Settings</a>
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
    <h3 style="margin-top:0;">Mark as Occupied (External Booking)</h3>
    <p style="font-size:0.8rem;color:#999;margin:0 0 14px;">
      Record a booking made outside our site (Airbnb, Booking.com, walk-in, phone booking, etc.)
      so it correctly blocks these dates on the calendar and stops double-bookings.
    </p>
    <?php if ($blockError): ?>
      <div id="form-feedback" class="error"><?php echo htmlspecialchars($blockError); ?></div>
    <?php endif; ?>
    <form method="post" class="settings-form">
      <input type="hidden" name="block_dates" value="1">
      <div class="form-row">
        <div class="field">
          <label for="block_floor">Unit</label>
          <select id="block_floor" name="block_floor">
            <option value="floor2">Floor 2</option>
            <option value="floor1">Floor 1</option>
            <option value="both">Both floors</option>
          </select>
        </div>
        <div class="field">
          <label for="block_source">Booked via</label>
          <select id="block_source" name="block_source">
            <option value="Airbnb">Airbnb</option>
            <option value="Booking.com">Booking.com</option>
            <option value="Walk-in">Walk-in</option>
            <option value="Phone">Phone</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="field">
          <label for="block_guest_name">Guest name (optional)</label>
          <input type="text" id="block_guest_name" name="block_guest_name" placeholder="e.g. Jane Doe" maxlength="120">
        </div>
        <div class="field"></div>
      </div>
      <div class="form-row">
        <div class="field">
          <label for="block_checkin">Check-in</label>
          <input type="date" id="block_checkin" name="block_checkin" required>
        </div>
        <div class="field">
          <label for="block_checkout">Check-out</label>
          <input type="date" id="block_checkout" name="block_checkout" required>
        </div>
      </div>
      <button type="submit" class="submit-btn" style="width:auto;padding:12px 28px;">Mark as Occupied</button>
    </form>
    <p style="font-size:0.78rem;color:#999;margin-top:10px;">
      This won't send any WhatsApp/email notifications, since there's no guest record from our site to notify.
    </p>
    </form>
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
          <tr<?php echo !empty($b['is_blocked']) ? ' style="background:#faf7f0;"' : ''; ?>>
            <td data-label="Guest">
              <?php echo htmlspecialchars($b['full_name']); ?>
              <?php if (!empty($b['is_blocked'])): ?><br><span class="badge" style="background:#e5e0d8;color:#6b6357;">External</span><?php endif; ?>
            </td>
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
              <?php if (!empty($b['is_blocked'])): ?>
                <span style="color:#bbb;">&mdash;</span>
              <?php elseif (!empty($b['checked_out_at'])): ?>
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
              <?php if (empty($b['is_blocked'])): ?>
              <form method="post" style="display:flex;gap:6px;margin-bottom:6px;">
                <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                <select name="new_status" class="mini-select">
                  <option value="pending" <?php echo $b['status']==='pending'?'selected':''; ?>>Pending</option>
                  <option value="confirmed" <?php echo $b['status']==='confirmed'?'selected':''; ?>>Confirmed</option>
                  <option value="cancelled" <?php echo $b['status']==='cancelled'?'selected':''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="mini-btn">Save</button>
              </form>
              <?php endif; ?>
              <form method="post" onsubmit="return confirm('Delete this <?php echo !empty($b['is_blocked']) ? 'external booking record' : 'booking'; ?> permanently? This cannot be undone.');">
                <input type="hidden" name="delete_booking_id" value="<?php echo (int)$b['id']; ?>">
                <button type="submit" class="mini-btn" style="background:#a3382a;">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
<script src="admin.js"></script>
</body>
</html>
