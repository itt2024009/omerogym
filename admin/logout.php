<?php
// admin/logout.php
require_once __DIR__ . '/includes/admin_auth.php';

unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email']);

// One shared login page for everyone now, so send the admin back there too.
header('Location: ../login.html');
exit;
