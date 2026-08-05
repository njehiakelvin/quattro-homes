<?php
require_once __DIR__ . '/settings.php';

/**
 * Normalizes a phone number to E.164-ish digits only (no +), assuming the
 * configured default country code for local-format numbers (e.g. 07XX...).
 */
function normalize_phone($phone, $defaultCountryCode = '254') {
    $digits = preg_replace('/\D/', '', (string)$phone);
    if ($digits === '') return '';
    if (strpos($digits, '0') === 0) {
        $digits = $defaultCountryCode . substr($digits, 1);
    } elseif (strpos($digits, $defaultCountryCode) !== 0 && strlen($digits) <= 10) {
        $digits = $defaultCountryCode . $digits;
    }
    return $digits;
}

/**
 * Sends a WhatsApp text message via Meta's WhatsApp Cloud API.
 * Requires 'whatsapp_access_token' and 'whatsapp_phone_number_id' to be set
 * in the settings table (Meta Business > WhatsApp > API Setup). Fails
 * silently (returns false) if not configured or the request errors, so a
 * missing/expired token never blocks a booking from completing.
 */
function send_whatsapp_message($toPhone, $message) {
    $settings = getSettings();
    $token = $settings['whatsapp_access_token'] ?? '';
    $phoneId = $settings['whatsapp_phone_number_id'] ?? '';
    if (!$token || !$phoneId) {
        return false; // WhatsApp API not configured yet — see admin settings panel
    }

    $to = normalize_phone($toPhone, $settings['default_country_code'] ?? '254');
    if (!$to) return false;

    $url = "https://graph.facebook.com/v20.0/{$phoneId}/messages";
    $payload = json_encode([
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'text',
        'text' => ['body' => $message],
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json",
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
    ]);
    $result = @curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode >= 200 && $httpCode < 300;
}

/** Best-effort email send; never throws. */
function send_email_message($to, $subject, $body) {
    if (!$to) return false;
    return @mail($to, $subject, $body, "From: no-reply@quattrohomes.example");
}

/**
 * Sends an SMS via Africa's Talking. Requires 'at_username' and 'at_api_key'
 * in settings (from africastalking.com dashboard). Fails silently if not
 * configured, same pattern as WhatsApp — never blocks the booking flow.
 */
function send_sms($toPhone, $message) {
    $settings = getSettings();
    $username = $settings['at_username'] ?? '';
    $apiKey = $settings['at_api_key'] ?? '';
    if (!$username || !$apiKey) return false;

    $to = '+' . normalize_phone($toPhone, $settings['default_country_code'] ?? '254');
    $url = $username === 'sandbox'
        ? 'https://api.sandbox.africastalking.com/version1/messaging'
        : 'https://api.africastalking.com/version1/messaging';

    $fields = [
        'username' => $username,
        'to' => $to,
        'message' => $message,
    ];
    if (!empty($settings['at_sender_id'])) {
        $fields['from'] = $settings['at_sender_id'];
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($fields),
        CURLOPT_HTTPHEADER => [
            "apiKey: {$apiKey}",
            "Content-Type: application/x-www-form-urlencoded",
            "Accept: application/json",
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
    ]);
    $result = @curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode >= 200 && $httpCode < 300;
}

/**
 * Triggers an automated voice call via Africa's Talking Voice API. Intended
 * only for genuinely urgent, time-sensitive cases (e.g. a same-day
 * cancellation) — not for routine updates, since calls are more intrusive.
 * NOTE: Voice requires a callback URL configured in your Africa's Talking
 * account to serve the call's XML response (what the caller hears); this
 * function only triggers the outbound call itself.
 */
function trigger_voice_call($toPhone) {
    $settings = getSettings();
    $username = $settings['at_username'] ?? '';
    $apiKey = $settings['at_api_key'] ?? '';
    if (!$username || !$apiKey || empty($settings['at_sender_id'])) return false;

    $to = '+' . normalize_phone($toPhone, $settings['default_country_code'] ?? '254');
    $url = $username === 'sandbox'
        ? 'https://voice.sandbox.africastalking.com/call'
        : 'https://voice.africastalking.com/call';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'username' => $username,
            'from' => $settings['at_sender_id'],
            'to' => $to,
        ]),
        CURLOPT_HTTPHEADER => [
            "apiKey: {$apiKey}",
            "Content-Type: application/x-www-form-urlencoded",
            "Accept: application/json",
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
    ]);
    $result = @curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode >= 200 && $httpCode < 300;
}

/**
 * Sends a booking lifecycle notification to the guest (WhatsApp preferred,
 * email as backup/always-on) and a short internal alert to the owner.
 * $event is one of: created, confirmed, cancelled, checked_out
 */
