<?php

/**
 * BOOTSTRAP SYSTEM
 * - Session
 * - Timezone
 * - Error reporting
 * - Load config
 * - Load helper
 * - Load database
 */

// 1. Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// 3. Error reporting (dev)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// 4. Load config
require_once __DIR__ . '/../config/config.php';

// 5. Load helper (CHỈ 1 dòng này)
require_once __DIR__ . '/helpers.php';

// 6. Load database
require_once __DIR__ . '/../config/database.php';