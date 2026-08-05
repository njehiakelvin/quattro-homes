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

// Honeypot: bots tend to fill every field, humans never see this one (hidden via CSS)
if (!empty($_POST['website'])) {
    // Silently pretend success so bots don't learn to avoid the trap
    $response['success'] = true;
    $response['message'] = 'Thank you! Your booking request has been received.';
    echo json_encode($response);
    exit;
}

// CSRF check
if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    $response['message'] = 'Your session expired. Please refresh the page and try again.';
    echo json_encode($response);
    exit;
}

// Basic rate limit: 1 submission per 30 seconds per session
if (!rate_limit_ok('booking_submit', 30)) {
    $response['message'] = 'Please wait a moment before submitting again.';
    echo json_encode($response);
    exit;
}

$settings = getSettings();

$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$checkin   = trim($_POST['checkin_date'] ?? '');
$checkout  = trim($_POST['checkout_date'] ?? '');
$guests    = (int)($_POST['guests'] ?? 1);
$purpose   = trim($_POST['purpose'] ?? '');
$message   = trim($_POST['message'] ?? '');
$referral_code = trim($_POST['referral_code'] ?? '');
$floor = trim($_POST['floor'] ?? 'floor2');
if (!in_array($floor, ['floor1', 'floor2', 'both'], true)) {
    $floor = 'floor2';
}

if (!$full_name || !$email || !$phone || !$checkin || !$checkout) {
    $response['message'] = 'Please fill in all required fields.';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Please enter a valid email address.';
    echo json_encode($response);
    exit;
}

$checkinDate = DateTime::createFromFormat('Y-m-d', $checkin);
$checkoutDate = DateTime::createFromFormat('Y-m-d', $checkout);

if (!$checkinDate || !$checkoutDate || $checkoutDate <= $checkinDate) {
    $response['message'] = 'Check-out date must be after check-in date.';
    echo json_encode($response);
    exit;
}

$nights = (int)$checkinDate->diff($checkoutDate)->days;
$minStay = (int)($settings['min_stay_nights'] ?? 1);
if ($nights < $minStay) {
    $response['message'] = "Minimum stay is {$minStay} night(s). Please adjust your dates.";
    echo json_encode($response);
    exit;
}

if ($guests < 1) { $guests = 1; }

$pricePerNight = (float)($settings['price_per_night'] ?? 0);
$discountPercent = (float)($settings['discount_percent'] ?? 0);
$discountMinNights = (int)($settings['discount_min_nights'] ?? 0);
$includedGuests = (int)($settings['included_guests'] ?? 2);
$extraGuestFee = (float)($settings['extra_guest_fee'] ?? 0);
$extraGuests = max(0, $guests - $includedGuests);
// Booking "both" floors is effectively two units, so the base rate doubles
$floorMultiplier = $floor === 'both' ? 2 : 1;
$subtotal = (($pricePerNight * $floorMultiplier * $nights)) + ($extraGuestFee * $extraGuests * $nights);
$discountApplies = $discountPercent > 0 && $discountMinNights > 0 && $nights >= $discountMinNights;
$totalPrice = $discountApplies ? $subtotal * (1 - $discountPercent / 100) : $subtotal;

