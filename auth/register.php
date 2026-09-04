<?php
require_once '../includes/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Secure BCRYPT hashing as required
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$username, $email, $phone, $hashed_password]);
            header("Location: login.php?registered=success");
            exit();
        } catch (PDOException $e) {
            $message = "Registration failed: Email might already exist.";
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OMERO GYM - Register</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
    <div class="auth-card">
        <h2>CREATE ACCOUNT</h2>
        <?php if ($message): ?><p class="error"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">REGISTER NOW</button>
        </form>
        <a href="login.php">Already registered? Login Here</a>
    </div>
</body>
</html>