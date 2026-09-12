# Quattro Homes — Booking Website (v6)

A PHP + MySQL booking site for Quattro Homes (Bungoma, Kenya). Domain:
quattrohomes.co.ke. Two floors/units, each independently bookable, WhatsApp-first
guest communication, branded HTML emails, and a small blog for SEO.

## What's new since v6

- **Dark / light theme toggle** — a moon/sun button next to the language toggle
  on every page. Preference is remembered (localStorage) and respects the
  visitor's OS preference on first visit. Header, hero, footer, and buttons
  stay their brand dark-green/gold regardless of theme (they're designed to);
  the rest of each page's background/text inverts.
- **Photos given much more presence**:
  - The homepage hero is now a rotating photo carousel (auto-advances every
    5 seconds, with click-to-jump dots) instead of a single static image.
  - New "See It For Yourself" photo strip on the homepage, a larger featured
    grid linking straight to the full gallery.
  - The gallery page itself now shows fewer, much larger photos per row.
  - Every gallery photo opens in a full-screen lightbox on click (arrow keys,
    on-screen prev/next, and a counter), instead of just sitting as a static
    thumbnail grid.

## Replacing the Floor 1 photos

When you upload the new Floor 1 batch, keep the same filenames as the current
set in `images/floor1/` (or update the `<img src="...">` paths in `gallery.php`,
`index.php`, and `about.php` if the filenames change) and the site picks them
up automatically, no other code changes needed.

## What's new in v6

- **Blog system** for SEO: `blog.php` (listing), `blog-post.php` (article, with
  Article structured data), and `admin/blog.php` (create/edit/delete posts,
  draft/published status). Seeded with two starter posts in `schema.sql`.
- **Dynamic sitemap** (`sitemap.php`) replacing the static `sitemap.xml` —
  automatically includes every published blog post. `robots.txt` updated to
  point at it.
- **Real SMTP email via PHPMailer** (vendored into `includes/PHPMailer/`,
  no Composer required) instead of PHP's bare `mail()`. Configure your vendor's
  SMTP details for `info@quattrohomes.co.ke` in Settings; falls back to
  `mail()` automatically if SMTP host is left blank.
- **Branded HTML emails with CTA buttons** — every notification (booking
  received/confirmed/cancelled/checked-out, issue reports, admin alerts) now
  renders as a styled HTML email with a clear call-to-action button (WhatsApp
  chat, report an issue, rebook, leave a review, view in dashboard), not just
  plain text.
- Admin alerts now go to **gilbert@quattrohomes.co.ke** by default; guest-facing
  "From" address defaults to **info@quattrohomes.co.ke**.
- Domain set to **quattrohomes.co.ke** throughout (sitemap, robots.txt, default
  Site URL setting).
- "Mark as Occupied" (external bookings from Airbnb/Booking.com/walk-ins) and
  booking delete, from the previous update, remain in `admin/dashboard.php`.

## Site structure (pages)

| Page | Purpose |
|---|---|
| `index.php` | Home |
| `about.php` | About Us |
| `gallery.php` | Photo gallery (Floor 2 / Floor 1 tabs) |
| `pricing.php` | Pricing + referral explainer |
| `book.php` | Booking form + floor-aware availability calendar |
| `testimonials.php` | Guest reviews + review form |
| `blog.php` / `blog-post.php` | Blog listing and article pages |
| `faq.php` | House rules + FAQ |
| `contact.php` | Contact info + map |
| `report-issue.php` | Private support page (linked only from confirmation emails, `noindex`) |

Admin: `admin/dashboard.php` (bookings), `reviews.php`, `issues.php`,
`referrals.php`, `blog.php` (post management), `settings.php`.

## Setup

1. **Create the database**: `mysql -u root -p < schema.sql`
   (If upgrading an existing install, run `migration.sql` instead/first.)
2. **Configure the connection** in `includes/db.php`.
3. **Set your admin password**:
   ```
   php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"
   ```
   ```sql
   UPDATE admin_users SET password_hash = 'PASTE_HASH_HERE' WHERE username = 'admin';
   ```
4. **Configure email (important)** — go to `admin/settings.php` → Email (SMTP)
   and fill in your vendor's SMTP host/port/username/password for
   `info@quattrohomes.co.ke`. Common examples: Zoho Mail (`smtp.zoho.com`,
   port 587), Google Workspace (`smtp.gmail.com`, port 587 with an app
   password), or whatever your hosting/email provider gives you. Until this
   is filled in, email falls back to the server's basic `mail()` function,
   which many hosts either block or mark as spam, so this step matters.
5. **Configure WhatsApp/SMS/site URL** as before, also in `admin/settings.php`.
6. Run on any PHP 7.4+/8.x + MySQL server, visit `index.php`. Admin at
   `admin/login.php`.

## Writing blog posts

From `admin/blog.php`: title, URL slug (auto-generated from the title if left
blank), excerpt (shown on the listing page), meta description (for search
engines), an optional cover image path, and content (basic HTML — `<p>` and
`<h2>` tags work well). Save as Draft to keep it unpublished, or Published to
make it live and included in the sitemap automatically.

## Note on previewing

The site is multi-page and database-backed, so it needs a real PHP + MySQL
server to view (XAMPP, Laragon, or any LAMP host) — no standalone preview file.

## Still worth adding later

- A proper captcha if spam becomes an issue beyond the honeypot/rate-limit
- Distinct whole-house rate for "Both floors" instead of a flat 2x multiplier
- Free-form AI-driven WhatsApp conversation (current bot is rule-based/menu-driven)
- Floor 1 photo reshoot with consistent white lighting
- A rich-text editor for blog post content instead of raw HTML textarea
