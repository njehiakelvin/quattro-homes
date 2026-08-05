<?php
require_once __DIR__ . '/includes/db.php';
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
    $response['message'] = 'Thank you for your feedback!';
    echo json_encode($response);
    exit;
}

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    $response['message'] = 'Your session expired. Please refresh the page and try again.';
    echo json_encode($response);
    exit;
}

if (!rate_limit_ok('review_submit', 20)) {
    $response['message'] = 'Please wait a moment before submitting again.';
    echo json_encode($response);
    exit;
}

$booking_ref = trim($_POST['booking_ref'] ?? '');
$email       = trim($_POST['email'] ?? '');
$rating      = (int)($_POST['rating'] ?? 0);
$comment     = trim($_POST['comment'] ?? '');

if (!$booking_ref || !ctype_digit($booking_ref)) {
    $response['message'] = 'Please enter a valid booking reference number (from your confirmation).';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Please enter the email address you booked with.';
    echo json_encode($response);
    exit;
}

if ($rating < 1 || $rating > 5) {
    $response['message'] = 'Please select a star rating.';
    echo json_encode($response);
    exit;
}

try {
    $pdo = getDB();

    // Verify the booking exists, belongs to this email, and has a completed stay
    $stmt = $pdo->prepare(
        "SELECT id, full_name FROM bookings
         WHERE id = :id AND email = :email AND status = 'confirmed' AND checked_out_at IS NOT NULL"
    );
    $stmt->execute([':id' => (int)$booking_ref, ':email' => $email]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        $response['message'] = "We couldn't match that booking reference and email to a checked-out stay. "
            . "Reviews can only be left after your stay has been checked out by our team.";
        echo json_encode($response);
        exit;
    }

    // One review per booking
    $dupCheck = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE booking_id = :id");
    $dupCheck->execute([':id' => $booking['id']]);
    if ($dupCheck->fetchColumn() > 0) {
        $response['message'] = "You've already submitted a review for this stay. Thank you!";
        echo json_encode($response);
        exit;
    }

    $insert = $pdo->prepare(
        "INSERT INTO reviews (booking_id, full_name, email, rating, comment)
         VALUES (:booking_id, :full_name, :email, :rating, :comment)"
    );
    $insert->execute([
        ':booking_id' => $booking['id'],
        ':full_name' => $booking['full_name'],
        ':email' => $email,
        ':rating' => $rating,
        ':comment' => $comment,
    ]);

    $response['success'] = true;
    $response['message'] = 'Thank you for your review! It will appear on the site once approved.';
} catch (PDOException $e) {
    $response['message'] = 'Something went wrong. Please try again later.';
}

echo json_encode($response);
