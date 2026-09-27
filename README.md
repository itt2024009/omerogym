# OMERO GYM

A gym booking web application built for **ICT 1209 – Web Technologies** (mini project).
Members can browse classes and training sessions, reserve a slot by date and time,
and view their bookings on a personal dashboard.

**Theme:** Fitness & Gym Management

## Group Members

| Name | Register Number |
|------|--------------|
| Mohammed Ashfak | ITT/2024/011 |
| Mohamed Ahsan | ITT/2024/009 |

## Features

- Responsive multi-page site (works on mobile, tablet and desktop)
- Class and session catalog with live category filtering — now loaded from the
  database (`sessions_catalog` table) instead of being hardcoded in `app.js`
- Slot booking with a confirmation modal, now saved permanently in MySQL
- **Trainer picker on Book a Slot** — members can optionally pick a trainer for their
  session; trainers marked unavailable by the admin are shown greyed out and can't be
  selected (checked again on the server, not just the browser)
- **Messages page** — a feedback/contact box, linked first in the main navigation,
  saved to the `messages` table
- **Admin control panel** (`/admin/`) — the gym owner signs in through the SAME
  `login.html` form members use (no separate admin URL); `auth/login.php` checks
  the `admins` table too and routes the admin straight to `admin/dashboard.php`
  to manage workout plans & pricing, trainers, and member feedback
- Personal dashboard listing booked slots (with trainer, if one was picked), with cancel
- Login, registration and contact forms with real-time (client) + server-side validation
- Passwords hashed with bcrypt, prepared statements everywhere, PHP sessions for auth

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Structure | HTML5, CSS3, Bootstrap 5 |
| Styling | Custom CSS on top of Bootstrap (dark theme) |
| Logic | JavaScript (Vanilla) + fetch() calls to the backend |
| Backend | PHP 8 |
| Database | MySQL (via XAMPP/WAMP) |
| Version control | Git & GitHub |

## Pages

| File | Description |
|------|--------------|
| `login.html` | Member login form (posts to `auth/login.php`) |
| `register.html` | Account creation form (posts to `auth/register.php`) |
| `contact.php` | Messages / feedback form (linked first in the nav), saved to the `messages` table |
| `classes.html` | Class catalog with category filter (member-only), loaded from `sessions.php` |
| `book.html` | Booking form with trainer picker + confirmation (saves via `bookings/create.php`) |
| `workouts.html` | Member dashboard — list of bookings, with trainer (`bookings/list.php`) |
| `admin/seed_admin.php` | **Run once** to create the single fixed admin account |
| `admin/dashboard.php` | Admin overview with quick stats |
| `admin/sessions.php` | Admin: add / edit / delete workout plans & pricing |
| `admin/trainers.php` | Admin: add / edit / delete trainers, toggle availability |
| `admin/messages.php` | Admin: read / delete member feedback messages |

## Backend Structure (Phase 3)

```
OMERO-GYM/
├── includes/
│   ├── db.php            PDO connection to the omero_gym database
│   └── functions.php     shared helpers (session guard, JSON helpers, sanitising)
├── auth/
│   ├── register.php      creates a user, hashes password, starts session
│   ├── login.php         verifies credentials, starts session
│   ├── logout.php        destroys the session
│   └── check.php         used by app.js to confirm a member is logged in
├── bookings/
│   ├── create.php        saves a new booking for the logged-in member (+ trainer)
│   ├── list.php          returns the member's bookings (+ trainer)
│   └── cancel.php        deletes a booking (only if it belongs to the member)
├── sessions.php           public: returns the workout-plan catalog (admin-managed)
├── trainers.php            public: returns trainers + availability (admin-managed)
├── contact.php            Messages / contact form + handler → messages table
├── admin/                 admin control panel (signs in via the shared login.html)
│   ├── logout.php / seed_admin.php   (login.php just forwards to ../login.html)
│   ├── dashboard.php, sessions.php, trainers.php, messages.php
│   └── includes/          admin session guard + shared header/footer
├── index.php              redirects to classes.html or login.html
├── dashboard.php          redirects to workouts.html (the member dashboard)
├── database.sql           creates the database + all tables (see below)
├── app.js                 frontend logic — fetches catalog/trainers from the DB
└── *.html / *.css         frontend pages and styling
```

## Database

Tables created by `database.sql`:

- **users** — id, name, email, phone, password (hashed), created_at
- **messages** — id, name, email, message, created_at (from the Messages/contact form)
- **trainers** — id, name, specialty, available, created_at
- **sessions_catalog** — id, session_key, category, title, description, price, duration, created_at (workout plans / pricing, admin-managed)
- **admins** — id, name, email, password (hashed), created_at (the one fixed admin account)
- **bookings** — id, user_id, session_id, title, price, duration, trainer_id, trainer_name, booking_date, booking_time, status, created_at

## How to Run (Phase 3 — with backend)

1. Install **XAMPP** (or WAMP) and start **Apache** and **MySQL**.
2. Copy the whole `OMERO-GYM` folder into `htdocs` (XAMPP) or `www` (WAMP).
3. Open **phpMyAdmin** → **Import** → select `database.sql` → Go.
   This creates the `omero_gym` database with all tables above, and seeds a starter
   set of workout plans and trainers.
4. Check `includes/db.php` — the default XAMPP/WAMP settings (`root` / no password) are
   already filled in. Change them there if your MySQL setup is different.
5. The fixed admin account is already seeded by `database.sql`
   (`admin@omerogym.com` / `Omero@Gym2026` — change the password once you've
   logged in). If you'd rather create your own from scratch, visit
   `http://localhost/OMERO-GYM/admin/seed_admin.php` **once** instead; it refuses
   to run again after an admin exists — delete it afterwards if you want to be
   extra safe.
6. Visit `http://localhost/OMERO-GYM/login.html` in your browser (or `index.php`).
   This ONE form is used by everyone: register a new member account and log in
   for the member side, or log in with the admin email/password from step 5 and
   you're taken straight to `admin/dashboard.php` instead — no separate admin URL
   to remember.

## Roadmap (Phase 3 — completed)

- [x] PHP 8 backend with user registration, login and logout
- [x] MySQL database (`users`, `messages`, `trainers`, `sessions_catalog`, `admins`, `bookings`) via XAMPP
- [x] Passwords hashed with `password_hash()` and prepared statements for all queries
- [x] Messages / contact form saving to the `messages` table, linked in the main nav
- [x] Trainer picker on Book a Slot, with server-side availability re-checks
- [x] Admin control panel with its own fixed-email login, for workout plans, pricing, trainers and messages
- [x] Session-based route protection for classes / book / workouts / admin
