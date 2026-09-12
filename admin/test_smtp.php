<?php
/**
 * SMTP Test — sends a test email using current settings.
 * Only accessible from admin panel. Returns JSON.
 */
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Must be POST with valid CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
    exit;
}

// Must be logged-in admin
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorised.']);
    exit;
}

$to       = filter_var(trim($_POST['to'] ?? ''), FILTER_VALIDATE_EMAIL);
$settings = getSettings();

if (!$to) {
    echo json_encode(['success' => false, 'message' => 'Invalid recipient email address.']);
    exit;
}

if (empty($settings['smtp_host'])) {
    echo json_encode(['success' => false, 'message' => 'SMTP host is not configured. Save your SMTP settings first.']);
    exit;
}

// Load PHPMailer
$phpMailerPath = __DIR__ . '/../includes/PHPMailer/PHPMailer.php';
if (!file_exists($phpMailerPath)) {
    echo json_encode(['success' => false, 'message' => 'PHPMailer not found at ' . $phpMailerPath]);
    exit;
}
require_once $phpMailerPath;
require_once __DIR__ . '/../includes/PHPMailer/SMTP.php';
require_once __DIR__ . '/../includes/PHPMailer/Exception.php';

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
    $mail->Subject = 'Quattro Homes — SMTP Test Email';
    $mail->Body    = '<p>This is a test email from <strong>Quattro Homes</strong>.</p>'
                   . '<p>If you received this, your SMTP settings are working correctly.</p>'
                   . '<p style="color:#888;font-size:12px;">Sent: ' . date('Y-m-d H:i:s') . ' | Host: ' . htmlspecialchars($settings['smtp_host']) . ':' . $settings['smtp_port'] . '</p>';
    $mail->AltBody = 'SMTP test from Quattro Homes. If you received this, your SMTP settings are working.';
    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'Test email sent to ' . $to . '. Check your inbox (and spam folder).'
    ]);
} catch (Exception $e) {
    // Log it
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0750, true);
    @file_put_contents(
        $logDir . '/mail_errors.log',
        date('[Y-m-d H:i:s] ') . 'SMTP TEST error to ' . $to . ': ' . $e->getMessage() . "\n",
        FILE_APPEND | LOCK_EX
    );

    echo json_encode([
        'success' => false,
        'message' => 'SMTP error: ' . $e->getMessage()
    ]);
}
