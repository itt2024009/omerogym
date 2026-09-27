<?php
// admin/includes/admin_auth.php
// Small helpers for the admin site's own login session. Kept completely separate
// from the member "user_id" session used by auth/*.php — an admin and a member are
// different accounts (different tables), so they get different session keys.

require_once __DIR__ . '/../../includes/functions.php'; // also calls session_start()

function admin_logged_in()
{
    return isset($_SESSION['admin_id']);
}

/** Redirect to the admin login page if nobody is logged in as admin. Call at the
 *  top of every admin page except login.php itself. */
function require_admin()
{
    if (!admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function admin_name()
{
    return $_SESSION['admin_name'] ?? 'Admin';
}
