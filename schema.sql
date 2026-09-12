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
    is_blocked TINYINT(1) NOT NULL DEFAULT 0,
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

INSERT IGNORE INTO blog_posts (title, slug, excerpt, content, meta_description, status) VALUES
(
  'A First-Timer''s Guide to Bungoma, Kenya',
  'guide-to-bungoma-kenya',
  'Planning a trip to Bungoma? Here is what to know about getting around, what to see, and where to stay.',
  '<p>Bungoma is a growing town in Western Kenya, known for its proximity to Mount Elgon and its role as a trading hub between Kenya and Uganda. Whether you are visiting for business, a family event, or simply passing through, a little local knowledge goes a long way.</p><h2>Getting there</h2><p>Bungoma is well served by road from both Nairobi and Kisumu, with regular bus and matatu services. If you are flying in, Eldoret International Airport is the closest option, with onward road transport into town.</p><h2>Where to stay</h2><p>For visitors who want more space and privacy than a standard hotel room, a fully-furnished apartment is worth considering. Quattro Homes offers two independent 2-bedroom units in Bungoma, each with a private kitchen, secure parking, and fast WiFi, ideal for families, business travelers, or longer stays.</p><h2>Getting around</h2><p>Boda bodas (motorcycle taxis) and taxis are the most common way to get around town. For longer stays, having a base with parking, like an apartment rather than a hotel room, makes day-to-day errands much easier.</p>',
  'A practical first-timer guide to visiting Bungoma, Kenya: getting there, where to stay, and getting around town.',
  'published'
),
(
  'Apartment vs Hotel: What Makes More Sense for Your Bungoma Stay?',
  'apartment-vs-hotel-bungoma',
  'Comparing serviced apartments and hotel rooms for stays in Bungoma, from cost to comfort to privacy.',
  '<p>When booking a stay in Bungoma, travelers usually choose between a hotel room and a serviced apartment. Both have their place, but they suit different kinds of trips.</p><h2>Space and privacy</h2><p>A hotel room is built for one or two people passing through. A 2-bedroom apartment, like the units at Quattro Homes, gives families or small groups separate sleeping areas, a shared living room, and a private kitchen, without sharing hallways or common areas with strangers.</p><h2>Cost for groups</h2><p>Splitting the cost of one apartment across a family or group is often more efficient than booking multiple hotel rooms, especially for stays of several nights.</p><h2>Cooking your own meals</h2><p>A fully equipped kitchen means you are not limited to restaurant hours or hotel menus, useful for families with children, dietary needs, or simply a preference for home-cooked food during a longer stay.</p><h2>When a hotel still makes sense</h2><p>For a single traveler on a one-night stopover, a hotel room can be simpler. But for family visits, business trips of more than a couple of nights, or medical and academic visits where you want a stable base, an apartment tends to offer better value and comfort.</p>',
  'Comparing hotels and serviced apartments for a stay in Bungoma, Kenya, covering space, cost, and comfort.',
  'published'
);

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
    ('wifi_password', '')
ON DUPLICATE KEY UPDATE setting_key = setting_key; -- never overwrite saved settings

-- Default admin login: username "admin" / password "change_me_now"
-- Generate a real hash before going live:
--   php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"
-- then UPDATE admin_users SET password_hash = '...' WHERE username = 'admin';
INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$L9m1v6Q1qk4S8G0z0zC7f.exampleCHANGEME000000000000000000')
ON DUPLICATE KEY UPDATE username = username;
