<?php
declare(strict_types=1);
define('DB_PORT', 3306); // nếu MySQL của bạn chạy 3307 thì đổi thành 3307

function db_connect(): PDO {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function db_close(?mysqli $conn): void
{
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}