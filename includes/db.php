<?php
// includes/db.php
// Central database connection for OMERO GYM (Phase 3 - PHP & MySQL Integration)
// Uses PDO with prepared statements everywhere else in the app.

$DB_HOST = 'localhost';
$DB_NAME = 'omero_gym';
$DB_USER = 'root';
$DB_PASS = '';       // default XAMPP/WAMP root password is empty
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed. Make sure MySQL/XAMPP is running and the "omero_gym" database has been imported from database.sql.',
    ]);
    exit;
}
