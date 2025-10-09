<?php
require_once 'config.php';
// checkAuth();

// $name = $_SESSION["authUser"]["name"];
$connetion = getDBConnection();

// إحصائيات سريعة
$stats = [];

// عدد الفئات
$result = $connetion->query("SELECT COUNT(*) as count FROM categories");
$stats['categories'] = $result->fetch_assoc()['count'];

// عدد الأخبار النشطة
$result = $connetion->query("SELECT COUNT(*) as count FROM news WHERE type = 'active'");
$stats['active_news'] = $result->fetch_assoc()['count'];

// عدد الأخبار المحذوفة
$result = $connetion->query("SELECT COUNT(*) as count FROM news WHERE type = 'deleted'");
$stats['deleted_news'] = $result->fetch_assoc()['count'];

// عدد المستخدمين
$result = $connetion->query("SELECT COUNT(*) as count FROM users");
$stats['users'] = $result->fetch_assoc()['count'];

$connetion->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 18px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .stat-card .label {
            color: #666;
            font-size: 16px;
        }
        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .menu-item {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .menu-item .icon {
            font-size: 50px;
            margin-bottom: 15px;
        }
        .menu-item .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .menu-item .desc {
            color: #999;
            font-size: 14px;
        }
        .logout {
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-top: 30px;
            text-align: center;
        }
        .logout a {
            color: #f44336;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }
        .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>مرحباً ! 👋</h1>
            <p>لوحة التحكم - إدارة الأخبار</p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="icon">📁</div>
                <div class="number"><?php echo $stats['categories']; ?></div>
                <div class="label">فئة</div>
            </div>
            <div class="stat-card">
                <div class="icon">📰</div>
                <div class="number"><?php echo $stats['active_news']; ?></div>
                <div class="label">خبر نشط</div>
            </div>
            <div class="stat-card">
                <div class="icon">🗑️</div>
                <div class="number"><?php echo $stats['deleted_news']; ?></div>
                <div class="label">خبر محذوف</div>
            </div>
            <div class="stat-card">
                <div class="icon">👥</div>
                <div class="number"><?php echo $stats['users']; ?></div>
                <div class="label">مستخدم</div>
            </div>
        </div>
        
        <div class="menu">
            <a href="Add_category.php" class="menu-item">
                <div class="icon">➕</div>
                <div class="title">إضافة فئة</div>
                <div class="desc">أضف فئة جديدة للأخبار</div>
            </a>
            
            <a href="showCategory.php" class="menu-item">
                <div class="icon">📋</div>
                <div class="title">عرض الفئات</div>
                <div class="desc">شاهد وأدر جميع الفئات</div>
            </a>
            
            <a href="creatPage_news.php" class="menu-item">
                <div class="icon">✍️</div>
                <div class="title">إضافة خبر</div>
                <div class="desc">انشر خبر جديد</div>
            </a>
            
            <a href="showNews.php" class="menu-item">
                <div class="icon">📄</div>
                <div class="title">عرض الأخبار</div>
                <div class="desc">شاهد الأخبار النشطة</div>
            </a>
            
            <a href="deletedNews.php" class="menu-item">
                <div class="icon">🗑️</div>
                <div class="title">الأخبار المحذوفة</div>
                <div class="desc">سلة المحذوفات</div>
            </a>
        </div>
        
        <div class="logout">
            <a href="logout.php">🚪 تسجيل الخروج</a>
        </div>
    </div>
</body>
</html>


<?php
// session_start();


$_SESSION = array();


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

}


session_destroy();



exit;
?>