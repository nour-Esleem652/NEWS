<!--  
// define('DB_HOST', 'db');   // اسم service في docker-compose
// define('DB_USER', 'root');
// define('DB_PASS', 'root');
// define('DB_NAME', 'news');


// define('UPLOAD_DIR', 'uploads/');
// define('MAX_FILE_SIZE', 5242880); 
// define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// if (session_status() === PHP_SESSION_NONE) {
//     session_start([
//         'cookie_httponly' => true,
//         'cookie_secure' => false, 
//         'cookie_samesite' => 'Strict'
//     ]);
// }

// function getDBConnection() {
//     $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
//     if ($conn->connect_error) {
//         error_log("Database connection failed: " . $conn->connect_error);
//         die("خطأ في الاتصال بقاعدة البيانات");
//     }
    
//     $conn->set_charset("utf8mb4");
//     return $conn;
// }

// function checkAuth() {
//     if (!isset($_SESSION["authUser"])) {
//         header("Location: login_ui.php");
//         exit;
//     }
// }

// function generateCSRFToken() {
//     if (empty($_SESSION['csrf_token'])) {
//         $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
//     }
//     return $_SESSION['csrf_token'];
// }

// function verifyCSRFToken($token) {
//     return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
// }

// function sanitizeInput($data) {
//     return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
// }
// 

<?php
// ✅ Database (works locally + on Back4App via ENV)
define('DB_HOST', getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: 'localhost'));
define('DB_USER', getenv('DB_USER') ?: (getenv('MYSQLUSER') ?: 'root'));
define('DB_PASS', getenv('DB_PASS') ?: (getenv('MYSQLPASSWORD') ?: 'root'));
define('DB_NAME', getenv('DB_NAME') ?: (getenv('MYSQLDATABASE') ?: 'news'));
define('DB_PORT', getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: 3306));


define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5242880);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_samesite' => 'Lax'
        // 'cookie_samesite' => 'Strict'
    ]);
}

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);

    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("خطأ في الاتصال بقاعدة البيانات");
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

function checkAuth() {
    if (!isset($_SESSION["authUser"])) {
        header("Location: login_ui.php");
        exit;
    }
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
