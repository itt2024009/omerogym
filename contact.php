<?php
// contact.php
// New page (the original frontend had no contact form). Built to match the existing
// dark OMERO GYM look using the same common.css / login.css, so it fits in visually
// without touching any of the original files.

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// contact.php doubles as two things:
//  - the "Messages" tab for a logged-in member (same nav/session as classes.html,
//    book.html, workouts.html — it must NOT look like the member got logged out)
//  - a public "Contact Us" form for a signed-out visitor
// Which header/copy is shown below depends on is_logged_in(), not on which link
// was clicked, so a logged-in member always keeps their Hello/Logout member nav.
$loggedIn  = is_logged_in();
$errors    = [];
$success   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = clean($_POST['name'] ?? '');
    $email   = clean($_POST['email'] ?? '');
    $message = clean($_POST['message'] ?? '');

    if ($name === '')    { $errors[] = 'Please enter your name.'; }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Please enter a valid email address.'; }
    if ($message === '') { $errors[] = 'Please enter a message.'; }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$name, $email, $message]);
        $success = true;
        $name = $email = $message = '';
    }
} elseif ($loggedIn) {
    // Pre-fill name/email for a logged-in member so they don't have to retype them.
    $name  = $_SESSION['user_name'];
    $email = $_SESSION['user_email'];
    $message = '';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#0a0a0b" />
  <title>OMERO GYM — Contact Us</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="common.css" />
  <link rel="stylesheet" href="login.css" />
</head>
<body data-page="contact">
  <div class="app">
    <?php if ($loggedIn): ?>
    <!-- Logged-in member: same navbar as classes.html / book.html / workouts.html,
         so switching to Messages behaves exactly like switching any other tab
         (Hello/Logout stay put, nobody gets bounced to a signed-out header). -->
    <header>
      <nav class="nav">
        <a class="logo" href="classes.html">
          <span class="logo-badge">
            <svg width="18" height="18" viewBox="0 0 512 512" fill="#fff">
              <rect x="176" y="236" width="160" height="40" rx="20"/>
              <rect x="140" y="196" width="44" height="120" rx="16"/>
              <rect x="328" y="196" width="44" height="120" rx="16"/>
              <rect x="96" y="216" width="40" height="80" rx="14"/>
              <rect x="376" y="216" width="40" height="80" rx="14"/>
            </svg>
          </span>
          <span class="logo-text">OMERO <span class="r">GYM</span></span>
        </a>
        <ul class="nav-links">
          <li><a class="nav-link active" href="contact.php">Messages</a></li>
          <li><a class="nav-link" href="classes.html">Classes &amp; Sessions</a></li>
          <li><a class="nav-link" href="book.html">Book a Slot</a></li>
          <li><a class="nav-link" href="workouts.html">My Workouts</a></li>
        </ul>
        <div class="nav-right">
          <span class="hello">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
            </svg>
            Hello, <span class="js-hello"><?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></span>
          </span>
          <button class="btn btn-ghost btn-sm" data-action="logout">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>
            </svg>
            Logout
          </button>
        </div>
        <button class="nav-toggle" id="navToggle" aria-label="Menu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </nav>
      <div class="mobile-menu" id="mobileMenu">
        <span class="hello" style="padding:8px 12px">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
          </svg>
          Hello, <span class="js-hello"><?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></span>
        </span>
        <a class="active" href="contact.php">Messages</a>
        <a href="classes.html">Classes &amp; Sessions</a>
        <a href="book.html">Book a Slot</a>
        <a href="workouts.html">My Workouts</a>
        <button class="btn btn-ghost" data-action="logout">Logout</button>
      </div>
    </header>
    <?php else: ?>
    <header>
      <nav class="nav">
        <a class="logo" href="login.html">
          <span class="logo-badge">
            <svg width="18" height="18" viewBox="0 0 512 512" fill="#fff">
              <rect x="176" y="236" width="160" height="40" rx="20"/>
              <rect x="140" y="196" width="44" height="120" rx="16"/>
              <rect x="328" y="196" width="44" height="120" rx="16"/>
              <rect x="96" y="216" width="40" height="80" rx="14"/>
              <rect x="376" y="216" width="40" height="80" rx="14"/>
            </svg>
          </span>
          <span class="logo-text">OMERO <span class="r">GYM</span></span>
        </a>
        <span></span>
        <div class="nav-right">
          <a class="nav-link" href="login.html">Login</a>
          <a class="btn btn-primary btn-sm" href="register.html">Create Account</a>
        </div>
      </nav>
    </header>
    <?php endif; ?>

    <div class="auth-main">
      <div class="auth-card fade">
        <div class="card auth-inner">
          <div class="brand-badge">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <h1 class="auth-title"><?= $loggedIn ? 'MESSAGES' : 'CONTACT US' ?></h1>
          <p class="auth-sub"><?= $loggedIn ? 'Send feedback or a question to the OMERO GYM team — we read every message.' : 'Questions about membership or classes? Send us a message.' ?></p>

          <?php if ($success): ?>
            <p class="help" style="color:#3ddc84;margin-bottom:16px;">Thank you — your message has been sent. We'll get back to you soon.</p>
          <?php endif; ?>

          <?php if ($errors): ?>
            <p class="help" style="color:#ff5c5c;margin-bottom:16px;"><?= htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8') ?></p>
          <?php endif; ?>

          <form id="contactForm" class="stack" method="post" action="contact.php" novalidate>
            <div>
              <label class="field-label" for="ct-name">Full Name</label>
              <input class="field" id="ct-name" name="name" required placeholder="John Doe" value="<?= htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8') ?>" />
            </div>
            <div>
              <label class="field-label" for="ct-email">Email Address</label>
              <input class="field" id="ct-email" name="email" type="email" required placeholder="john@example.com" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" />
            </div>
            <div>
              <label class="field-label" for="ct-message">Message</label>
              <textarea class="field" id="ct-message" name="message" rows="4" required placeholder="How can we help?"><?= htmlspecialchars($message ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Send Message</button>
          </form>

          <?php if ($loggedIn): ?>
            <p class="center-link">Back to <a href="classes.html">Classes &amp; Sessions</a></p>
          <?php else: ?>
            <p class="center-link">Back to <a href="login.html">Login</a></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Lightweight client-side validation, mirroring the style used on the other
    // OMERO GYM forms — server-side validation in contact.php still runs regardless.
    document.getElementById('contactForm').addEventListener('submit', function (e) {
      var name = document.getElementById('ct-name').value.trim();
      var email = document.getElementById('ct-email').value.trim();
      var message = document.getElementById('ct-message').value.trim();
      var emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      if (!name || !emailOk || !message) {
        e.preventDefault();
        alert('Please fill in your name, a valid email, and a message.');
      }
    });
  </script>
  <script src="app.js"></script>
</body>
</html>
