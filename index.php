<?php
// index.php
// Entry point required by the Phase 3 folder structure. Sends visitors to the right
// page depending on whether they already have a session.

require_once __DIR__ . '/includes/functions.php';

header('Location: ' . (is_logged_in() ? 'classes.html' : 'login.html'));
exit;
