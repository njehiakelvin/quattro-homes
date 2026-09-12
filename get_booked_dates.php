<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
header('Content-Type: application/json');

$floor = $_GET['floor'] ?? 'floor2';
if (!in_array($floor, ['floor1', 'floor2', 'both'], true)) {
    $floor = 'floor2';
}

// A booking blocks a given floor selection if it shares that floor, or if
// either booking is 'both' (whole-house), since that occupies every unit.
switch ($floor) {
    case 'floor1':
        $floorCondition = "floor IN ('floor1', 'both')";
        break;
    case 'floor2':
        $floorCondition = "floor IN ('floor2', 'both')";
        break;
    default: // 'both'. blocked by ANY existing booking on either floor
        $floorCondition = "floor IN ('floor1', 'floor2', 'both')";
}

try {
    $pdo = getDB();
    $stmt = $pdo->query(
        "SELECT checkin_date, checkout_date FROM bookings
         WHERE status != 'cancelled' AND checkout_date >= CURDATE() AND {$floorCondition}"
    );
    $ranges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $settings = getSettings();
    echo json_encode([
        'success' => true,
        'ranges' => $ranges,
        'floor' => $floor,
        'price_per_night' => (float)$settings['price_per_night'],
        'currency' => $settings['currency'],
        'min_stay_nights' => (int)$settings['min_stay_nights'],
        'discount_percent' => (float)$settings['discount_percent'],
        'discount_min_nights' => (int)$settings['discount_min_nights'],
        'included_guests' => (int)$settings['included_guests'],
        'extra_guest_fee' => (float)$settings['extra_guest_fee'],
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'ranges' => []]);
}
