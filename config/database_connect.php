<?php
declare(strict_types=1);

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'web_ban_hang');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', 3307); // nếu MySQL của bạn chạy 3307 thì đổi thành 3307

function db_connect(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        $conn->set_charset('utf8mb4');
        return $conn;
    } catch (mysqli_sql_exception $e) {
        die('Loi ket noi database: ' . $e->getMessage());
    }
}

function db_close(?mysqli $conn): void
{
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}