try {
    $pdo = getDB();

    // A conflict exists if another booking shares this floor, or either booking is 'both'
    // (whole-house), since that occupies every unit.
    $floorCondition = $floor === 'both'
        ? "floor IN ('floor1', 'floor2', 'both')"
        : "floor IN (:floor, 'both')";

    $sql = "SELECT COUNT(*) FROM bookings
            WHERE status != 'cancelled'
            AND checkin_date < :checkout
            AND checkout_date > :checkin
            AND {$floorCondition}";
    $stmt = $pdo->prepare($sql);
    $params = [':checkout' => $checkout, ':checkin' => $checkin];
    if ($floor !== 'both') {
        $params[':floor'] = $floor;
    }
    $stmt->execute($params);

    if ($stmt->fetchColumn() > 0) {
        $response['message'] = 'Those dates include one or more days that are already booked. Please pick different check-in/check-out dates.';
        echo json_encode($response);
        exit;
    }

    // Referral code: format "QH" + referrer's booking ID (e.g. QH1023)
    $referrerBookingId = null;
    $referralDiscountPercent = (float)($settings['referral_discount_percent'] ?? 0);
    if ($referral_code !== '') {
        $codeDigits = preg_replace('/^QH/i', '', $referral_code);
        if (ctype_digit($codeDigits)) {
            $refCheck = $pdo->prepare(
                "SELECT id FROM bookings WHERE id = :id AND status = 'confirmed' AND checked_out_at IS NOT NULL AND email != :email"
            );
            $refCheck->execute([':id' => (int)$codeDigits, ':email' => $email]);
            $refRow = $refCheck->fetch(PDO::FETCH_ASSOC);
            if ($refRow) {
                $referrerBookingId = (int)$refRow['id'];
                // Apply whichever discount benefits the guest more, rather than stacking
                $referralTotal = $subtotal * (1 - $referralDiscountPercent / 100);
                if ($referralTotal < $totalPrice) {
                    $totalPrice = $referralTotal;
                    $discountApplies = true;
                    $discountPercent = $referralDiscountPercent;
                }
            } else {
                $response['message'] = 'That referral code is not valid. Please check it and try again, or leave it blank.';
                echo json_encode($response);
                exit;
            }
        } else {
            $response['message'] = 'That referral code format is not recognized. Please check it and try again, or leave it blank.';
            echo json_encode($response);
            exit;
        }
    }

    $insert = $pdo->prepare(
        "INSERT INTO bookings (full_name, email, phone, checkin_date, checkout_date, floor, nights, total_price, guests, purpose, message)
         VALUES (:full_name, :email, :phone, :checkin, :checkout, :floor, :nights, :total_price, :guests, :purpose, :message)"
    );
    $insert->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':phone' => $phone,
        ':checkin' => $checkin,
        ':checkout' => $checkout,
        ':floor' => $floor,
        ':nights' => $nights,
        ':total_price' => $totalPrice,
        ':guests' => $guests,
        ':purpose' => $purpose,
        ':message' => $message,
    ]);
    $newBookingId = (int)$pdo->lastInsertId();

    if ($referrerBookingId !== null) {
        $refInsert = $pdo->prepare(
            "INSERT INTO referrals (code, referrer_booking_id, referred_booking_id)
             VALUES (:code, :referrer, :referred)"
        );
        $refInsert->execute([
            ':code' => strtoupper($referral_code),
            ':referrer' => $referrerBookingId,
            ':referred' => $newBookingId,
        ]);
    }

    // Notify guest (WhatsApp-first, per settings) and owner
    require_once __DIR__ . '/includes/notify.php';
    $currency = $settings['currency'] ?? 'KES';
    $newBookingRow = [
        'id' => $newBookingId,
        'full_name' => $full_name,
        'email' => $email,
        'phone' => $phone,
        'checkin_date' => $checkin,
        'checkout_date' => $checkout,
        'total_price' => $totalPrice,
    ];
    notify_booking_event($newBookingRow, 'created');

    $floorLabel = ['floor1' => 'Floor 1', 'floor2' => 'Floor 2', 'both' => 'Both floors'][$floor];
    $discountLabel = $referrerBookingId !== null ? 'referral discount' : 'long-stay discount';
    $discountNote = $discountApplies ? " (includes {$discountPercent}% {$discountLabel})" : '';
    $response['success'] = true;
    $response['message'] = 'Thank you, ' . htmlspecialchars($full_name) . '! Your booking request for '
        . $floorLabel . ', ' . $nights . ' night(s) (est. ' . $currency . ' ' . number_format($totalPrice) . $discountNote
        . ') has been received. We will contact you shortly on WhatsApp/phone to confirm.';
} catch (PDOException $e) {
    $response['message'] = 'Something went wrong. Please try again or contact us via WhatsApp.';
}

echo json_encode($response);
