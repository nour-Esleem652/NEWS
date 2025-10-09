<?php
session_start();

// التحقق من تسجيل الدخول
// if(!isset($_SESSION["authUser"])){
//     header("Location:login_ui.php");
//     exit;
// }

include "connetionOnDatabase.php";
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة خبر</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 700px;
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
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        input[type="file"] {
            margin-bottom: 20px;
        }
        .radio-group {
            margin-bottom: 20px;
        }
        .radio-group label {
            display: inline-block;
            margin-right: 20px;
            font-weight: normal;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>إضافة خبر جديد</h2>
        
        <form action="createPage_news_logic.php" method="POST" enctype="multipart/form-data">   
            
            <label>عنوان الخبر:</label>
            <input type="text" name="title" placeholder="أدخل عنوان الخبر" required>
            
            <label>تفاصيل الخبر:</label>
            <textarea name="details" placeholder="أدخل تفاصيل الخبر" required></textarea>
            
            <label>الفئة:</label>
            <select name="category_id" required>
                <option value="">-- اختر الفئة --</option>
                <?php
                $result = $connetion->query("SELECT id, category_name FROM categories");
                if($result && $result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        echo "<option value='{$row['id']}'>{$row['category_name']}</option>";
                    }
                } else {
                    echo "<option value=''>لا توجد فئات متاحة</option>";
                }
                ?>
            </select>
            
            <label>صورة الخبر:</label>
            <input type="file" name="image" accept="image/*">
            
            <div class="radio-group">
                <label>حالة الخبر:</label>
                <label>
                    <input type="radio" name="type" value="active" checked> نشط
                </label>
                <label>
                    <input type="radio" name="type" value="deleted"> محذوف
                </label>
            </div>
            
            <input type="submit" name="save_news" value="حفظ الخبر">
        </form>
        
        <a href="dashboardUi.php" class="back-link">← العودة للوحة التحكم</a>
    </div>
</body>
</html>