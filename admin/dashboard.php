<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$sessionCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM sessions_catalog')->fetch()['c'];
$trainerCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM trainers')->fetch()['c'];
$availableTrainerCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM trainers WHERE available = 1')->fetch()['c'];
$messageCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM messages')->fetch()['c'];
$memberCount  = (int) $pdo->query('SELECT COUNT(*) AS c FROM users')->fetch()['c'];
$bookingCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM bookings')->fetch()['c'];

$activeTab = '';
$pageTitle = 'Dashboard';
require __DIR__ . '/includes/admin_chrome_top.php';
?>
        <section class="fade">
          <span class="eyebrow">Admin Control Panel</span>
          <h1 class="page-title">OVERVIEW</h1>
          <p class="page-sub">Everything the admin controls for OMERO GYM — workout plans, pricing, trainers and member feedback.</p>
        </section>

        <div class="admin-form-grid" style="margin-top:28px;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
          <div class="card pad">
            <p class="sum-label">Workout Plans</p>
            <p class="sum-title"><?= $sessionCount ?></p>
          </div>
          <div class="card pad">
            <p class="sum-label">Trainers</p>
            <p class="sum-title"><?= $trainerCount ?> <span style="font-size:13px;color:rgba(255,255,255,.4);">(<?= $availableTrainerCount ?> available)</span></p>
          </div>
          <div class="card pad">
            <p class="sum-label">Feedback Messages</p>
            <p class="sum-title"><?= $messageCount ?></p>
          </div>
          <div class="card pad">
            <p class="sum-label">Members</p>
            <p class="sum-title"><?= $memberCount ?></p>
          </div>
          <div class="card pad">
            <p class="sum-label">Total Bookings</p>
            <p class="sum-title"><?= $bookingCount ?></p>
          </div>
        </div>

        <div class="admin-form-grid" style="margin-top:20px;">
          <a class="card pad" href="sessions.php" style="display:block;">
            <div class="panel-head" style="padding:0 0 10px;border:0;"><h2>Workout Plans &amp; Pricing</h2></div>
            <p class="page-sub" style="margin:0;">Add, edit or remove floor passes, group classes and personal training packages, and set their price and duration.</p>
          </a>
          <a class="card pad" href="trainers.php" style="display:block;">
            <div class="panel-head" style="padding:0 0 10px;border:0;"><h2>Trainers</h2></div>
            <p class="page-sub" style="margin:0;">Add trainers and mark them available or unavailable — members only see available trainers as pickable in Book a Slot.</p>
          </a>
          <a class="card pad" href="messages.php" style="display:block;">
            <div class="panel-head" style="padding:0 0 10px;border:0;"><h2>Messages</h2></div>
            <p class="page-sub" style="margin:0;">Read feedback and questions members sent through the Messages / Contact form.</p>
          </a>
        </div>
<?php require __DIR__ . '/includes/admin_chrome_bottom.php'; ?>
