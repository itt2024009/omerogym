<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
check_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_service_id'];
    $date     = $_POST['appointment_date'];
    $time     = $_POST['appointment_time'];
    $user_id  = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, class_service_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $class_id, $date, $time]);

    header("Location: dashboard.php?booking=success");
    exit();
}

$classes = $pdo->query("SELECT * FROM class_services")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OMERO GYM - Book Slot</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/book.css">
</head>
<body>
    <h1>BOOK A TRAINING SLOT</h1>
    <form action="book.php" method="POST">
        <label>Select Session:</label>
        <select name="class_service_id" required>
            <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (LKR <?= $c['price'] ?>)</option>
            <?php endforeach; ?>
        </select><br>

        <label>Choose Date:</label>
        <input type="date" name="appointment_date" min="<?= date('Y-m-d') ?>" required><br>

        <label>Choose Time:</label>
        <input type="time" name="appointment_time" required><br>

        <button type="submit">CONFIRM BOOKING</button>
    </form>
</body>
</html>