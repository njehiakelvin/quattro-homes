-- Quattro Homes — migration script
-- Run this once against your EXISTING database to bring it up to date,
-- if you set up the database before the floor/reviews/issues/referrals/
-- WhatsApp features were added. Safe to run even if some parts are
-- already applied — see notes per section below.

USE quattro_homes;

-- 1. Add the `floor` column to bookings, if it doesn't already exist.
--    If you get "Duplicate column name 'floor'", this step is already done — skip it.
ALTER TABLE bookings
  ADD COLUMN floor ENUM('floor1','floor2','both') NOT NULL DEFAULT 'floor2' AFTER checkout_date;

-- 2. Add the `checked_out_at` column, if it doesn't already exist.
--    If you get a duplicate-column error, this step is already done — skip it.
ALTER TABLE bookings
  ADD COLUMN checked_out_at TIMESTAMP NULL DEFAULT NULL AFTER status;

-- 2b. Add the `is_blocked` column (for admin-created manual date blocks), if it
--     doesn't already exist. If you get a duplicate-column error, skip it.
ALTER TABLE bookings
  ADD COLUMN is_blocked TINYINT(1) NOT NULL DEFAULT 0 AFTER status;

-- 3. Create any newer tables that don't exist yet (safe to re-run — no-ops if already present).
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    excerpt VARCHAR(300) DEFAULT NULL,
    content MEDIUMTEXT NOT NULL,
    cover_image VARCHAR(255) DEFAULT NULL,
    meta_description VARCHAR(300) DEFAULT NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(60) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- 4. Add any newer settings keys that don't exist yet. Uses INSERT IGNORE, so this
--    is always safe to re-run — it will NOT overwrite any settings you've already
--    configured in the admin panel, it only adds keys that are missing.
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
    ('price_per_night', '4500'),
    ('currency', 'KES'),
    ('min_stay_nights', '1'),
    ('whatsapp_number', '254733545858'),
    ('contact_email', 'info@quattrohomes.co.ke'),
    ('notify_email', 'gilbert@quattrohomes.co.ke'),
    ('discount_percent', '0'),
    ('discount_min_nights', '7'),
    ('included_guests', '2'),
    ('extra_guest_fee', '0'),
    ('referral_discount_percent', '10'),
    ('referral_reward_percent', '10'),
    ('whatsapp_access_token', ''),
    ('smtp_host', ''),
    ('smtp_port', '587'),
    ('smtp_username', ''),
    ('smtp_password', ''),
    ('smtp_from_email', 'info@quattrohomes.co.ke'),
    ('smtp_from_name', 'Quattro Homes'),
    ('whatsapp_phone_number_id', ''),
    ('owner_whatsapp_number', '254733545858'),
    ('default_country_code', '254'),
    ('notifications_channel', 'whatsapp'),
    ('at_username', ''),
    ('at_api_key', ''),
    ('at_sender_id', ''),
    ('whatsapp_verify_token', ''),
    ('auto_reply_bot_enabled', '1'),
    ('site_url', 'https://quattrohomes.co.ke'),
    ('wifi_ssid', ''),
    ('wifi_password', '');

-- Done. Existing bookings will show as "Floor 2" (the default) since there's no way
-- to know retroactively which unit they were actually for — you may want to manually
-- update older rows via phpMyAdmin, e.g.:
--   UPDATE bookings SET floor = 'floor1' WHERE id IN (1, 4, 7);
