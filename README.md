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
- Slot booking with a confirmation modal (saved in the browser for now)
- Personal dashboard listing booked slots, with cancel
- Login, registration and contact forms with real-time validation

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Structure | HTML5, CSS3, Bootstrap 5 |
| Styling | Custom CSS on top of Bootstrap (dark theme) |
| Logic | JavaScript (Vanilla) |
| Backend | PHP 8 *(added in Phase 3)* |
| Database | MySQL *(added in Phase 3)* |
| Version control | Git & GitHub |

## Pages

| File | Description |

| `login.html` | Login form |
| `register.html` | Account creation form |
| `classes.html` | Class catalog with category filter |
| `book.html` | Booking form with confirmation modal |
| `workout dashboard.html` | List of the member's bookings |


## JavaScript Features

1. Dynamic content – filter class cards by category without reloading.
2. Image slider – Bootstrap carousel on the home page.
3. Form validation – required fields, email format, matching passwords and live feedback.
4. Smooth scrolling – navigation links scroll smoothly to page sections.
5. Custom animation – sections fade in as you scroll.
6. Event handling – navbar shadow on scroll, booking and cancel actions.


## How to Run

This phase is front-end only, so no server is required.

1. Clone the repository:
   ```
   https://github.com/itt2024009/omerogym.git
   ```
2. Open the folder in your editor.
3. Open `login.html` in a browser, or use the **Live Server** extension in VS Code
   (recommended, so booking data is kept between pages).

Bootstrap 5 and the icons/fonts load from a CDN, so an internet connection is needed
the first time.

## Roadmap (Phase 3)

- PHP 8 backend with user registration, login and logout
- MySQL database (`users`, `messages`, `bookings`) via XAMPP
- Passwords hashed with `password_hash()` and prepared statements for all queries
