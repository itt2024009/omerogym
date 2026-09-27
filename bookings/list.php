<?php
// bookings/list.php
// Called from workouts.html to populate the "My Workouts" dashboard.

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$stmt = $pdo->prepare(
    'SELECT id, session_id, title, price, duration, trainer_name, booking_date, booking_time, status
     FROM bookings WHERE user_id = ? ORDER BY booking_date DESC, booking_time DESC'
);
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();

$bookings = array_map(function ($row) {
    return [
        'id'        => make_reservation_code($row['id']),
        'dbId'      => (int) $row['id'],
        'sessionId' => $row['session_id'],
        'title'     => $row['title'],
        'price'     => (int) $row['price'],
        'duration'  => (int) $row['duration'],
        'trainer'   => $row['trainer_name'],
        'date'      => $row['booking_date'],
        'time'      => $row['booking_time'],
        'status'    => $row['status'],
    ];
}, $rows);

json_out(['success' => true, 'bookings' => $bookings]);
