<?php
require_once 'config.php';

$connetion = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["login"])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    
    // التحقق من المدخلات
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "الاسم مطلوب";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "البريد الإلكتروني غير صحيح";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "كلمة المرور يجب أن تكون 6 أحرف على الأقل";
    }
    
    if (empty($errors)) {
        // التحقق من عدم وجود البريد مسبقاً
        $check_stmt = $connetion->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['error_msg'] = "البريد الإلكتروني مسجل مسبقاً";
            header("Location: login_user.php");
            exit;
        }
        $check_stmt->close();
        
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $connetion->prepare("INSERT INTO users(name, email, password) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول";
            header("Location: login_ui.php");
            exit;
        } else {
            $_SESSION['error_msg'] = "فشل في إنشاء الحساب";
        }
        
        $stmt->close();
    } else {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_data'] = $_POST;
    }
    
    header("Location:login_ui.php");
    exit;
}

$connetion->close();
?>