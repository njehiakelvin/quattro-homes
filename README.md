# Quattro Homes — Booking Website (v4)

A PHP + MySQL booking site for Quattro Homes (Bungoma, Kenya) — two floors/units,
each independently bookable, with WhatsApp-first guest communication.

## Feature summary

**Guest-facing site**
- Hero (real Floor 2 photo), About, Gallery (Floor 2 shown first, then Floor 1 — each
  labeled since they're different units with different finishes), Features, Pricing,
  Testimonials + review-submission form, House Rules, FAQ, Contact + map
- **Per-floor booking & availability**: guests choose Floor 1, Floor 2, or Both (whole
  house) — the calendar and live pricing adjust to match; booking one floor doesn't
  block the other, but "Both" is blocked by any booking on either floor
- Live booking form: nights & total price recalculate as dates/guests/floor change,
  long-stay and referral discounts applied automatically, live overlap checking
- **Referral program with auto-apply links**: after checkout, guests get a personal
  link (`?ref=QH1023`) — opening it auto-fills and applies the referral discount,
  no code to type
- EN / SW language toggle, mobile-responsive throughout
- Floating WhatsApp button (site-wide)
- Font Awesome icons throughout (replacing emoji)

**Admin dashboard** (`admin/`)
- `dashboard.php` — bookings list (with floor/unit shown), status updates, "mark
  checked out", and a full Settings panel: pricing, discounts, extra-guest fee,
  referral %, WhatsApp/SMS/voice config, WiFi details, site URL, auto-reply bot toggle
- `reviews.php` — approve/reject guest reviews before they go live
- `issues.php` — manage reported issues (open / in progress / resolved)
- `referrals.php` — track referral link usage and mark rewards as issued
- `login.php` / `logout.php` — session-based admin auth

**Notifications** (`includes/notify.php`)
- WhatsApp (via Meta's WhatsApp Cloud API) for booking created/confirmed/cancelled/
  checked-out — confirmation messages include WiFi details and a personal "report an
  issue" link; the checkout message includes the guest's referral link
- Email as an always-on fallback
- SMS backup + urgent automated voice call for same-day cancellations (via Africa's
  Talking)
- All channels degrade gracefully — if credentials aren't configured, sends are
  skipped silently rather than breaking the booking flow

**WhatsApp auto-reply bot** (`whatsapp_webhook.php`)
- Rule-based menu bot: book a stay, check pricing, check booking status, or hand off
  to a human. Conversation state stored in the `whatsapp_sessions` table. Toggle
  on/off from the admin settings panel

**Report an issue** — no longer a public site section. It's a standalone page
(`report-issue.php`) linked only from the booking-confirmed message, intended for
guests who are already staying or have stayed.

## File structure
```
quattro-homes/
├── index.php                 Main site
├── preview.html              Static, self-contained preview (mock data, no PHP needed)
├── report-issue.php          Guest-facing issue report page (linked via confirmation msg)
├── process_booking.php       Booking form handler (floor-aware validation, pricing, notify)
├── get_booked_dates.php      Booked dates + settings as JSON, filtered by floor
├── submit_review.php         Guest review submission handler
├── report_issue.php          Issue report form handler (backend for report-issue.php)
├── whatsapp_webhook.php      WhatsApp bot webhook (verification + message handling)
├── schema.sql                 Full database schema
├── includes/
│   ├── db.php                PDO database connection
│   ├── settings.php           Loads all settings from the DB (with sane defaults)
│   ├── security.php           CSRF token + basic rate limiter
│   └── notify.php             WhatsApp / SMS / voice / email notification helpers
├── admin/
│   ├── dashboard.php          Bookings (by floor) + Settings panel
│   ├── reviews.php            Review moderation
│   ├── issues.php             Issue report management
│   ├── referrals.php          Referral tracking
│   ├── login.php / logout.php / auth.php
│   └── admin.css
├── css/style.css
├── js/script.js
└── images/
    ├── floor1/, floor2/        Real property photos (used by the site)
    └── floor1_originals_backup/  Safety backup of Floor 1 originals
```

## Setup

1. **Create the database**
   ```
   mysql -u root -p < schema.sql
   ```

2. **Configure the connection** in `includes/db.php`.

3. **Set your admin password** — replace the placeholder hash:
   ```
   php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"
   ```
   ```sql
   UPDATE admin_users SET password_hash = 'PASTE_HASH_HERE' WHERE username = 'admin';
   ```

4. **Configure everything else from the admin panel** (`admin/dashboard.php` → Settings):
   - Price per night, discounts, extra-guest fee, referral percentages
   - **Site URL** — required for referral links and the report-issue link to work
   - **WiFi name/password** — included automatically in confirmation messages
   - **WhatsApp Cloud API**: Phone Number ID + Access Token (Meta Business Suite →
     WhatsApp → API Setup) — needed for automated WhatsApp notifications and the bot
   - **WhatsApp webhook**: set `https://yourdomain.com/whatsapp_webhook.php` as the
     webhook URL in Meta's dashboard, using the same Verify Token set in the panel
   - **Africa's Talking**: username + API key (africastalking.com) for SMS/voice.
     Use `sandbox` as the username while testing

   None of these are required for the site to function — booking, admin, reviews, and
   issue reporting all work without them. They only add the WhatsApp/SMS/voice layer.

5. **Run it** on any PHP 7.4+/8.x + MySQL server, then visit `index.php`.
   Visit `admin/login.php` to manage bookings, reviews, issues, and referrals.

## Previewing without a server

Open `preview.html` directly — same design and most interactions (calendar, floor
selector, live pricing, language toggle, floating WhatsApp button), but with CSS/JS
inlined and mock data instead of live database calls, so no PHP install is required.

## Still worth adding later

- Real SMTP email (PHPMailer) instead of PHP's built-in `mail()`
- A proper captcha if spam becomes an issue beyond the honeypot/rate-limit
- Distinct whole-house rate for "Both floors" instead of a flat 2× multiplier, if desired
- Free-form AI-driven WhatsApp conversation (current bot is rule-based/menu-driven)
- Floor 1 photo reshoot with consistent white lighting, and/or repainting the one
  purple wall — see prior notes on why color-correction alone couldn't fully fix this
- A true 360° virtual tour if panorama photos are captured later
