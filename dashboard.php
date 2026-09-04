<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
check_login();

$user_id = $_SESSION['user_id'];

// Handle Cancellation
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    $stmt = $pdo->prepare("UPDATE reservations SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
    $stmt->execute([$cancel_id, $user_id]);
    header("Location: dashboard.php");
    exit();
}

// Fetch user reservations
$stmt = $pdo->prepare("
    SELECT r.id, c.name AS class_name, r.appointment_date, r.appointment_time, c.price, r.status 
    FROM reservations r
    JOIN class_services c ON r.class_service_id = c.id
    WHERE r.user_id = ?
    ORDER BY r.appointment_date DESC
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OMERO GYM - My Workouts</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/workouts.css">
</head>
<body>
    <h1>MY WORKOUT DASHBOARD</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="auth/logout.php">Logout</a></p>

    <h2>UPCOMING SCHEDULED SLOTS</h2>
    <table border="1">
        <tr>
            <th>Class/Session</th>
            <th>Scheduled Date & Time</th>
            <th>Cost</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($reservations as $res): ?>
        <tr>
            <td><?= htmlspecialchars($res['class_name']) ?></td>
            <td><?= $res['appointment_date'] ?> - <?= $res['appointment_time'] ?></td>
            <td>LKR <?= $res['price'] ?></td>
            <td><?= $res['status'] ?></td>
            <td>
                <?php if ($res['status'] === 'Confirmed'): ?>
                    <a href="dashboard.php?cancel_id=<?= $res['id'] ?>" onclick="return confirm('Cancel slot?')">Cancel</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>