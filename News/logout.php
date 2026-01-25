<?php
session_start();

// فضّي السيشن
$_SESSION = [];

// احذف كوكي السيشن
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// رجّع لصفحة اللوجن
header("Location: login_ui.php");
exit;

?>