<?php
require_once 'config.php';
// checkAuth();

$connetion = getDBConnection();

$user_id = $_SESSION["authUser"]["id"] ?? 0;
if ($user_id <= 0) {
    header("Location: login_ui.php");
    exit;
}

// استرجاع الأخطاء والبيانات من الجلسة
$errors = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["save_news"])) {

    // (اختياري) تحقق CSRF
    // if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    //     die("Invalid CSRF token");
    // }

    $title       = trim($_POST["title"] ?? '');
    $content     = trim($_POST["content"] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $type        = $_POST["type"] ?? 'active';

    $errors = [];

    if ($title === '') {
        $errors[] = "عنوان الخبر مطلوب";
    }

    if ($content === '') {
        $errors[] = "تفاصيل الخبر مطلوبة";
    }

    if ($category_id <= 0) {
        $errors[] = "يجب اختيار فئة صحيحة";
    }

    if (!in_array($type, ['active', 'deleted'])) {
        $errors[] = "حالة الخبر غير صحيحة";
    }

    // رفع الصورة (اختياري)
    $image_name = null;

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

        $file_size      = $_FILES["image"]["size"];
        $file_tmp       = $_FILES["image"]["tmp_name"];
        $file_name      = $_FILES["image"]["name"];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_size > MAX_FILE_SIZE) {
            $errors[] = "حجم الصورة يجب أن يكون أقل من 5 ميجابايت";
        }

        if (!in_array($file_extension, ALLOWED_EXTENSIONS)) {
            $errors[] = "صيغة الصورة غير مدعومة! استخدم JPG, JPEG, PNG, GIF فقط";
        }

        $check = getimagesize($file_tmp);
        if ($check === false) {
            $errors[] = "الملف المرفوع ليس صورة صحيحة";
        }

        if (empty($errors)) {
            if (!file_exists(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }

            $image_name  = uniqid() . "_" . time() . "." . $file_extension;
            $target_file = UPLOAD_DIR . $image_name;

            if (!move_uploaded_file($file_tmp, $target_file)) {
                $errors[] = "فشل في رفع الصورة";
                $image_name = null;
            }
        }
    }

    // حفظ الخبر
    if (empty($errors)) {

        $stmt = $connetion->prepare(
            "INSERT INTO news (title, content, image, category_id, user_id, type)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        // title s, content s, image s (nullable), category_id i, user_id i, type s
        $stmt->bind_param("sss i i s", $title, $content, $image_name, $category_id, $user_id, $type);
        // ملاحظة: بعض بيئات PHP تتحسس من المسافات داخل string النوع
        // إذا واجهت مشكلة، استخدمي السطر التالي بدل اللي فوق:
        // $stmt->bind_param("sssiis", $title, $content, $image_name, $category_id, $user_id, $type);

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "تم إضافة الخبر بنجاح!";
            header("Location: showNews.php");
            exit;
        } else {
            $errors[] = "فشل في إضافة الخبر: " . $connetion->error;

            if (!empty($image_name) && file_exists(UPLOAD_DIR . $image_name)) {
                unlink(UPLOAD_DIR . $image_name);
            }
        }

        $stmt->close();
    }

    // لو في أخطاء: نخزنها ونرجّعها للفورم
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: creatPage_news_logic.php");
        exit;
    }
}

$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة خبر</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h2 { text-align: center; color: #333; margin-bottom: 30px; font-size: 28px; }
        .errors {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .errors ul { list-style-position: inside; margin-right: 10px; }
        .errors li { margin-bottom: 5px; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: bold; font-size: 15px; }
        label .required { color: #f44336; }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, textarea:focus, select:focus { outline: none; border-color: #667eea; }
        textarea { min-height: 150px; resize: vertical; }
        input[type="file"] {
            padding: 10px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
        }
        .radio-group { display: flex; gap: 30px; margin-top: 10px; }
        .radio-option { display: flex; align-items: center; gap: 8px; }
        input[type="submit"] {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.2s;
            margin-top: 10px;
        }
        input[type="submit"]:hover { transform: translateY(-2px); }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover { text-decoration: underline; }
        .file-info { font-size: 12px; color: #777; margin-top: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h2>📰 إضافة خبر جديد</h2>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <strong>⚠️ يرجى تصحيح الأخطاء التالية:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo sanitizeInput($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="creatPage_news_logic.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="form-group">
            <label>عنوان الخبر: <span class="required">*</span></label>
            <input type="text"
                   name="title"
                   placeholder="أدخل عنوان الخبر"
                   value="<?php echo sanitizeInput($form_data['title'] ?? ''); ?>"
                   required
                   maxlength="255">
        </div>

        <div class="form-group">
            <label>تفاصيل الخبر: <span class="required">*</span></label>
            <textarea name="content"
                      placeholder="أدخل تفاصيل الخبر الكاملة..."
                      required><?php echo sanitizeInput($form_data['content'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>الفئة: <span class="required">*</span></label>
            <select name="category_id" required>
                <option value="">-- اختر الفئة --</option>
                <?php
                $result = $connetion->query("SELECT id, category_name FROM categories ORDER BY category_name");
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $selected = (!empty($form_data['category_id']) && $form_data['category_id'] == $row['id']) ? 'selected' : '';
                        echo "<option value='{$row['id']}' {$selected}>" . sanitizeInput($row['category_name']) . "</option>";
                    }
                } else {
                    echo "<option value='' disabled>لا توجد فئات متاحة</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>صورة الخبر:</label>
            <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/gif">
            <div class="file-info">الحد الأقصى لحجم الصورة: 5 ميجابايت | الصيغ المدعومة: JPG, PNG, GIF</div>
        </div>

        <div class="form-group">
            <label>حالة الخبر: <span class="required">*</span></label>
            <div class="radio-group">
                <div class="radio-option">
                    <input type="radio"
                           name="type"
                           value="active"
                           id="active"
                        <?php echo (!isset($form_data['type']) || $form_data['type'] === 'active') ? 'checked' : ''; ?>>
                    <label for="active">✅ نشط</label>
                </div>

                <div class="radio-option">
                    <input type="radio"
                           name="type"
                           value="deleted"
                           id="deleted"
                        <?php echo (isset($form_data['type']) && $form_data['type'] === 'deleted') ? 'checked' : ''; ?>>
                    <label for="deleted">🗑️ محذوف</label>
                </div>
            </div>
        </div>

        <input type="submit" name="save_news" value="💾 حفظ الخبر">
    </form>

    <a href="dashboardUi.php" class="back-link">← العودة للوحة التحكم</a>
</div>
</body>
</html>

<?php $connetion->close(); ?>