function notify_booking_event(array $booking, string $event) {
    $settings = getSettings();
    $currency = $settings['currency'] ?? 'KES';
    $channel = $settings['notifications_channel'] ?? 'whatsapp'; // whatsapp | email | both
    $name = $booking['full_name'] ?? 'Guest';
    $checkin = $booking['checkin_date'] ?? '';
    $checkout = $booking['checkout_date'] ?? '';
    $total = isset($booking['total_price']) ? number_format((float)$booking['total_price']) : null;
    $bookingRef = 'QH' . (int)($booking['id'] ?? 0);

    $siteUrl = rtrim($settings['site_url'] ?? '', '/');
    $reportLink = $siteUrl ? "{$siteUrl}/report-issue.php?booking={$booking['id']}" : '';
    $wifiLine = '';
    if (!empty($settings['wifi_ssid'])) {
        $wifiLine = "\n\nWiFi: {$settings['wifi_ssid']}" . (!empty($settings['wifi_password']) ? " / Password: {$settings['wifi_password']}" : '');
    }

    $templates = [
        'created' => [
            'subject' => 'Booking request received — Quattro Homes',
            'guest' => "Hi {$name}, thank you for your booking request with Quattro Homes.\n\n"
                . "Dates: {$checkin} to {$checkout}\n"
                . ($total ? "Estimated total: {$currency} {$total}\n" : "")
                . "Booking ref: {$bookingRef}\n\n"
                . "We'll confirm shortly. Reply here on WhatsApp if you have any questions.",
        ],
        'confirmed' => [
            'subject' => 'Your stay is confirmed — Quattro Homes',
            'guest' => "Good news, {$name}! Your stay at Quattro Homes is confirmed.\n\n"
                . "Dates: {$checkin} to {$checkout}\n"
                . ($total ? "Total: {$currency} {$total}\n" : "")
                . "Booking ref: {$bookingRef}"
                . $wifiLine
                . "\n\nWe look forward to hosting you. If anything comes up during your stay, report it here: "
                . ($reportLink ?: '(contact us on WhatsApp)')
                . "\n\nMessage us anytime on WhatsApp if you need anything before check-in.",
        ],
        'cancelled' => [
            'subject' => 'Your booking was cancelled — Quattro Homes',
            'guest' => "Hi {$name}, your booking (ref {$bookingRef}) for {$checkin} to {$checkout} has been cancelled.\n\n"
                . "If this wasn't expected, or you'd like to rebook different dates, just reply here on WhatsApp.",
        ],
        'checked_out' => [
            'subject' => 'Thank you for staying with Quattro Homes',
            'guest' => "Thank you for staying with us, {$name}! We hope you had a great time.\n\n"
                . "Your booking ref {$bookingRef} is now marked as checked out. We'd love a quick review on our site.\n\n"
                . "Here's your personal referral link — share it with friends and you'll both save on your next stays:\n"
                . ($siteUrl ? "{$siteUrl}/index.php?ref={$bookingRef}#book" : "{$bookingRef} (mention this code when they book)"),
        ],
    ];

    if (!isset($templates[$event])) return;
    $t = $templates[$event];

    // Guest notification
    if (!empty($booking['phone']) && ($channel === 'whatsapp' || $channel === 'both')) {
        $sentWhatsapp = send_whatsapp_message($booking['phone'], $t['guest']);
        // If WhatsApp isn't configured/delivered, SMS is the next-best "phone-first" fallback
        if (!$sentWhatsapp) {
            send_sms($booking['phone'], $t['guest']);
        }
    }
    if (!empty($booking['email']) && ($channel === 'email' || $channel === 'both' || $channel === 'whatsapp')) {
        // Email always sent as a reliable fallback even in "whatsapp" mode,
        // since WhatsApp delivery depends on API credentials being configured.
        send_email_message($booking['email'], $t['subject'], $t['guest']);
    }

    // Urgent escalation: a cancellation within 24 hours of check-in is time-critical,
    // so place an automated call in addition to the message (if voice is configured).
    if ($event === 'cancelled' && !empty($booking['phone']) && !empty($checkin)) {
        $hoursToCheckin = (strtotime($checkin) - time()) / 3600;
        if ($hoursToCheckin >= 0 && $hoursToCheckin <= 24) {
            trigger_voice_call($booking['phone']);
        }
    }

    // Internal owner alert (short, WhatsApp-first)
    $ownerMsg = "[{$event}] {$name} — {$checkin} to {$checkout} — ref {$bookingRef}"
        . ($total ? " — {$currency} {$total}" : '');
    if (!empty($settings['owner_whatsapp_number'])) {
        send_whatsapp_message($settings['owner_whatsapp_number'], $ownerMsg);
    }
    if (!empty($settings['notify_email'])) {
        send_email_message($settings['notify_email'], "[Quattro Homes] Booking {$event}: {$bookingRef}", $ownerMsg);
    }
}
