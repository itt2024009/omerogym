<?php
// dashboard.php
// Kept for the folder structure required in the Phase 3 guideline. In OMERO GYM the
// member dashboard is workouts.html ("My Workouts"), so this simply forwards there.

require_once __DIR__ . '/includes/functions.php';

header('Location: ' . (is_logged_in() ? 'workouts.html' : 'login.html'));
exit;
