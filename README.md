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
- Class and session catalog with live category filtering
- Slot booking with a confirmation modal, now saved permanently in MySQL
- Personal dashboard listing booked slots, with cancel
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
| `login.html` | Login form (posts to `auth/login.php`) |
| `register.html` | Account creation form (posts to `auth/register.php`) |
| `classes.html` | Class catalog with category filter (member-only) |
| `book.html` | Booking form with confirmation (saves via `bookings/create.php`) |
| `workouts.html` | Member dashboard — list of bookings (`bookings/list.php`) |
| `contact.php` | New contact form, saved to the `messages` table |

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
│   ├── create.php        saves a new booking for the logged-in member
│   ├── list.php          returns the member's bookings
│   └── cancel.php        deletes a booking (only if it belongs to the member)
├── contact.php           contact form + handler → messages table
├── index.php             redirects to classes.html or login.html
├── dashboard.php         redirects to workouts.html (the member dashboard)
├── database.sql          creates the database + users/messages/bookings tables
├── app.js                original frontend logic, now calling the PHP endpoints above
└── *.html / *.css        original frontend, unchanged
```

## Database

Three tables, created by `database.sql`:

- **users** — id, name, email, phone, password (hashed), created_at
- **messages** — id, name, email, message, created_at (from the contact form)
- **bookings** — id, user_id, session_id, title, price, duration, booking_date, booking_time, status, created_at (the theme-specific table for this fitness/gym project)

## How to Run (Phase 3 — with backend)

1. Install **XAMPP** (or WAMP) and start **Apache** and **MySQL**.
2. Copy the whole `OMERO-GYM` folder into `htdocs` (XAMPP) or `www` (WAMP).
3. Open **phpMyAdmin** → **Import** → select `database.sql` → Go.
   This creates the `omero_gym` database with the `users`, `messages` and `bookings` tables.
4. Check `includes/db.php` — the default XAMPP/WAMP settings (`root` / no password) are
   already filled in. Change them there if your MySQL setup is different.
5. Visit `http://localhost/OMERO-GYM/login.html` in your browser (or `index.php`).
6. Register a new account, then log in — classes, booking and the workouts dashboard
   are all backed by MySQL from this point on.

## Roadmap (Phase 3 — completed)

- [x] PHP 8 backend with user registration, login and logout
- [x] MySQL database (`users`, `messages`, `bookings`) via XAMPP
- [x] Passwords hashed with `password_hash()` and prepared statements for all queries
- [x] Contact form saving to the `messages` table
- [x] Session-based route protection for classes / book / workouts
