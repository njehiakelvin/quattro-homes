<?php
/**
 * WhatsApp webhook endpoint.
 *
 * Set this URL (https://yourdomain.com/whatsapp_webhook.php) in Meta Business >
 * WhatsApp > Configuration > Webhook, with the same Verify Token you set in
 * the admin settings panel ("WhatsApp Verify Token").
 *
 * This implements a simple, rule-based menu bot — not free-form AI chat.
 * It covers: enquiries, starting a booking, checking a booking's status,
 * and handing off to a human. Toggle it off entirely from the admin
 * settings panel ("Auto-reply bot") if you'd rather handle WhatsApp manually.
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/notify.php';

$settings = getSettings();

// --- Step 1: webhook verification (Meta calls this once when you save the webhook URL) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mode = $_GET['hub_mode'] ?? '';
    $token = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? '';
    if ($mode === 'subscribe' && $token !== '' && hash_equals($settings['whatsapp_verify_token'] ?? '', $token)) {
        echo $challenge;
        exit;
    }
    http_response_code(403);
    exit;
}

// --- Step 2: incoming messages ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (($settings['auto_reply_bot_enabled'] ?? '1') !== '1') {
    http_response_code(200); // bot disabled — acknowledge but do nothing
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

$message = $payload['entry'][0]['changes'][0]['value']['messages'][0] ?? null;
if (!$message || ($message['type'] ?? '') !== 'text') {
    http_response_code(200); // ignore non-text messages (images, statuses, etc.)
    exit;
}

$from = $message['from'];
$text = trim($message['text']['body'] ?? '');
$textLower = strtolower($text);

$pdo = getDB();

function get_session($pdo, $phone) {
    $stmt = $pdo->prepare("SELECT * FROM whatsapp_sessions WHERE phone = :phone");
    $stmt->execute([':phone' => $phone]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $pdo->prepare("INSERT INTO whatsapp_sessions (phone, state, data) VALUES (:phone, 'menu', NULL)")
            ->execute([':phone' => $phone]);
        return ['phone' => $phone, 'state' => 'menu', 'data' => null];
    }
    $row['data'] = $row['data'] ? json_decode($row['data'], true) : [];
    return $row;
}

function save_session($pdo, $phone, $state, $data) {
    $stmt = $pdo->prepare("UPDATE whatsapp_sessions SET state = :state, data = :data WHERE phone = :phone");
    $stmt->execute([':state' => $state, ':data' => json_encode($data), ':phone' => $phone]);
}

function reply($to, $text) {
    send_whatsapp_message($to, $text);
}

const MENU_TEXT = "Welcome to Quattro Homes! How can we help?\n\n"
    . "1️⃣ Book a stay\n"
    . "2️⃣ Check availability & pricing\n"
    . "3️⃣ Check my booking status\n"
    . "4️⃣ Talk to a human\n\n"
    . "Reply with a number, or type *menu* anytime to see this again.";

$session = get_session($pdo, $from);
$state = $session['state'];
$data = $session['data'] ?: [];

// Global resets
if (in_array($textLower, ['menu', 'hi', 'hello', 'start', 'hey'], true)) {
    save_session($pdo, $from, 'menu', []);
    reply($from, MENU_TEXT);
    exit;
}

switch ($state) {

    case 'menu':
        if ($text === '1') {
            save_session($pdo, $from, 'booking_dates', []);
            reply($from, "Great! What dates would you like to stay?\n\nReply like: *12-08-2026 to 15-08-2026*");
        } elseif ($text === '2') {
            $price = (float)$settings['price_per_night'];
            $currency = $settings['currency'];
            reply($from, "Our rate is {$currency} " . number_format($price) . " per night (2 guests included).\n\n"
                . "To check specific dates, reply *1* to start a booking, or visit our site's Availability calendar.");
            save_session($pdo, $from, 'menu', []);
        } elseif ($text === '3') {
            save_session($pdo, $from, 'status_ref', []);
            reply($from, "Sure — what's your booking reference number? (e.g. 1023, from your confirmation)");
        } elseif ($text === '4') {
            save_session($pdo, $from, 'human', []);
            reply($from, "No problem — a member of our team will reach out to you here shortly.");
            if (!empty($settings['owner_whatsapp_number'])) {
                send_whatsapp_message($settings['owner_whatsapp_number'], "[Bot handoff] Guest {$from} wants to talk to a human.");
            }
        } else {
            reply($from, MENU_TEXT);
        }
        break;

    case 'booking_dates':
        if (preg_match('/(\d{1,2}-\d{1,2}-\d{4})\s*to\s*(\d{1,2}-\d{1,2}-\d{4})/i', $text, $m)) {
            $checkin = DateTime::createFromFormat('d-m-Y', $m[1]);
            $checkout = DateTime::createFromFormat('d-m-Y', $m[2]);
            if ($checkin && $checkout && $checkout > $checkin) {
                $data['checkin'] = $checkin->format('Y-m-d');
                $data['checkout'] = $checkout->format('Y-m-d');
                save_session($pdo, $from, 'booking_details', $data);
                reply($from, "Got it: {$m[1]} to {$m[2]}.\n\nNow send your *full name* and *number of guests*, like:\n*Jane Doe, 2*");
            } else {
                reply($from, "That check-out date needs to be after check-in. Try again, e.g. *12-08-2026 to 15-08-2026*");
            }
        } else {
            reply($from, "Please use this format: *12-08-2026 to 15-08-2026*");
        }
        break;

    case 'booking_details':
        if (strpos($text, ',') !== false) {
            [$name, $guestsRaw] = array_map('trim', explode(',', $text, 2));
            $guests = max(1, (int)preg_replace('/\D/', '', $guestsRaw));
            if ($name === '') {
                reply($from, "Please include your name, like: *Jane Doe, 2*");
                break;
            }

            $checkin = $data['checkin'];
            $checkout = $data['checkout'];
            $nights = (int)((strtotime($checkout) - strtotime($checkin)) / 86400);
            $price = (float)$settings['price_per_night'];
            $total = $price * $nights;

            // Check for overlap before creating
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM bookings WHERE status != 'cancelled' AND checkin_date < :checkout AND checkout_date > :checkin"
            );
            $stmt->execute([':checkout' => $checkout, ':checkin' => $checkin]);
            if ($stmt->fetchColumn() > 0) {
                reply($from, "Sorry, those dates are already booked. Reply *1* to try different dates.");
                save_session($pdo, $from, 'menu', []);
                break;
            }

            $insert = $pdo->prepare(
                "INSERT INTO bookings (full_name, email, phone, checkin_date, checkout_date, nights, total_price, guests, purpose, message)
                 VALUES (:name, '', :phone, :checkin, :checkout, :nights, :total, :guests, 'WhatsApp booking', 'Booked via WhatsApp bot')"
            );
            $insert->execute([
                ':name' => $name, ':phone' => $from, ':checkin' => $checkin, ':checkout' => $checkout,
                ':nights' => $nights, ':total' => $total, ':guests' => $guests,
            ]);
            $bookingId = (int)$pdo->lastInsertId();
            $currency = $settings['currency'];

            reply($from, "Thanks {$name}! Your booking request is in:\n\n"
                . "Ref: QH{$bookingId}\nDates: {$checkin} to {$checkout}\nEst. total: {$currency} " . number_format($total) . "\n\n"
                . "We'll confirm shortly. Reply *menu* anytime for other options.");

            notify_booking_event([
                'id' => $bookingId, 'full_name' => $name, 'email' => '', 'phone' => $from,
                'checkin_date' => $checkin, 'checkout_date' => $checkout, 'total_price' => $total,
            ], 'created');

            save_session($pdo, $from, 'menu', []);
        } else {
            reply($from, "Please send your name and guest count separated by a comma, like: *Jane Doe, 2*");
        }
        break;

    case 'status_ref':
        $ref = preg_replace('/\D/', '', $text);
        if ($ref === '') {
            reply($from, "Please send just the reference number, e.g. *1023*");
            break;
        }
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id AND phone = :phone");
        $stmt->execute([':id' => (int)$ref, ':phone' => $from]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($booking) {
            reply($from, "Booking QH{$booking['id']}: {$booking['checkin_date']} to {$booking['checkout_date']}\n"
                . "Status: " . ucfirst($booking['status'])
                . (!empty($booking['checked_out_at']) ? " (checked out)" : ""));
        } else {
            reply($from, "We couldn't find a booking with that reference on this number. Reply *4* to talk to a human, or *menu* to start over.");
        }
        save_session($pdo, $from, 'menu', []);
        break;

    case 'human':
        // Session stays in 'human' state until guest types 'menu' — messages just pass through to staff manually.
        break;

    default:
        save_session($pdo, $from, 'menu', []);
        reply($from, MENU_TEXT);
}

http_response_code(200);
