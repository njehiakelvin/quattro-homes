<?php
/**
 * SMTP Test — sends a test email using current settings.
 * Accessed via POST from admin settings page.
 */

require_once __DIR__ . '/auth.php';        // starts session correctly
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Must be logged-in admin
if (!admin_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not authorised. Please log in to admin first.']);
    exit;
}

// Must be POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// CSRF check
if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Security token mismatch. Reload the page and try again.']);
    exit;
}

$to       = filter_var(trim($_POST['to'] ?? ''), FILTER_VALIDATE_EMAIL);
$settings = getSettings();

if (!$to) {
    echo json_encode(['success' => false, 'message' => 'Enter a valid email address to send the test to.']);
    exit;
}

if (empty($settings['smtp_host'])) {
    echo json_encode(['success' => false, 'message' => 'SMTP host is empty. Fill in and save your SMTP settings first.']);
    exit;
}

if (empty($settings['smtp_password'])) {
    echo json_encode(['success' => false, 'message' => 'SMTP password is empty. Re-enter and save your password first.']);
    exit;
}

// Load PHPMailer
$base = __DIR__ . '/../includes/PHPMailer/';
if (!file_exists($base . 'PHPMailer.php')) {
    echo json_encode(['success' => false, 'message' => 'PHPMailer files not found at ' . $base]);
    exit;
}
require_once $base . 'PHPMailer.php';
require_once $base . 'SMTP.php';
require_once $base . 'Exception.php';

try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $settings['smtp_host'];
    $mail->Port       = (int)($settings['smtp_port'] ?? 587);
    $mail->SMTPAuth   = true;
    $mail->Username   = $settings['smtp_username'] ?? '';
    $mail->Password   = $settings['smtp_password'] ?? '';
    $mail->SMTPSecure = ((int)($settings['smtp_port'] ?? 587) === 465)
        ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->setFrom(
        $settings['smtp_from_email'] ?? 'info@quattrohomes.co.ke',
        $settings['smtp_from_name']  ?? 'Quattro Homes'
    );
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = 'Quattro Homes SMTP Test';
    $mail->Body    = '<p>This is a test email from <strong>Quattro Homes</strong>.</p>'
                   . '<p>Your SMTP settings are working correctly.</p>'
                   . '<p style="color:#999;font-size:12px;">Sent via: '
                   . htmlspecialchars($settings['smtp_host']) . ':' . (int)$settings['smtp_port']
                   . ' at ' . date('Y-m-d H:i:s T') . '</p>';
    $mail->AltBody = 'SMTP test from Quattro Homes — settings are working correctly.';
    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'Sent! Check the inbox (and spam folder) of ' . $to
    ]);

} catch (Exception $e) {
    // Log to file
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0750, true);
    @file_put_contents(
        $logDir . '/mail_errors.log',
        date('[Y-m-d H:i:s] ') . 'SMTP TEST to ' . $to . ': ' . $e->getMessage() . "\n",
        FILE_APPEND | LOCK_EX
    );

    echo json_encode([
        'success' => false,
        'message' => 'SMTP error: ' . $e->getMessage()
    ]);
}
