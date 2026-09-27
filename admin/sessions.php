<?php
// admin/sessions.php
// Lets the admin add, edit and delete workout plans / classes / personal training
// packages, and control their category, description, price and duration. This is
// what powers the "Classes & Sessions" catalog and the "Book a Slot" dropdown on
// the member site (see ../sessions.php, which the member pages fetch from).

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$CATEGORIES = [
    'floor'    => 'General Gym Floor Access',
    'classes'  => 'Group Fitness Classes',
    'personal' => 'Personal Training (1-on-1)',
];

$error = '';
$success = '';

// --- Handle form submissions -------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM sessions_catalog WHERE id = ?');
            $stmt->execute([$id]);
            $success = 'Workout plan deleted.';
        }
    } else {
        // add or update, both share the same field set
        $id          = (int) ($_POST['id'] ?? 0);
        $sessionKey  = clean($_POST['session_key'] ?? '');
        $category    = clean($_POST['category'] ?? '');
        $title       = clean($_POST['title'] ?? '');
        $description = clean($_POST['description'] ?? '');
        $price       = (int) ($_POST['price'] ?? 0);
        $duration    = (int) ($_POST['duration'] ?? 0);

        if ($sessionKey === '' || $title === '' || !array_key_exists($category, $CATEGORIES)) {
            $error = 'Please fill in a session key, title and a valid category.';
        } elseif (!preg_match('/^[a-z0-9-]+$/', $sessionKey)) {
            $error = 'Session key can only contain lowercase letters, numbers and hyphens (e.g. "hiit-blast").';
        } else {
            if ($formAction === 'update' && $id > 0) {
                $stmt = $pdo->prepare(
                    'UPDATE sessions_catalog SET session_key = ?, category = ?, title = ?, description = ?, price = ?, duration = ? WHERE id = ?'
                );
                $stmt->execute([$sessionKey, $category, $title, $description, $price, $duration, $id]);
                $success = 'Workout plan updated.';
            } else {
                $dupe = $pdo->prepare('SELECT id FROM sessions_catalog WHERE session_key = ? LIMIT 1');
                $dupe->execute([$sessionKey]);
                if ($dupe->fetch()) {
                    $error = 'That session key is already used by another plan.';
                } else {
                    $stmt = $pdo->prepare(
                        'INSERT INTO sessions_catalog (session_key, category, title, description, price, duration, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())'
                    );
                    $stmt->execute([$sessionKey, $category, $title, $description, $price, $duration]);
                    $success = 'Workout plan added.';
                }
            }
        }
    }
}

// --- Load data for the page ---------------------------------------------------
$rows = $pdo->query('SELECT * FROM sessions_catalog ORDER BY category, price')->fetchAll();

$editing = null;
if (isset($_GET['edit'])) {
    $editStmt = $pdo->prepare('SELECT * FROM sessions_catalog WHERE id = ? LIMIT 1');
    $editStmt->execute([(int) $_GET['edit']]);
    $editing = $editStmt->fetch() ?: null;
}

$activeTab = 'sessions';
$pageTitle = 'Workout Plans & Pricing';
require __DIR__ . '/includes/admin_chrome_top.php';
?>
        <section class="fade">
          <span class="eyebrow">Admin Control Panel</span>
          <h1 class="page-title">WORKOUT PLANS &amp; PRICING</h1>
          <p class="page-sub">Add, edit and remove floor passes, group classes and personal training packages. Changes appear immediately on Classes &amp; Sessions and Book a Slot.</p>
        </section>

        <?php if ($success): ?><p class="help" style="color:#3ddc84;margin-top:16px;"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php if ($error): ?><p class="help" style="color:#ff5c5c;margin-top:16px;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <div class="card pad" style="margin-top:20px;">
          <div class="panel-head" style="padding:0 0 14px;border:0;">
            <h2><?= $editing ? 'Edit Workout Plan' : 'Add a New Workout Plan' ?></h2>
          </div>
          <form method="post" action="sessions.php<?= $editing ? '?edit=' . (int) $editing['id'] : '' ?>">
            <input type="hidden" name="form_action" value="<?= $editing ? 'update' : 'add' ?>" />
            <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $editing['id'] ?>" /><?php endif; ?>
            <div class="admin-form-grid">
              <div>
                <label class="field-label" for="s-key">Session Key (unique, e.g. "hiit-blast")</label>
                <input class="field" id="s-key" name="session_key" required pattern="[a-z0-9-]+"
                  value="<?= htmlspecialchars($editing['session_key'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
              </div>
              <div>
                <label class="field-label" for="s-cat">Category</label>
                <select class="field" id="s-cat" name="category" required>
                  <?php foreach ($CATEGORIES as $key => $label): ?>
                    <option value="<?= $key ?>" <?= (isset($editing['category']) && $editing['category'] === $key) ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="span-2">
                <label class="field-label" for="s-title">Title</label>
                <input class="field" id="s-title" name="title" required
                  value="<?= htmlspecialchars($editing['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
              </div>
              <div class="span-2">
                <label class="field-label" for="s-desc">Description</label>
                <textarea class="field" id="s-desc" name="description" rows="2"><?= htmlspecialchars($editing['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div>
                <label class="field-label" for="s-price">Price (LKR)</label>
                <input class="field" id="s-price" name="price" type="number" min="0" required
                  value="<?= htmlspecialchars((string) ($editing['price'] ?? 0), ENT_QUOTES, 'UTF-8') ?>" />
              </div>
              <div>
                <label class="field-label" for="s-dur">Duration (minutes)</label>
                <input class="field" id="s-dur" name="duration" type="number" min="0" required
                  value="<?= htmlspecialchars((string) ($editing['duration'] ?? 0), ENT_QUOTES, 'UTF-8') ?>" />
              </div>
            </div>
            <div style="margin-top:16px;display:flex;gap:10px;">
              <button class="btn btn-primary" type="submit"><?= $editing ? 'Save Changes' : 'Add Workout Plan' ?></button>
              <?php if ($editing): ?><a class="btn btn-ghost" href="sessions.php">Cancel</a><?php endif; ?>
            </div>
          </form>
        </div>

        <div class="card panel" style="margin-top:20px;">
          <div class="panel-head"><h2>All Workout Plans</h2></div>
          <div class="pad table-wrap">
            <table>
              <thead><tr><th>Key</th><th>Category</th><th>Title</th><th>Price</th><th>Duration</th><th class="right">Actions</th></tr></thead>
              <tbody>
                <?php if (!$rows): ?>
                  <tr><td colspan="6" class="cell-muted">No workout plans yet — add one above.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                  <tr>
                    <td class="id"><?= htmlspecialchars($row['session_key'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="cell-muted"><?= htmlspecialchars($CATEGORIES[$row['category']] ?? $row['category'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="cell-title"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="cell-muted">LKR <?= number_format((int) $row['price']) ?></td>
                    <td class="cell-muted"><?= (int) $row['duration'] ?> mins</td>
                    <td class="right">
                      <a class="btn btn-ghost btn-sm" href="sessions.php?edit=<?= (int) $row['id'] ?>">Edit</a>
                      <form method="post" action="sessions.php" style="display:inline" onsubmit="return confirm('Delete this workout plan?');">
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
