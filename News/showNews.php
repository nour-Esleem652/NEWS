<?php
require_once 'config.php';
// checkAuth();

$connetion = getDBConnection();

// حذف خبر (نقل للمحذوفات)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    $stmt = $connetion->prepare("UPDATE news SET type = 'deleted' WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "تم نقل الخبر إلى المحذوفات!";
    } else {
        $_SESSION['error_msg'] = "فشل في حذف الخبر!";
    }
    $stmt->close();
    
    header("Location: showNews.php");
    exit;
}

// جلب الأخبار النشطة
$sql = "SELECT news.*, categories.category_name, users.name as user_name 
        FROM news 
        LEFT JOIN categories ON news.category_id = categories.id 
        LEFT JOIN users ON news.user_id = users.id 
        WHERE news.type = 'active' 
        ORDER BY news.created_at DESC";

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
    <title>عرض الأخبار</title>
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
        .news-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
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
        <h2>جميع الأخبار النشطة</h2>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo sanitizeInput($success); ?></div>
        <?php endif; ?>
        
        <a href="creatPage_news.php" class="add-btn">+ إضافة خبر جديد</a>
        
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
                        <td><?php echo sanitizeInput($row['category_name']); ?></td>
                        <td><?php echo sanitizeInput($row['user_name']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="?delete_id=<?php echo $row['id']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('هل تريد نقل هذا الخبر للمحذوفات؟')">
                                حذف
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center; padding:40px; color:#999;">لا توجد أخبار حالياً</p>
        <?php endif; ?>
        
        <a href="dashboardUi.php" class="back-link">← العودة للوحة التحكم</a>
    </div>
</body>
</html>

<?php $connetion->close(); ?>