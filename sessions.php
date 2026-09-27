<?php
// sessions.php
// Public read-only endpoint. Returns the workout-plan / pricing catalog from the
// sessions_catalog table (managed by the admin at admin/sessions.php), so the
// Classes & Sessions page and the Book a Slot page always show whatever the admin
// has configured instead of a hardcoded list in app.js.

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$stmt = $pdo->query('SELECT session_key, category, title, description, price, duration FROM sessions_catalog ORDER BY category, price');
$rows = $stmt->fetchAll();

$sessions = array_map(function ($row) {
    return [
        'id'          => $row['session_key'],
        'category'    => $row['category'],
        'title'       => $row['title'],
        'description' => $row['description'],
        'price'       => (int) $row['price'],
        'duration'    => (int) $row['duration'],
    ];
}, $rows);

json_out(['success' => true, 'sessions' => $sessions]);
