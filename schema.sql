-- Quattro Homes Booking System Database Schema (v2)
CREATE DATABASE IF NOT EXISTS quattro_homes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quattro_homes;

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    floor ENUM('floor1','floor2','both') NOT NULL DEFAULT 'floor2',
    nights INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) DEFAULT NULL,
    guests INT NOT NULL DEFAULT 1,
    purpose VARCHAR(50) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    checked_out_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT DEFAULT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS issue_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT DEFAULT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    category VARCHAR(40) NOT NULL DEFAULT 'other',
    description TEXT NOT NULL,
    status ENUM('open','in_progress','resolved') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL,
    referrer_booking_id INT NOT NULL,
    referred_booking_id INT NOT NULL,
    reward_status ENUM('pending','issued') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (referred_booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS whatsapp_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(30) NOT NULL UNIQUE,
    state VARCHAR(40) NOT NULL DEFAULT 'menu',
    data JSON DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(60) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
    ('price_per_night', '4500'),
    ('currency', 'KES'),
    ('min_stay_nights', '1'),
    ('whatsapp_number', '254733545858'),
    ('contact_email', 'info@quattrohomes.example'),
    ('notify_email', 'owner@quattrohomes.example'),
    ('discount_percent', '0'),
    ('discount_min_nights', '7'),
    ('included_guests', '2'),
    ('extra_guest_fee', '0'),
    ('referral_discount_percent', '10'),
    ('referral_reward_percent', '10'),
    ('whatsapp_access_token', ''),
    ('whatsapp_phone_number_id', ''),
    ('owner_whatsapp_number', '254733545858'),
    ('default_country_code', '254'),
    ('notifications_channel', 'whatsapp'),
    ('at_username', ''),
    ('at_api_key', ''),
    ('at_sender_id', ''),
    ('whatsapp_verify_token', ''),
    ('auto_reply_bot_enabled', '1'),
    ('site_url', 'https://quattrohomes.example'),
    ('wifi_ssid', ''),
    ('wifi_password', '')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Default admin login: username "admin" / password "change_me_now"
-- Generate a real hash before going live:
--   php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"
-- then UPDATE admin_users SET password_hash = '...' WHERE username = 'admin';
INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$L9m1v6Q1qk4S8G0z0zC7f.exampleCHANGEME000000000000000000')
ON DUPLICATE KEY UPDATE username = username;
