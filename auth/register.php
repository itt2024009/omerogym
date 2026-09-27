<?php
// auth/register.php
// Called via fetch() from register.html (app.js). Creates a new member account.

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$input = read_json_input();

$name     = clean($input['name'] ?? '');
$email    = clean($input['email'] ?? '');
$phone    = clean($input['phone'] ?? '');
$password = (string) ($input['password'] ?? '');

if ($name === '' || $email === '' || $password === '') {
    json_out(['success' => false, 'message' => 'Name, email and password are required.'], 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_out(['success' => false, 'message' => 'Please enter a valid email address.'], 422);
}
if (strlen($password) < 6) {
    json_out(['success' => false, 'message' => 'Password must be at least 6 characters.'], 422);
}

// Check for an existing account with the same email (prepared statement).
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    json_out(['success' => false, 'message' => 'An account with that email already exists.'], 409);
}

$hashed = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare(
    'INSERT INTO users (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())'
);
$stmt->execute([$name, $email, $phone, $hashed]);
$userId = (int) $pdo->lastInsertId();

// Log the new member straight in and protect against session fixation.
session_regenerate_id(true);
$_SESSION['user_id']    = $userId;
$_SESSION['user_name']  = $name;
$_SESSION['user_email'] = $email;

json_out(['success' => true, 'name' => $name, 'email' => $email]);
