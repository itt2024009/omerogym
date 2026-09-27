<?php
// admin/trainers.php
// Lets the admin add trainers, edit their specialty, and mark them available or
// unavailable. This directly controls which trainers members can pick in the
// Book a Slot dropdown (see ../trainers.php and app.js).

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM trainers WHERE id = ?');
            $stmt->execute([$id]);
            $success = 'Trainer removed.';
        }
    } elseif ($formAction === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE trainers SET available = 1 - available WHERE id = ?');
            $stmt->execute([$id]);
            $success = 'Trainer availability updated.';
        }
    } else {
        $id        = (int) ($_POST['id'] ?? 0);
        $name      = clean($_POST['name'] ?? '');
        $specialty = clean($_POST['specialty'] ?? '');
        $available = isset($_POST['available']) ? 1 : 0;

        if ($name === '') {
            $error = 'Please enter the trainer\'s name.';
        } elseif ($formAction === 'update' && $id > 0) {
            $stmt = $pdo->prepare('UPDATE trainers SET name = ?, specialty = ?, available = ? WHERE id = ?');
            $stmt->execute([$name, $specialty, $available, $id]);
            $success = 'Trainer updated.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO trainers (name, specialty, available, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$name, $specialty, $available]);
            $success = 'Trainer added.';
        }
    }
}

$rows = $pdo->query('SELECT * FROM trainers ORDER BY available DESC, name')->fetchAll();

$editing = null;
if (isset($_GET['edit'])) {
    $editStmt = $pdo->prepare('SELECT * FROM trainers WHERE id = ? LIMIT 1');
    $editStmt->execute([(int) $_GET['edit']]);
    $editing = $editStmt->fetch() ?: null;
}

$activeTab = 'trainers';
$pageTitle = 'Trainers';
require __DIR__ . '/includes/admin_chrome_top.php';
?>
        <section class="fade">
          <span class="eyebrow">Admin Control Panel</span>
          <h1 class="page-title">TRAINERS</h1>
          <p class="page-sub">Add trainers and toggle availability. Only trainers marked available can be picked by members on Book a Slot.</p>
        </section>

        <?php if ($success): ?><p class="help" style="color:#3ddc84;margin-top:16px;"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php if ($error): ?><p class="help" style="color:#ff5c5c;margin-top:16px;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <div class="card pad" style="margin-top:20px;">
          <div class="panel-head" style="padding:0 0 14px;border:0;">
            <h2><?= $editing ? 'Edit Trainer' : 'Add a New Trainer' ?></h2>
          </div>
          <form method="post" action="trainers.php<?= $editing ? '?edit=' . (int) $editing['id'] : '' ?>">
            <input type="hidden" name="form_action" value="<?= $editing ? 'update' : 'add' ?>" />
            <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $editing['id'] ?>" /><?php endif; ?>
            <div class="admin-form-grid">
              <div>
                <label class="field-label" for="t-name">Trainer Name</label>
                <input class="field" id="t-name" name="name" required
                  value="<?= htmlspecialchars($editing['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
              </div>
              <div>
                <label class="field-label" for="t-spec">Specialty</label>
                <input class="field" id="t-spec" name="specialty" placeholder="e.g. Strength & Conditioning"
                  value="<?= htmlspecialchars($editing['specialty'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
              </div>
              <div class="span-2" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" id="t-avail" name="available" value="1" style="width:16px;height:16px;"
                  <?= (!isset($editing) || !empty($editing['available'])) ? 'checked' : '' ?> />
                <label class="field-label" for="t-avail" style="margin:0;">Available for booking</label>
              </div>
            </div>
            <div style="margin-top:16px;display:flex;gap:10px;">
              <button class="btn btn-primary" type="submit"><?= $editing ? 'Save Changes' : 'Add Trainer' ?></button>
              <?php if ($editing): ?><a class="btn btn-ghost" href="trainers.php">Cancel</a><?php endif; ?>
            </div>
          </form>
        </div>

        <div class="card panel" style="margin-top:20px;">
          <div class="panel-head"><h2>All Trainers</h2></div>
          <div class="pad table-wrap">
            <table>
              <thead><tr><th>Name</th><th>Specialty</th><th>Availability</th><th class="right">Actions</th></tr></thead>
              <tbody>
                <?php if (!$rows): ?>
                  <tr><td colspan="4" class="cell-muted">No trainers yet — add one above.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                  <tr>
                    <td class="cell-title"><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="cell-muted"><?= htmlspecialchars($row['specialty'] ?: '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                      <span class="badge <?= $row['available'] ? 'available' : 'unavailable' ?>">
                        <?= $row['available'] ? 'Available' : 'Unavailable' ?>
                      </span>
                    </td>
                    <td class="right">
                      <form method="post" action="trainers.php" style="display:inline">
                        <input type="hidden" name="form_action" value="toggle" />
                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>" />
                        <button class="btn btn-ghost btn-sm" type="submit"><?= $row['available'] ? 'Mark Unavailable' : 'Mark Available' ?></button>
                      </form>
                      <a class="btn btn-ghost btn-sm" href="trainers.php?edit=<?= (int) $row['id'] ?>">Edit</a>
                      <form method="post" action="trainers.php" style="display:inline" onsubmit="return confirm('Remove this trainer?');">
                        <input type="hidden" name="form_action" value="delete" />
                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>" />
                        <button class="btn btn-ghost btn-sm" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
<?php require __DIR__ . '/includes/admin_chrome_bottom.php'; ?>
