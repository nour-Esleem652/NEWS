<?php
require_once 'config.php';
// checkAuth();

$connetion = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {

    // CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error_msg'] = "CSRF token غير صحيح";
        header("Location: Add_category.php");
        exit;
    }

    $category_name = trim($_POST['category_name'] ?? '');

    if ($category_name === '') {
        $_SESSION['error_msg'] = "اسم الفئة مطلوب";
        header("Location: Add_category.php");
        exit;
    }

    // تعبئة العمودين (لأن جدولك فيه name مطلوب)
    $stmt = $connetion->prepare("INSERT INTO categories (name, category_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $category_name, $category_name);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "تمت إضافة الفئة بنجاح";
        header("Location: showCategory.php");
        exit;
    } else {
        $_SESSION['error_msg'] = "فشل إضافة الفئة: " . $stmt->error;
        header("Location: Add_category.php");
        exit;
    }
}
header("Location: Add_category.php");
exit;

