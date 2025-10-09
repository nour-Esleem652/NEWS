<?php
// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'news');

// إعدادات الأمان
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// بدء الجلسة بشكل آمن
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => false, // غيّرها إلى true في الإنتاج مع HTTPS
        'cookie_samesite' => 'Strict'
    ]);
}

// دالة الاتصال بقاعدة البيانات
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("خطأ في الاتصال بقاعدة البيانات");
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// دالة التحقق من تسجيل الدخول
function checkAuth() {
    if (!isset($_SESSION["authUser"])) {
        header("Location: login_ui.php");
        exit;
    }
}

// دالة توليد CSRF Token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// دالة التحقق من CSRF Token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// دالة تنظيف المدخلات
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>