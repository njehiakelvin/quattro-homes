<?php
require_once __DIR__ . '/db.php';

function getSettings() {
    static $settings = null;
    if ($settings === null) {
        $settings = [
            'price_per_night' => '4500',
            'currency' => 'KES',
            'min_stay_nights' => '1',
            'whatsapp_number' => '254733545858',
            'contact_email' => 'info@quattrohomes.example',
            'notify_email' => 'owner@quattrohomes.example',
            'discount_percent' => '0',
            'discount_min_nights' => '7',
            'included_guests' => '2',
            'extra_guest_fee' => '0',
            'referral_discount_percent' => '10',
            'referral_reward_percent' => '10',
            'whatsapp_access_token' => '',
            'whatsapp_phone_number_id' => '',
            'owner_whatsapp_number' => '254733545858',
            'default_country_code' => '254',
            'notifications_channel' => 'whatsapp',
            'at_username' => '',
            'at_api_key' => '',
            'at_sender_id' => '',
            'whatsapp_verify_token' => '',
            'auto_reply_bot_enabled' => '1',
            'site_url' => 'https://quattrohomes.example',
            'wifi_ssid' => '',
            'wifi_password' => '',
        ];
        try {
            $pdo = getDB();
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
            foreach ($stmt->fetchAll(PDO::FETCH_KEY_PAIR) as $key => $value) {
                $settings[$key] = $value;
            }
        } catch (Exception $e) {
            // fall back to defaults above if DB/table isn't ready yet
        }
    }
    return $settings;
}
