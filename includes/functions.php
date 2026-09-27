<?php
// includes/functions.php
// Small shared helpers used across the backend.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Send a JSON response and stop execution. */
function json_out($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/** Read the JSON body of a request (used by the fetch() calls in app.js). */
function read_json_input()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/** Basic string cleanup before validation / storage. */
function clean($value)
{
    return trim(htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'));
}

/** True if a member is currently logged in. */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/** Stop AJAX requests from unauthenticated users. Call at the top of protected endpoints. */
function require_login()
{
    if (!is_logged_in()) {
        json_out(['success' => false, 'message' => 'Please log in to continue.'], 401);
    }
}

/** Generate a short human-friendly reservation code, e.g. OM-4821. */
function make_reservation_code($id)
{
    return 'OM-' . str_pad((string) ((int) $id + 4000), 4, '0', STR_PAD_LEFT);
}
