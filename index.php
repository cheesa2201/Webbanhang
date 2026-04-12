<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (is_post()) {
    $name = post('name');
    dd($name);
}

echo format_price(1500000);
?>

<br><a href="user/logout.php">Logout</a>