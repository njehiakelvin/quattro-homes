<?php
require_once __DIR__ . '/auth.php';
require_admin_login();

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['new_status'])) {
    $allowed = ['pending', 'approved', 'rejected'];
    if (in_array($_POST['new_status'], $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE reviews SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $_POST['new_status'], ':id' => (int)$_POST['review_id']]);
    }
    header('Location: reviews.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
    exit;
}

$filter = $_GET['status'] ?? 'pending';
$sql = "SELECT r.*, b.checkin_date, b.checkout_date FROM reviews r JOIN bookings b ON b.id = r.booking_id";
$params = [];
if (in_array($filter, ['pending', 'approved', 'rejected'], true)) {
    $sql .= " WHERE r.status = :status";
    $params[':status'] = $filter;
}
$sql .= " ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counts = $pdo->query("SELECT status, COUNT(*) c FROM reviews GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reviews — Quattro Homes Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">

<header class="admin-header">
  <div class="brand" style="color:var(--cream)">
    <div class="mark">Q</div>
    <div class="name">Quattro Homes<small>Reviews</small></div>
  </div>
  <nav class="admin-nav">
    <a href="dashboard.php">Bookings</a>
    <a href="reviews.php" class="active">Reviews</a>
    <a href="issues.php">Issues</a>
    <a href="referrals.php">Referrals</a>
    <a href="logout.php">Log out</a>
  </nav>
</header>

<div class="container" style="padding-top:36px;padding-bottom:60px;">

  <div class="admin-stats">
    <a href="?status=pending" class="stat-card <?php echo $filter==='pending'?'active':''; ?>">
      <span class="stat-num"><?php echo $counts['pending'] ?? 0; ?></span><span>Pending</span>
    </a>
    <a href="?status=approved" class="stat-card <?php echo $filter==='approved'?'active':''; ?>">
      <span class="stat-num"><?php echo $counts['approved'] ?? 0; ?></span><span>Approved</span>
    </a>
    <a href="?status=rejected" class="stat-card <?php echo $filter==='rejected'?'active':''; ?>">
      <span class="stat-num"><?php echo $counts['rejected'] ?? 0; ?></span><span>Rejected</span>
    </a>
    <a href="?status=all" class="stat-card <?php echo $filter==='all'?'active':''; ?>">
      <span class="stat-num"><?php echo array_sum($counts); ?></span><span>Total</span>
    </a>
  </div>

  <div class="card" style="padding:0;overflow-x:auto;">
    <table class="admin-table">
      <thead>
        <tr><th>Guest</th><th>Stay</th><th>Rating</th><th>Comment</th><th>Submitted</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php if (!$reviews): ?>
          <tr><td colspan="7" style="text-align:center;padding:30px;color:#999;">No reviews found.</td></tr>
        <?php endif; ?>
        <?php foreach ($reviews as $r): ?>
          <tr>
            <td data-label="Guest"><?php echo htmlspecialchars($r['full_name']); ?><br><span style="color:#999;font-size:0.8rem;"><?php echo htmlspecialchars($r['email']); ?></span></td>
            <td data-label="Stay"><?php echo htmlspecialchars($r['checkin_date']); ?> &rarr; <?php echo htmlspecialchars($r['checkout_date']); ?></td>
            <td data-label="Rating"><?php echo str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5 - (int)$r['rating']); ?></td>
            <td data-label="Comment"><?php echo $r['comment'] ? nl2br(htmlspecialchars($r['comment'])) : '<span style="color:#bbb;">—</span>'; ?></td>
            <td data-label="Submitted"><?php echo htmlspecialchars(date('M j, Y', strtotime($r['created_at']))); ?></td>
            <td data-label="Status"><span class="badge badge-<?php echo $r['status'] === 'approved' ? 'confirmed' : ($r['status'] === 'rejected' ? 'cancelled' : 'pending'); ?>"><?php echo ucfirst($r['status']); ?></span></td>
            <td data-label="Action">
              <form method="post" style="display:flex;gap:6px;">
                <input type="hidden" name="review_id" value="<?php echo (int)$r['id']; ?>">
                <select name="new_status" class="mini-select">
                  <option value="pending" <?php echo $r['status']==='pending'?'selected':''; ?>>Pending</option>
                  <option value="approved" <?php echo $r['status']==='approved'?'selected':''; ?>>Approved</option>
                  <option value="rejected" <?php echo $r['status']==='rejected'?'selected':''; ?>>Rejected</option>
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
