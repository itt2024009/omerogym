<?php
// admin/login.php
// The gym now has ONE login page for everyone: ../login.html. That form (via
// auth/login.php) checks the admins table too, so an admin signs in exactly the
// same way a member does — there's no separate admin URL to remember or share.
// This file is kept only so an old bookmark/link doesn't 404; it just forwards
// straight to the shared login page (or to the dashboard if already signed in).

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/admin_auth.php';

if (admin_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

header('Location: ../login.html');
exit;
