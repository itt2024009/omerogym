<?php
// admin/includes/admin_chrome_top.php
// Shared header/nav for every admin page. Include this AFTER require_admin() and
// after setting $activeTab to one of: sessions | trainers | messages.
$activeTab = $activeTab ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#0a0a0b" />
  <title>OMERO GYM — Admin <?= isset($pageTitle) ? '· ' . htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') : '' ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../common.css" />
  <link rel="stylesheet" href="../workouts.css" />
  <link rel="stylesheet" href="../login.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body data-page="admin">
  <div class="app">
    <header>
      <nav class="nav">
        <a class="logo" href="dashboard.php">
          <span class="logo-badge">
            <svg width="18" height="18" viewBox="0 0 512 512" fill="#fff">
              <rect x="176" y="236" width="160" height="40" rx="20"/>
              <rect x="140" y="196" width="44" height="120" rx="16"/>
              <rect x="328" y="196" width="44" height="120" rx="16"/>
              <rect x="96" y="216" width="40" height="80" rx="14"/>
              <rect x="376" y="216" width="40" height="80" rx="14"/>
            </svg>
          </span>
          <span class="logo-text">OMERO <span class="r">GYM</span> <span style="color:rgba(255,255,255,.4);font-size:12px;">ADMIN</span></span>
        </a>
        <ul class="nav-links">
          <li><a class="nav-link <?= $activeTab === 'sessions' ? 'active' : '' ?>" href="sessions.php">Workout Plans &amp; Pricing</a></li>
          <li><a class="nav-link <?= $activeTab === 'trainers' ? 'active' : '' ?>" href="trainers.php">Trainers</a></li>
          <li><a class="nav-link <?= $activeTab === 'messages' ? 'active' : '' ?>" href="messages.php">Messages</a></li>
        </ul>
        <div class="nav-right">
          <span class="hello">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
            </svg>
            Hello, <?= htmlspecialchars(admin_name(), ENT_QUOTES, 'UTF-8') ?>
          </span>
          <a class="btn btn-ghost btn-sm" href="logout.php">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>
            </svg>
            Logout
          </a>
        </div>
        <button class="nav-toggle" id="navToggle" aria-label="Menu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </nav>
      <div class="mobile-menu" id="mobileMenu">
        <a class="<?= $activeTab === 'sessions' ? 'active' : '' ?>" href="sessions.php">Workout Plans &amp; Pricing</a>
        <a class="<?= $activeTab === 'trainers' ? 'active' : '' ?>" href="trainers.php">Trainers</a>
        <a class="<?= $activeTab === 'messages' ? 'active' : '' ?>" href="messages.php">Messages</a>
        <a href="logout.php">Logout</a>
      </div>
    </header>

    <main>
      <div class="shell">
