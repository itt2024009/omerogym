<?php
// auth/check.php
// Polled by app.js on protected pages (classes/book/workouts) to confirm the visitor
// is logged in before showing any member-only content, and to keep the "Hello, name"
// greeting in sync with the database instead of trusting the browser alone.

require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    json_out([
        'loggedIn' => true,
        'name'     => $_SESSION['user_name'],
        'email'    => $_SESSION['user_email'],
    ]);
}

json_out(['loggedIn' => false]);
