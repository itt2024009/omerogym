<?php
// bookings/cancel.php
// Called from workouts.html when the member cancels an upcoming slot.

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$input = read_json_input();
$dbId  = (int) ($input['dbId'] ?? 0);

if ($dbId <= 0) {
    json_out(['success' => false, 'message' => 'Missing booking id.'], 422);
}

// Only ever delete a booking that belongs to the logged-in member.
$stmt = $pdo->prepare('DELETE FROM bookings WHERE id = ? AND user_id = ?');
$stmt->execute([$dbId, $_SESSION['user_id']]);

json_out(['success' => true]);
