<?php
require_once 'config.php';

$connetion = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    
    if (empty($email) || empty($password)) {
        $_SESSION['error_msg'] = "الرجاء إدخال البريد الإلكتروني وكلمة المرور";
        header("Location: login_ui.php");
        exit;
    }
    

    $stmt = $connetion->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user["password"])) {
            session_regenerate_id(true); 
            $_SESSION["authUser"] = $user;
            
            header("Location: dashboardUi.php");
            exit;
        } else {
            $_SESSION['error_msg'] = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
        }
    } else {
        $_SESSION['error_msg'] = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
    }
    
    $stmt->close();
    header("Location: dashboardUi.php");
    exit;
}

$connetion->close();
?>