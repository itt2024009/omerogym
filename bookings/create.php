<?php
// bookings/create.php
// Called from book.html when the member presses "Confirm Booking".

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$input = read_json_input();

$sessionId = clean($input['sessionId'] ?? '');
$title     = clean($input['title'] ?? '');
$price     = (int) ($input['price'] ?? 0);
$duration  = (int) ($input['duration'] ?? 0);
$date      = clean($input['date'] ?? '');
$time      = clean($input['time'] ?? '');

if ($sessionId === '' || $title === '' || $date === '' || $time === '') {
    json_out(['success' => false, 'message' => 'Please choose a session, date and time before confirming.'], 422);
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    json_out(['success' => false, 'message' => 'Invalid date.'], 422);
}

$stmt = $pdo->prepare(
    'INSERT INTO bookings (user_id, session_id, title, price, duration, booking_date, booking_time, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, "Confirmed", NOW())'
);
$stmt->execute([$_SESSION['user_id'], $sessionId, $title, $price, $duration, $date, $time]);

$bookingId = (int) $pdo->lastInsertId();

json_out([
    'success' => true,
    'booking' => [
        'id'       => make_reservation_code($bookingId),
        'status'   => 'Confirmed',
        'sessionId'=> $sessionId,
        'title'    => $title,
        'price'    => $price,
        'duration' => $duration,
        'date'     => $date,
        'time'     => $time,
    ],
]);
