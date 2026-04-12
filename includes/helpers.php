<?php

if (!function_exists('redirect')) {
    function redirect($path = '')
    {
        $baseUrl = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        $path = ltrim($path, '/');
        $url = $path ? $baseUrl . '/' . $path : $baseUrl;

        header("Location: $url");
        exit;
    }
}

if (!function_exists('is_post')) {
    function is_post()
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }
}

if (!function_exists('is_get')) {
    function is_get()
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET';
    }
}

if (!function_exists('sanitize')) {
    function sanitize($data)
    {
        if (is_array($data)) {
            return array_map('sanitize', $data);
        }

        return htmlspecialchars(trim(stripslashes($data)), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('post')) {
    function post($key, $default = null)
    {
        return isset($_POST[$key]) ? sanitize($_POST[$key]) : $default;
    }
}

if (!function_exists('get')) {
    function get($key, $default = null)
    {
        return isset($_GET[$key]) ? sanitize($_GET[$key]) : $default;
    }
}

if (!function_exists('format_price')) {
    function format_price($price)
    {
        return number_format((float)$price, 0, ',', '.') . ' đ';
    }
}

if (!function_exists('dd')) {
    function dd($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die();
    }
}

if (!function_exists('require_login')) {
    function require_login()
    {
        if (!isset($_SESSION['id_nguoi_dung'])) {
            redirect('user/login.php');
        }
    }
}