<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/security.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

if (!empty($_POST['website'])) { // honeypot
    $response['success'] = true;
    $response['message'] = 'Thank you, your report has been received.';
    echo json_encode($response);
    exit;
}

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    $response['message'] = 'Your session expired. Please refresh the page and try again.';
    echo json_encode($response);
    exit;
}

if (!rate_limit_ok('issue_submit', 20)) {
    $response['message'] = 'Please wait a moment before submitting again.';
    echo json_encode($response);
    exit;
}

$full_name   = trim($_POST['full_name'] ?? '');
$email       = trim($_POST['email'] ?? '');
$phone       = trim($_POST['phone'] ?? '');
$category    = trim($_POST['category'] ?? 'other');
$description = trim($_POST['description'] ?? '');
$booking_ref = trim($_POST['booking_ref'] ?? '');

$allowedCategories = ['billing', 'property', 'booking', 'other'];
if (!in_array($category, $allowedCategories, true)) {
    $category = 'other';
}

if (!$full_name || !$email || !$description) {
    $response['message'] = 'Please fill in your name, email, and a description of the issue.';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Please enter a valid email address.';
    echo json_encode($response);
    exit;
}

$bookingId = null;
if ($booking_ref !== '' && ctype_digit($booking_ref)) {
    $bookingId = (int)$booking_ref;
}

try {
    $pdo = getDB();

    if ($bookingId !== null) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE id = :id");
        $check->execute([':id' => $bookingId]);
        if ($check->fetchColumn() == 0) {
            $bookingId = null; // ignore invalid reference rather than blocking the report
        }
    }

    $insert = $pdo->prepare(
        "INSERT INTO issue_reports (booking_id, full_name, email, phone, category, description)
         VALUES (:booking_id, :full_name, :email, :phone, :category, :description)"
    );
    $insert->execute([
        ':booking_id' => $bookingId,
        ':full_name' => $full_name,
        ':email' => $email,
        ':phone' => $phone,
        ':category' => $category,
        ':description' => $description,
    ]);

    require_once __DIR__ . '/includes/notify.php';
    $settings = getSettings();
    $body = "New issue report:\n\nName: {$full_name}\nEmail: {$email}\nPhone: {$phone}\n"
        . "Category: {$category}\nBooking ref: " . ($bookingId ?? 'N/A') . "\n\n{$description}";
    if (!empty($settings['owner_whatsapp_number'])) {
        send_whatsapp_message($settings['owner_whatsapp_number'], "[Issue report] {$full_name} ({$category}): " . mb_substr($description, 0, 300));
    }
    if (!empty($settings['notify_email'])) {
        $dashboardLink = !empty($settings['site_url']) ? rtrim($settings['site_url'], '/') . '/admin/issues.php' : null;
        send_email_message($settings['notify_email'], 'New issue report: Quattro Homes', $body, $dashboardLink ? 'View Issue in Dashboard' : null, $dashboardLink, 'New issue reported');
    }

    $response['success'] = true;
    $response['message'] = "Thank you, {$full_name}. Your report has been received and we'll get back to you shortly.";
} catch (PDOException $e) {
    $response['message'] = 'Something went wrong. Please try again or contact us via WhatsApp.';
}

echo json_encode($response);
