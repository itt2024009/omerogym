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
$trainerId = (int) ($input['trainerId'] ?? 0);

if ($sessionId === '' || $title === '' || $date === '' || $time === '') {
    json_out(['success' => false, 'message' => 'Please choose a session, date and time before confirming.'], 422);
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    json_out(['success' => false, 'message' => 'Invalid date.'], 422);
}

// A trainer is optional (e.g. plain floor-access passes), but if one was picked it
// must exist AND still be marked available — re-checked here server-side so a member
// can never lock in a trainer who was unavailable, even if the dropdown was tampered with.
$trainerName = null;
if ($trainerId > 0) {
    $tStmt = $pdo->prepare('SELECT id, name, available FROM trainers WHERE id = ? LIMIT 1');
    $tStmt->execute([$trainerId]);
    $trainer = $tStmt->fetch();
    if (!$trainer) {
        json_out(['success' => false, 'message' => 'Selected trainer no longer exists.'], 422);
    }
    if (!$trainer['available']) {
        json_out(['success' => false, 'message' => 'That trainer is not available. Please pick another trainer.'], 422);
    }
    $trainerName = $trainer['name'];
} else {
    $trainerId = null;
}

$stmt = $pdo->prepare(
    'INSERT INTO bookings (user_id, session_id, title, price, duration, trainer_id, trainer_name, booking_date, booking_time, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "Confirmed", NOW())'
);
$stmt->execute([$_SESSION['user_id'], $sessionId, $title, $price, $duration, $trainerId, $trainerName, $date, $time]);

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
        'trainer'  => $trainerName,
        'date'     => $date,
        'time'     => $time,
    ],
]);
