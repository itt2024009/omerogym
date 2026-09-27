<?php
// admin/seed_admin.php
// Run this ONCE in the browser (e.g. http://localhost/OMERO-GYM/admin/seed_admin.php)
// after importing database.sql. It lets you create the one fixed admin account with a
// real bcrypt-hashed password (matching how auth/register.php hashes member passwords).
// Once an admin account exists, this page refuses to create another one — delete this
// file afterwards, or at least don't leave it publicly reachable on a live server.

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$countStmt = $pdo->query('SELECT COUNT(*) AS c FROM admins');
$alreadySetUp = ((int) $countStmt->fetch()['c']) > 0;

$error = '';
$success = false;

if (!$alreadySetUp && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = clean($_POST['name'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill in the name, email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO admins (name, email, password, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$name, $email, $hashed]);
        $success = true;
        $alreadySetUp = true;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#0a0a0b" />
  <title>OMERO GYM — Admin Setup</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../common.css" />
  <link rel="stylesheet" href="../login.css" />
</head>
<body data-page="admin-setup">
  <div class="app">
    <div class="auth-main">
      <div class="auth-card fade">
        <div class="card auth-inner">
          <div class="brand-badge">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2l3 6 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"/>
            </svg>
          </div>
          <h1 class="auth-title">ADMIN SETUP</h1>

          <?php if ($alreadySetUp): ?>
            <?php if ($success): ?>
              <p class="help" style="color:#3ddc84;margin-bottom:16px;">Admin account created. You can log in now.</p>
              <a class="btn btn-primary btn-block" href="../login.html">Go to Login</a>
            <?php else: ?>
              <p class="auth-sub">An admin account already exists — this fixed account is set up. For security, delete <code>admin/seed_admin.php</code> from the server now.</p>
              <a class="btn btn-primary btn-block" href="../login.html">Go to Login</a>
            <?php endif; ?>
          <?php else: ?>
            <p class="auth-sub">One-time setup — create the single, fixed admin account for OMERO GYM. This page will refuse to run again once an admin exists.</p>

            <?php if ($error): ?>
              <p class="help" style="color:#ff5c5c;margin-bottom:16px;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <form class="stack" method="post" action="seed_admin.php">
              <div>
                <label class="field-label" for="sa-name">Admin Name</label>
                <input class="field" id="sa-name" name="name" required placeholder="Gym Owner" />
              </div>
              <div>
                <label class="field-label" for="sa-email">Admin Email (this becomes the fixed login)</label>
                <input class="field" id="sa-email" name="email" type="email" required placeholder="admin@omerogym.com" />
              </div>
              <div>
                <label class="field-label" for="sa-pass">Password</label>
                <input class="field" id="sa-pass" name="password" type="password" required minlength="6" placeholder="Minimum 6 characters" />
              </div>
              <button class="btn btn-primary btn-block" type="submit">Create Admin Account</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
