<?php

// ============================================================
// DATABASE CONNECTION
// ============================================================
// This file connects to MySQL so the app can save and read data.
//
// How it works:
// 1. Load database settings from config.php
// 2. Call mysqli_connect() to open a connection to MySQL
// 3. If it fails, show an error message and stop
// 4. Set the connection to use UTF-8 (supports all languages)
// 5. Define a simple function db() that returns the connection
//    so other files can use it
// ============================================================

$config = require __DIR__ . '/config.php';
$db = $config['db'];

$conn = mysqli_connect(
    $db['host'],     // where MySQL lives (e.g. 127.0.0.1)
    $db['username'], // username (e.g. root)
    $db['password'], // password (e.g. empty)
    $db['database'], // database name (e.g. customer_feedback_system)
    (int) $db['port'] // port number (e.g. 3307)
);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, $db['charset']);

// Shortcut function — call db() anywhere to get the connection
function db(): mysqli
{
    global $conn;
    return $conn;
}
