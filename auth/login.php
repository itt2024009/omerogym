<?php
// auth/login.php
// Called via fetch() from login.html (app.js). Validates credentials and starts the session.

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$input = read_json_input();

$email    = clean($input['email'] ?? '');
$password = (string) ($input['password'] ?? '');

if ($email === '' || $password === '') {
    json_out(['success' => false, 'message' => 'Email and password are required.'], 422);
}

$stmt = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Member login.
    session_regenerate_id(true);
    $_SESSION['user_id']    = (int) $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];

    json_out(['success' => true, 'name' => $user['name'], 'email' => $user['email'], 'admin' => false]);
}

// Not a member account (or wrong password) — try the admins table so the SAME
// login form works for the gym owner too, with no separate admin URL to remember.
// A member and an admin are still different session keys/tables under the hood,
// this just lets one form check both.
$adminStmt = $pdo->prepare('SELECT id, name, email, password FROM admins WHERE email = ? LIMIT 1');
$adminStmt->execute([$email]);
$admin = $adminStmt->fetch();

if ($admin && password_verify($password, $admin['password'])) {
    session_regenerate_id(true);
    $_SESSION['admin_id']    = (int) $admin['id'];
    $_SESSION['admin_name']  = $admin['name'];
    $_SESSION['admin_email'] = $admin['email'];

    json_out(['success' => true, 'name' => $admin['name'], 'email' => $admin['email'], 'admin' => true]);
}

json_out(['success' => false, 'message' => 'Incorrect email or password.'], 401);
