<?php
require_once __DIR__ . '/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (admin_attempt_login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
}

if (admin_logged_in()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Quattro Homes</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <div class="admin-login-wrap">
    <div class="admin-login-card">
      <div class="brand" style="justify-content:center;color:var(--forest);margin-bottom:8px;">
        <div class="mark" style="color:var(--forest);border-color:var(--forest);">Q</div>
        <div class="name">Quattro Homes<small style="color:var(--gold)">Admin</small></div>
      </div>
      <h2 style="text-align:center;margin-top:4px;">Sign in</h2>
      <?php if ($error): ?>
        <div id="form-feedback" class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required autofocus>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="submit-btn">Sign In</button>
      </form>
      <p style="text-align:center;font-size:0.8rem;color:#999;margin-top:18px;">
        <a href="../index.php">&larr; Back to site</a>
      </p>
    </div>
  </div>
</body>
</html>
