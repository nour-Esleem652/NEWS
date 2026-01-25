<?php
require_once 'config.php';
// checkAuth();

$connetion = getDBConnection();

// حذف فئة بشكل آمن
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // التحقق من عدم وجود أخبار مرتبطة بهذه الفئة
    $check_stmt = $connetion->prepare("SELECT COUNT(*) as count FROM news WHERE category_id = ?");
    $check_stmt->bind_param("i", $delete_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_data = $check_result->fetch_assoc();

    if ($check_data['count'] > 0) {
        $_SESSION['error_msg'] = "لا يمكن حذف هذه الفئة لأن هناك أخبار مرتبطة بها!";
    } else {
        $stmt = $connetion->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "تم حذف الفئة بنجاح!";
        } else {
            $_SESSION['error_msg'] = "فشل في حذف الفئة!";
        }
        $stmt->close();
    }

    $check_stmt->close();
    header("Location: showCategory.php");
    exit;
}

// جلب جميع الفئات
$result = $connetion->query("SELECT id, category_name FROM categories ORDER BY id DESC");

$success = $_SESSION['success_msg'] ?? '';
$error = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض الفئات</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .add-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>جميع الفئات</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo sanitizeInput($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo sanitizeInput($success); ?></div>
        <?php endif; ?>
        
        <a href="Add_category.php" class="add-btn">+ إضافة فئة جديدة</a>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الفئة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 1;
                    while ($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo sanitizeInput($row['category_name']); ?></td>
                        <td>
                            <a href="showCategory.php?delete_id=<?php echo $row['id']; ?>" 

                                class="delete-btn" 
                                onclick="return confirm('هل أنت متأكد من حذف هذه الفئة؟')">
                                حذف
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center; padding:40px; color:#999;">
                لا توجد فئات حالياً. قم بإضافة فئة جديدة!
            </p>
        <?php endif; ?>
        
        <a href="dashboardUi.php" class="back-link">← العودة للوحة التحكم</a>
    </div>
</body>
</html>

<?php $connetion->close(); ?>