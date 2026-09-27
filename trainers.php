<?php
// trainers.php
// Public read-only endpoint. Returns every trainer with their current availability,
// so the Book a Slot page can let a member pick a trainer for the class/package they
// selected, and grey out / disable trainers who are marked unavailable by the admin.

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$stmt = $pdo->query('SELECT id, name, specialty, available FROM trainers ORDER BY available DESC, name');
$rows = $stmt->fetchAll();

$trainers = array_map(function ($row) {
    return [
        'id'        => (int) $row['id'],
        'name'      => $row['name'],
        'specialty' => $row['specialty'],
        'available' => (bool) $row['available'],
    ];
}, $rows);

json_out(['success' => true, 'trainers' => $trainers]);
