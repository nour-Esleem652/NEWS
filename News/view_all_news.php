<?php
include "connectionDatabase.php";

$sql = "SELECT news.id, news.title, news.content, news.created_at, categories.name AS category_name, users.username AS author
        FROM news
        JOIN categories ON news.category_id = categories.id
        JOIN users ON news.user_id = users.id
        WHERE news.status = 'active'";
         // لو بتستخدمي حذف منطقي

$result = mysqli_query($connection, $sql);
?>

<h2>جميع الأخبار</h2>
<table border="1">
    <tr>
        <th>Title</th>
        <th>Details</th>
        <th>category_name</th>
        <th>Author</th>
        <th>تاريخ النشر</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <tD>
            <?php echo $row['title']; ?>
    </tD>
        <td>
            <?php echo $row['details']; ?>
    </td>
        <td>
            <?php echo $row['category_name']; ?>
    </td>
        <td>
            <?php echo $row['author']; ?>
    </td>
        <td>
            <?php echo $row['created_at']; ?>
    </td>
    </tr>
    <?php
 } 
    ?>
</table>