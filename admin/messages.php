<?php
// admin/messages.php
// Shows the feedback/questions members send through the Messages page (contact.php),
// which are stored in the "messages" table.

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM messages WHERE id = ?');
        $stmt->execute([$id]);
        $success = 'Message deleted.';
    }
}

$rows = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();

$activeTab = 'messages';
$pageTitle = 'Messages';
require __DIR__ . '/includes/admin_chrome_top.php';
?>
        <section class="fade">
          <span class="eyebrow">Admin Control Panel</span>
          <h1 class="page-title">MESSAGES</h1>
          <p class="page-sub">Feedback and questions members have sent through the Messages page.</p>
        </section>

        <?php if ($success): ?><p class="help" style="color:#3ddc84;margin-top:16px;"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <div class="card panel" style="margin-top:20px;">
          <div class="panel-head"><h2><?= count($rows) ?> Message<?= count($rows) === 1 ? '' : 's' ?></h2></div>
          <div class="pad stack-tight">
            <?php if (!$rows): ?>
              <div class="empty"><p>No messages yet.</p></div>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
              <div class="card pad" style="background:rgba(255,255,255,.02);">
                <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
                  <div>
                    <p class="cell-title"><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>
                      <span class="cell-muted" style="font-weight:400;">&lt;<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>&gt;</span></p>
                    <p class="cell-muted" style="font-size:12px;margin-top:2px;"><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
                  </div>
                  <form method="post" action="messages.php" onsubmit="return confirm('Delete this message?');">
                    <input type="hidden" name="form_action" value="delete" />
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>" />
                    <button class="btn btn-ghost btn-sm" type="submit">Delete</button>
                  </form>
                </div>
                <p class="msg-body" style="margin-top:10px;"><?= nl2br(htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8')) ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
<?php require __DIR__ . '/includes/admin_chrome_bottom.php'; ?>
