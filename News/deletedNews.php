<?php
require_once 'config.php';
checkAuth();

$connetion = getDBConnection();

// استعادة خبر محذوف
if (isset($_GET['restore_id'])) {
    $restore_id = intval($_GET['restore_id']);
    
    $stmt = $connetion->prepare("UPDATE news SET type = 'active' WHERE id = ?");
    $stmt->bind_param("i", $restore_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "تم استعادة الخبر بنجاح!";
    } else {
        $_SESSION['error_msg'] = "فشل في استعادة الخبر!";
    }
    $stmt->close();
    
    header("Location: deletedNews.php");
    exit;
}

// حذف نهائي
if (isset($_GET['permanent_delete'])) {
    $delete_id = intval($_GET['permanent_delete']);
    
    // جلب اسم الصورة قبل الحذف
    $stmt = $connetion->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_name = $row['image'];
        
        // حذف السجل من قاعدة البيانات
        $delete_stmt = $connetion->prepare("DELETE FROM news WHERE id = ?");
        $delete_stmt->bind_param("i", $delete_id);
        
        if ($delete_stmt->execute()) {
            // حذف الصورة من المجلد
            if (!empty($image_name) && file_exists('uploads/' . $image_name)) {
                unlink('uploads/' . $image_name);
            }
            $_SESSION['success_msg'] = "تم حذف الخبر نهائياً!";
        } else {
            $_SESSION['error_msg'] = "فشل في حذف الخبر!";
        }
        $delete_stmt->close();
    }
    $stmt->close();
    
    header("Location: deletedNews.php");
    exit;
}

// جلب الأخبار المحذوفة
$sql = "SELECT news.*, categories.category_name, users.name as user_name 
        FROM news 
        LEFT JOIN categories ON news.category_id = categories.id 
        LEFT JOIN users ON news.user_id = users.id 
        WHERE news.type = 'deleted' 
        ORDER BY news.updated_at DESC";

$result = $connetion->query($sql);

$success = $_SESSION['success_msg'] ?? '';
$error = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الأخبار المحذوفة</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
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
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            background-color: #f44336;
            color: white;
        }
        table tr:hover {
            background-color: #f9f9f9;
        }
        .news-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .restore-btn {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 5px;
            display: inline-block;
        }
        .restore-btn:hover {
            background-color: #45a049;
        }
        .delete-btn {
            background-color: #9e0000;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
        .delete-btn:hover {
            background-color: #7a0000;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            color: #333;
        }
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🗑️ الأخبار المحذوفة</h2>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo sanitizeInput($success); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo sanitizeInput($error); ?></div>
        <?php endif; ?>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>العنوان</th>
                        <th>الفئة</th>
                        <th>الكاتب</th>
                        <th>التاريخ</th>
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
                        <td>
                            <?php if (!empty($row['image']) && file_exists('uploads/' . $row['image'])): ?>
                                <img src="uploads/<?php echo sanitizeInput($row['image']); ?>" 
                                     class="news-image" 
                                     alt="صورة الخبر">
                            <?php else: ?>
                                لا توجد صورة
                            <?php endif; ?>
                        </td>
                        <td><?php echo sanitizeInput($row['title']); ?></td>
                        <td><?php echo sanitizeInput($row['category_name'] ?? 'غير محدد'); ?></td>
                        <td><?php echo sanitizeInput($row['user_name'] ?? 'غير معروف'); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['updated_at'])); ?></td>
                        <td>
                            <a href="?restore_id=<?php echo $row['id']; ?>" 
                               class="restore-btn" 
                               onclick="return confirm('هل تريد استعادة هذا الخبر؟')">
                                ↩️ استعادة
                            </a>
                            <a href="?permanent_delete=<?php echo $row['id']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('⚠️ تحذير!\n\nسيتم حذف الخبر نهائياً ولن يمكن استعادته.\n\nهل أنت متأكد؟')">
                                ❌ حذف نهائي
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>✨ لا توجد أخبار محذوفة</p>
                <p style="font-size: 14px; margin-top: 10px;">سلة المحذوفات فارغة</p>
            </div>
        <?php endif; ?>
        
        <a href="dashboardUi.php" class="back-link">← العودة للوحة التحكم</a>
    </div>
</body>
</html>

<?php $connetion->close(); ?>