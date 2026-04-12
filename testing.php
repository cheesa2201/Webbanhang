<?php
session_destroy();
header('Location: user/login.php');
exit;
require_once 'includes/bootstrap.php';
require_once 'includes/auth.php';

echo '<pre>';
print_r($_SESSION);
echo '</pre>';